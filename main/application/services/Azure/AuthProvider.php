<?php

use Application_Model_Entity_System_UserRoles as UserRoles;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException as IdentityProviderExceptionAlias;
use TheNetworg\OAuth2\Client\Provider\Azure;
use TheNetworg\OAuth2\Client\Token\AccessToken;

class Application_Service_Azure_AuthProvider
{
    private Azure $provider;
    private string $adGroupName;
    private string $resourceId;
    private string $scopeSso;
    private string $scopeGroupRead;

    private const LIST_AD_ROLES = [
        UserRoles::SUPER_ADMIN_ROLE_ID => 'super_admin',
        UserRoles::ADMIN_ROLE_ID => 'admin',
        UserRoles::MANAGER_ROLE_ID => 'settlements_manager',
        UserRoles::SPECIALIST_ROLE_ID => 'settlements_specialist',
        UserRoles::ONBOARDING_ROLE_ID => 'settlements_onboarding_specialist',
    ];
    private const LIST_AD_DIVISIONS = [
        'division_linehaul',
        'division_pud_ics',
        'division_intermodal',
    ];

    public function __construct()
    {
        $options = Zend_Registry::getInstance()->options['azure']['sso'];
        $this->adGroupName = $options['adGroupName'];
        $this->resourceId = $options['resourceId'];
        $this->scopeSso = $options['scopeSso'];
        $this->scopeGroupRead = $options['scopeGroupRead'];
        $this->provider = new Azure([
            'clientId' => $options['clientId'],
            'clientSecret' => $options['clientSecret'],
            'redirectUri' => $options['redirectUri'],
            'scopes' => [$this->scopeSso],
            'defaultEndPointVersion' => '2.0',
        ]);
    }

    public function getProvider(): Azure
    {
        return $this->provider;
    }

    public function getScopeGroupRead(): string
    {
        return $this->scopeGroupRead;
    }

    public function getUserGroups(AccessToken $token): array
    {
        return $this->provider->get(
            $this->provider->getRootMicrosoftGraphUri($token) . '/v1.0/me/memberOf',
            $token
        );
    }

    public function getUserRoles(AccessToken $token): array
    {
        return $this->provider->get(
            $this->provider->getRootMicrosoftGraphUri($token) . '/v1.0/me/appRoleAssignments?$filter=resourceId+eq+'
            . $this->resourceId,
            $token
        );
    }

    public function getAppRoles(AccessToken $token): array
    {
        return $this->provider->get(
            $this->provider->getRootMicrosoftGraphUri($token) . '/v1.0/servicePrincipals/'
            . $this->resourceId . '/appRoles',
            $token
        );
    }

    public function getArrayAppRoles(AccessToken $token): array
    {
        $roles = $this->getAppRoles($token);

        $result = [];
        foreach ($roles as $role) {
            $result[$role['id']] = $role;
        }

        return $result;
    }

    /**
     * @throws IdentityProviderExceptionAlias
     * @throws JsonException
     */
    public function getUserAdData(string $authorizationCode): array
    {
        $provider = $this->getProvider();
        /** @var AccessToken $token */
        $token = $provider->getAccessToken('authorization_code', [
            'scope' => $provider->scope,
            'code' => $authorizationCode,
        ]);
        $userAdInfo = $provider->validateAccessToken($token->getToken());

        /** @var AccessToken $newToken */
        $newToken = $provider->getAccessToken('refresh_token', [
            'scope' => $this->getScopeGroupRead(),
            'refresh_token' => $token->getRefreshToken(),
        ]);
        $userAdRoles = $this->getUserRoles($newToken);
        $this->getLogger()->info('Sso found roles info: ' . json_encode($userAdRoles, JSON_THROW_ON_ERROR));

        $appRoles = $this->getArrayAppRoles($newToken);
        $this->getLogger()->info('Sso app roles info: ' . json_encode($appRoles, JSON_THROW_ON_ERROR));

        $email = strtolower($userAdInfo['preferred_username'] ?? '');
        $this->getLogger()->info('Sso start matching roles for user: ' . $email);
        $result = [
            'email' => $email,
            'name' => $userAdInfo['name'],
            'sub' => $userAdInfo['sub'],
            'divisions' => [],
            'roleId' => UserRoles::GUEST_ROLE_ID,
        ];
        foreach ($userAdRoles as $userRoleInfo) {
            $appRoleInfo = $appRoles[$userRoleInfo['appRoleId']] ?? null;
            if (!$appRoleInfo) {
                $this->getLogger()->alert('Sso in appRoles not found userRoleId: ' . $userRoleInfo['appRoleId']);
                continue;
            }
            $adRoleName = $appRoleInfo['value'] ?? null;
            if (!$adRoleName) {
                $this->getLogger()->alert('Sso in appRoleInfo not found value: ' . json_encode($appRoleInfo));
                continue;
            }
            $this->getLogger()->info('Sso match object: ' . $adRoleName);
            if ($roleId = array_search($adRoleName, self::LIST_AD_ROLES)) {
                if ($roleId < $result['roleId']) {
                    $result['roleId'] = $roleId;
                }
            } elseif (in_array($adRoleName, self::LIST_AD_DIVISIONS)) {
                $result['divisions'][] = mb_strcut((string) $adRoleName, 9);
            } else {
                $this->getLogger()->err('Sso not match: ' . $adRoleName);
            }
        }
        $this->getLogger()->info('Sso finish matching, info: ' . json_encode($result));

        return $result;
    }

    protected function getLogger(): Zend_Log
    {
        return Zend_Registry::get('logger');
    }
}
