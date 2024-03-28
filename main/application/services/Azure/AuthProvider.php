<?php

use Application_Model_Entity_System_UserRoles as UserRoles;
use TheNetworg\OAuth2\Client\Provider\Azure;
use TheNetworg\OAuth2\Client\Token\AccessToken;

class Application_Service_Azure_AuthProvider
{
    private Azure $provider;
    private string $adGroupName;
    private string $resourceId;
    private string $scopeSso;
    private string $scopeGroupRead;

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

    public function getUserAuthData(array $userRoles, array $appRoles, string $email): array
    {
        $this->getLogger()->info('Sso start matching roles for user: ' . $email);
        $this->getLogger()->info('Sso AD Group name: ' . $this->adGroupName);
        $result = [
            'email' => $email,
            'divisionCode' => '',
            'roleId' => UserRoles::GUEST_ROLE_ID,
        ];
        foreach ($this->getArrayMapAdRoles() as $adRoleName => $userRoleId) {
            $this->getLogger()->info('Sso match role: ' . $adRoleName);
            foreach ($userRoles as $userRoleInfo) {
                $appRoleInfo = $appRoles[$userRoleInfo['appRoleId']] ?? null;
                if (!$appRoleInfo) {
                    continue;
                }
                if ($appRoleInfo['value'] === $adRoleName) {
                    $result['roleId'] = $userRoleId;
                    if (UserRoles::CARRIER_ROLE_ID === $userRoleId) {
                        $result['divisionCode'] = mb_strcut($adRoleName, 9);
                    }
                    $result += $appRoleInfo;
                }
            }
        }
        $this->getLogger()->info('Sso finish matching, for user ' . $email . ' set roleId = ' . $result['roleId']);

        return $result;
    }

    private function getArrayMapAdRoles(): array
    {
        return [
            'division_linehaul' => UserRoles::CARRIER_ROLE_ID,
            'division_pud_ics' => UserRoles::CARRIER_ROLE_ID,
            'division_intermodal' => UserRoles::CARRIER_ROLE_ID,
            'admin' => UserRoles::MODERATOR_ROLE_ID,
            'super_admin' => UserRoles::SUPER_ADMIN_ROLE_ID,
        ];
    }

    protected function getLogger(): Zend_Log
    {
        return Zend_Registry::get('logger');
    }
}
