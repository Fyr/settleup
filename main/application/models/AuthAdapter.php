<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 9/23/14
 * Time: 4:20 PM
 */

class Application_Model_AuthAdapter extends Zend_Auth_Adapter_DbTable
{
    /**
     * _authenticateCreateSelect() - This method creates a Zend_Db_Select object that
     * is completely configured to be queried against the database.
     *
     * @return Zend_Db_Select
     */
    protected function _authenticateCreateSelect()
    {
        // build credential expression
        if (empty($this->_credentialTreatment) || (!str_contains($this->_credentialTreatment, '?'))) {
            $this->_credentialTreatment = '?';
        }

        $credentialExpression = new Zend_Db_Expr(
            '(CASE WHEN ' . $this->_zendDb->quoteInto(
                $this->_zendDb->quoteIdentifier($this->_credentialColumn, true) . ' = ' . $this->_credentialTreatment,
                $this->_credential
            ) . ' THEN 1 ELSE 0 END) AS ' . $this->_zendDb->quoteIdentifier(
                $this->_zendDb->foldCase('zend_auth_credential_match')
            )
        );

        // get select
        $dbSelect = clone $this->getDbSelect();
        $dbSelect->from($this->_tableName, ['*', $credentialExpression])->where(
            $this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?',
            $this->_identity
        )->joinLeft(['e' => 'entity'], $this->_tableName . '.' . 'entity_id = e.id')->where(
            $this->_zendDb->quoteIdentifier('e.deleted', true) . '= 0 OR ' . $this->_zendDb->quoteIdentifier(
                'role_id',
                true
            ) . ' IN (' . Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID . ', ' . Application_Model_Entity_System_UserRoles::MODERATOR_ROLE_ID . ')'
        );

        return $dbSelect;
    }
}
