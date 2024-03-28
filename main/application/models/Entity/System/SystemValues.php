<?php

class Application_Model_Entity_System_SystemValues extends Application_Model_Base_Entity
{
    final public const NOT_DELETED_STATUS = 0;
    final public const DELETED_STATUS = 1;
    final public const NOT_CONFIGURED_STATUS = 0;
    final public const CONFIGURED_STATUS = 1;
    final public const GENDER_MALE = 1;
    final public const GENDER_FEMALE = 2;

    public static function getStates()
    {
        return [
            '-' => '-',
            'AL' => 'AL',
            'AK' => 'AK',
            'AZ' => 'AZ',
            'AR' => 'AR',
            'CA' => 'CA',
            'CO' => 'CO',
            'CT' => 'CT',
            'DE' => 'DE',
            'DC' => 'DC',
            'FL' => 'FL',
            'GA' => 'GA',
            'HI' => 'HI',
            'ID' => 'ID',
            'IL' => 'IL',
            'IN' => 'IN',
            'IA' => 'IA',
            'KS' => 'KS',
            'KY' => 'KY',
            'LA' => 'LA',
            'ME' => 'ME',
            'MD' => 'MD',
            'MA' => 'MA',
            'MI' => 'MI',
            'MN' => 'MN',
            'MS' => 'MS',
            'MO' => 'MO',
            'MT' => 'MT',
            'NE' => 'NE',
            'NV' => 'NV',
            'NH' => 'NH',
            'NJ' => 'NJ',
            'NM' => 'NM',
            'NY' => 'NY',
            'NC' => 'NC',
            'ND' => 'ND',
            'OH' => 'OH',
            'OK' => 'OK',
            'OR' => 'OR',
            'PA' => 'PA',
            'RI' => 'RI',
            'SC' => 'SC',
            'SD' => 'SD',
            'TN' => 'TN',
            'TX' => 'TX',
            'UT' => 'UT',
            'VT' => 'VT',
            'VA' => 'VA',
            'WA' => 'WA',
            'WV' => 'WV',
            'WI' => 'WI',
            'WY' => 'WY',
        ];
    }
}
