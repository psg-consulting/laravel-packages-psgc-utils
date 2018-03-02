<?php
namespace PsgcLaravelPackages\Utils;

class CurrencyHelpers
{
    public static function makeNiceCurrency($str,$option=null)
    {
        setlocale(LC_MONETARY, 'en_US');
        switch ($option) {
            case 'rounded-no-decimal':
                $str = money_format('%.0n',$str);
                break;
            case 'rounded-no-decimal-nosymbol':
                $str = money_format('%!.0n',$str);
                break;
            case 'stripe':
                $str = '$'.number_format($str/100, 2, '.',',');
                break;
            default:
                $str = '$'.number_format($str, 2, '.',',');
        }
        return $str;
    }

    // remove $, commas
    public static function normalize($str)
    {
        $str = str_replace( ',', '', $str );
        $str = str_replace( '$', '', $str );
        $str = str_replace( ' ', '', $str );
        return $str;
    }
        
        
}
