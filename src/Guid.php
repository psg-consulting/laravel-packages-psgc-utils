<?php
namespace PsgcLaravelPackages\Utils;

class Guid
{
    public static function create($useLongVersion=false)
    {
        if ($useLongVersion) {
            // github-style
            $uTime = microtime();
            list($timeUnitA, $timeUnitB) = explode(' ', $uTime);
    
            $hexA = dechex($timeUnitA * 1000000);
            $hexB = dechex($timeUnitB);
    
            self::checkLength($hexA, 5);
            self::checkLength($hexB, 6);
    
            $guid = '';
            $guid .= $hexA;
            $guid .= self::getRandomString(3);
            $guid .= '-';
            $guid .= self::getRandomString(4);
            $guid .= '-';
            $guid .= self::getRandomString(4);
            $guid .= '-';
            $guid .= self::getRandomString(4);
            $guid .= '-';
            $guid .= $hexB;
            $guid .= self::getRandomString(6);
        } else {
            // airline-style
            $guid = strtolower(self::getRandomAlphaNumString(6));
        }

        return $guid;
    }

    protected static function getRandomString($chars)
    {
        $str = '';
        for ($i = 0; $i < $chars; $i++) {
            $str .= dechex(mt_rand(0, 15));
        }

        return $str;
    }

    protected static function checkLength(&$str, $length)
    {
        $strlen = strlen($str);
        if ($strlen < $length) {
            $str = str_pad($str, $length, '0');
        } elseif ($strlen > $length) {
            $str = substr($str, 0, $length);
        }
    }

    public static function getRandomAlphaNumString($length) {
        $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $char = str_shuffle($char);
        for($i=0, $rand='', $l=strlen($char)-1; $i<$length; $i++) {
            $rand .= $char{mt_rand(0, $l)};
        }
        return $rand;
    }
}
