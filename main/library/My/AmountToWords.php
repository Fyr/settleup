<?php

class My_AmountToWords  {
    const MAJOR = 'Dollars';
    const MINOR = ' Cents';
    const POINT_WORD = ' and ';
    public static $magind;
    public static $units = array('','One','Two','Three','Four','Five','Six','Seven','Eight','Nine');
    public static $teens = array('Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen');
    public static $tens = array('','Ten','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety');
    public static $mag = array('','Thousand','Million','Billion','Trillion');

    public static function toWords($amount, $pointWord = self::POINT_WORD, $major=self::MAJOR, $minor=self::MINOR) {

        $number = number_format($amount,2);
        list($pounds,$pence) = explode('.',$number);
        $words = $major . $pointWord . $pence . $minor;
        if ($pounds==0) {
            $words = "Zero " . $words;
        } else {
            $groups = explode(',',$pounds);
            $groups = array_reverse($groups);
            for (self::$magind=0; self::$magind<count($groups); self::$magind++) {
                if ((self::$magind==1)&&(strpos($words,'Hundred') === false)&&($groups[0]!='000'))
                    $words = ' ' . $words;
                $words = self::_build($groups[self::$magind]).$words;
            }
        }
        return $words;
    }

    public static function _build($n) {
        $res = '';
        $na = str_pad("$n",3,"0",STR_PAD_LEFT);
        if ($na == '000') return '';
        if ($na{0} != 0)
            $res = ' '.self::$units[$na{0}] . ' Hundred';
        if (($na{1}=='0')&&($na{2}=='0'))
            return $res . ' ' . self::$mag[self::$magind];
//        $res .= $res==''? '' : ' and';
        $t = (int)$na{1}; $u = (int)$na{2};
        switch ($t) {
            case 0: $res .= ' ' . self::$units[$u]; break;
            case 1: $res .= ' ' . self::$teens[$u]; break;
            default:$res .= ' ' . self::$tens[$t] . ' ' . self::$units[$u] ; break;
        }
        $res .= ' ' . self::$mag[self::$magind];
        return $res;
    }
}