<?php
namespace PsgcLaravelPackages\Utils;

class Helpers
{

    // is the string json-formatted?
    public function isJson(string $string) : bool
    {
        $string = trim($string);
    
        if (    empty($string)  
            || ( "{" != $string[0] )  
            || ( "}" != substr($string,-1) )
        ) 
        { 
            return false;
        }
    
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function trueempty($var) {
        return !($var==="0"||$var);
    }

    // Select Options for fields such as is_complete, is_valid, is_{something}, etc
    // yes or no
    public static function getBinarySelectOptions($semantics=[])
    {
        $list = [];
        $list[''] = '';
        if ( !empty($semantics) ) {
            // Override default display in select dropdown that corresponds to yes or no
            // Server will process yes or no (array key)
            $list['no'] = $semantics['no'];
            $list['yes'] = $semantics['yes'];
        } else {
            $list['no'] = 'No';
            $list['yes'] = 'Yes';
        }
        return $list;
    }


    public static function getHourSelectOptions($is24Period = 1)
    {
        $options = [];
        if ($is24Period) {
            for ($i = 0; $i <= 23; $i++) {
                $options[$i] = sprintf('%02d', $i);
            }
        } else {
            // am/pm periods
            foreach ([12, 1, 2, 3, 4, 5, 6, 7, 9, 10, 11] as $h) {
                $options[$h] = sprintf('%02d', $h);
            }
        }

        return $options;
    }
    public static function getMinuteSelectOptions()
    {
        $options = [];
        for ($i = 0; $i <= 59; $i++) {
            $options[$i] = sprintf('%02d', $i);
        }

        return $options;
    }

    public static function _formval($obj, $field)
    {
        if (empty($obj)) {
            return;
        }
        /*
        if (empty($obj->{$field})) {
            throw new \Exception('field not found: '.$field);
            return null;
        }
         */
        $val = $obj->{$field};

        return empty($val) ? null : $val;
    }

    public static function currentMonthNum()
    {
        return date('m');
    }
    public static function currentYear()
    {
        return date('Y');
    }

    public static function fillUrlFromDomain($domain)
    {
        $url = 'http://www.'.$domain;

        return $url;
    }
    public static function parseDomainFromURL($url)
    {
        $urlParts = parse_url($url);
        $domain = preg_replace('/^www\./', '', $urlParts['host']); // remove www
        return $domain;
    }

    public static function getLaravelEnv()
    {
        $laravelEnv = null;

        if (!empty($_SERVER['SERVER_ADDR']) && ($_SERVER['SERVER_ADDR'] == '127.0.0.1')) {
            $laravelEnv = 'local';
        } elseif (!empty($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'www.dev-clssfy.com')) {
            $laravelEnv = 'local';
        } elseif (
            !empty($_SERVER['HTTP_HOST']) &&
            (($_SERVER['HTTP_HOST'] == 'www.clssfy.com') || ($_SERVER['HTTP_HOST'] == 'staging.clssfy.com'))
        ) {
            $laravelEnv = 'production';
        }

        return $laravelEnv;
    }

    // like link_to_route, but specific to dashboard and distinguishes client vs agent
    public static function linkToDashboard($user, $title = null, $classes = [])
    {
        if ($user->hasRole('client')) {
            return link_to_route('site.client.dashboard', $title, $user->id, ['class' => implode(' ', $classes)]);
        } elseif ($user->hasRole('bondagent')) {
            return link_to_route('site.bondsman.dashboard', $title, $user->id, ['class' => implode(' ', $classes)]);
        } else {
            return link_to_route('site.home');
        }
    }

    public static function mixpanelIgnore()
    {
        $ignore = 0;
        if (\Auth::guest()) {
            return $ignore; // this will not ignore them
        }
        $serverName = \Request::server('SERVER_NAME');
        switch ($serverName) {
            case 'www.dev-permits.com':
                $ignore = 1;
                break;
        }

        $user = \Auth::user();
        switch ($user->email) {
            case 'peter@peltronic.com':
                $ignore = 1;
                break;
        }

        return $ignore;
    }

    public static function getJSRoutes()
    {
        $routes = [];
        $routeCollection = \Route::getRoutes();
        //$rc2 = $routeCollection->getRoutes();
        foreach ($routeCollection as $value) {
            $methods = $value->methods();
            foreach ($methods as $m) {
                if ($m != 'HEAD') {
                    $routes[$m.'.'.$value->getName()] = '/'.$value->uri();
                }
            }
        }
        return $routes;
    }

    // %FIXME: is the table portion of this used in models or is there a version in base model?
    public static function slugify($strIn, $table = null)
    {
        $slug = preg_replace('~[^\\pL\d]+~u', '-', $strIn); // replace non letter or digits by -
        $slug = trim($slug, '-'); // trim
        //$slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug); // transliterate
        $slug = strtolower($slug); // lowercase
        $slug = preg_replace('~[^-\w]+~', '', $slug); // remove unwanted characters

        if (empty($table)) {
            return $slug;
        }

        // ensure unique
        $iter = 1;
        $ogSlug = $slug;
        do {
            $numMatches = \DB::table($table)->where('slug', '=', $slug)->count();
            if (($numMatches == 0) || ($iter > 10)) {
                break; // already unique, or we've exceeded max tries
            }
            $slug = $ogSlug.'-'.rand(1, 999);
        } while ($numMatches > 0);

        return $slug;
    }  // slugify()

    public static function unslugify($slug)
    {
        $str = ucwords(preg_replace('~[\-_]+~u', ' ', $slug)); // replace dash or underscore with space
        return $str;
    }

    public static function getBgImgByUniv($univSlug)
    {
        $univ = \University::where('slug', $univSlug)->first();
        if (empty($univ)) {
            return;
        }

        $files = [];
        $dirpath = public_path().'/img/background-boards/'.$univSlug;
        if ($handle = opendir($dirpath)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    $files[] = $entry;
                }
            }
            closedir($handle);
        }
        //dd($files);

        // pick one at random
        $max = count($files) - 1;
        $index = mt_rand(0, $max);
        //dd($index);
        $bgfile = $files[$index];

//dd($bgfile);
        return $bgfile;
        /*
        $univ = \University::where('slug',$univSlug)->first();
        if ( empty($univ) ) {
            return null;
        }

        $bgimgs = \Backgroundimage::where('university_id',$univ->id)->get();
        if ( !count($bgimgs) ) {
            return null;
        }

        // pick one at random
        $max = count($bgimgs) - 1;
        $index = mt_rand(0,$max);
        //dd($index);
        $bgimg = $bgimgs[$index];
        $bgimg->index = $index;

        return $bgimg;
         */
    }

}
