<?php
namespace Codeception\Module;
use \AcceptanceTester;

class System_Data
{
    public static $system = 'System';
    public static $system_Dropdown_Menu = '.dropdown-toggle';
    public static $system_Menu = '//div[3]//li[10]/a';
    public static $system_Users_Submenu = '//div[3]//li[10]//li[1]/a';
    public static $users = 'Users';
    public static $users_Page_URL = '/users_index';
    public static $create_User_Page_URL = '/users_index/new';
    public static $states = 'States';
    public static $states_Page_URL = '/freeze';
    public static $users_header_Table = '#updateheader-Application_Model_Entity_Accounts_User th';
    public static $users_header_Table_Original = array('', 'ID', 'User Name', 'User Email', 'User Type', 'Company', 'Action');
    public static $usersTable=array("","ID","User Name","User Email","User Type","Company","Action");


    public static $customer_Filter_Button = 'div.btn.btnFilter';
    public static $filter_Name_Button = 'Filter';
    public static $customer_Clear_Button = 'div.btn.btnClearFilter';
    public static $Clear_Name_Button = 'Clear';

    public static $delete_Selected_Users_Button = 'a.btn.btn-danger.confirm-delete.btn-multiaction';
    public static $delete_Selected_Users_Name_Button = 'Delete Selected';
    public static $add_User_Button = 'div.row.right a.btn.btn-success';
    public static $add_User_Name_Button = 'Add User';

}