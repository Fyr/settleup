<?php

/**
 * class for unit tests
 */
final class Application_Model_Base_Array
{
    /**
     * @static
     * return $_POST array as
     *  'key' =>'value',
     *  'key' =>'value',
     * @return string
     */
    public static function getPost()
    {
        $html = '';
        foreach ($_POST as $k => $v) {
            $html .= "'$k' => '$v',<br>";
        }

        return $html;
    }
}
