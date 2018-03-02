<?php
namespace PsgcLaravelPackages\Utils;

/*
Example Usage

    abstract class FooEnum extends \App\Libs\BaseEnum {
    
        const HOTEL    = 1;
        const FOXTROT  = 2;
    
        public static $keymap = [
            self::HOTEL=>['slug'=>'hotel','title'=>'Hotel'], 
            self::FOXTROT=>['slug'=>'foxtrot','title'=>'Foxtrot'],
        ];
    }

alternately, we can drop the 'slug', and just have a simple array map with key => title:

    abstract class FooEnum extends \App\Libs\BaseEnum {

        const HOTEL    = 1;
        const FOXTROT  = 2;
    
        public static $keymap = [
            self::HOTEL=>'Hotel', 
            self::FOXTROT=>'Foxtrot',
        ];
    }
*/
abstract class BaseEnum {

    public static $keymap;

    // convert to string
    public static function stringify($key) {
        if (!key_exists($key, static::$keymap)) {
            return null;
        }
        $str = !empty(static::$keymap[$key]['title']) ? static::$keymap[$key]['title'] : static::$keymap[$key];
        return $str;
    }

    public static function render($key) {
        return self::stringify($key);
    }

    public static function description($key) {
        return empty(static::$keymap[$key]['description']) ? 'N/A' : static::$keymap[$key]['description'];
    }

    public static function slugify($key) {
        if (!key_exists($key, static::$keymap)) {
            return null;
        }
        return static::$keymap[$key]['slug'];
    }

    public static function isValid($key) {
        return key_exists($key, static::$keymap);
    }

    public static function getKeymap() {
        return static::$keymap;
    }

    // index the key map by slug instead of integer index
    public static function getKeymapBySlug() {
        $hash = [];
        foreach ( static::$keymap as $i => $o ) {
            $o['index'] = $i;
            $hash[$o['slug']] = $o;
            unset($hash[$o['slug']]['slug']);
        }
        return $hash;
    }

    public static function getKeys() {
        return array_keys(static::$keymap);
    }

    public static function isKeyValid($key) {
        $is = array_key_exists($key,static::$keymap);
        return $is;
    }

    public static function findKeyBySlug($slug) {
        foreach (static::$keymap as $k => $o) {
            if ( !empty($o['slug']) && ( strtolower($slug)==strtolower($o['slug']) ) ) {
                return $k;
            }
        }
        return null;
    }

    // Array for a select dropdown form element
    public static function getSelectOptions($includeBlank=false)
    {
        $list = [];
        if ($includeBlank) {
            $list[''] = '';
        }
        foreach (static::$keymap as $k => $o) {
            $list[$k] = !empty($o['title']) ? $o['title'] : $o;
        }
        return $list;
    }

}
