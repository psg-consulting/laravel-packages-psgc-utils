<?php
namespace PsgcLaravelPackages\Utils;

use DB;

trait ModelTraits
{

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function getGuardedColumns()
    {
        return with(new static)->guarded;
    }

    public static function _getFillables()
    {
        $table = self::getTableName();
        $columns = \Schema::getColumnListing($table);
        $guarded = self::getGuardedColumns();
        $fillables = array_diff($columns,$guarded);
        return $fillables;
    }

    // %FIXME: better implementation
    // SEE: https://stackoverflow.com/questions/32989034/laravel-handle-findorfail-on-fail
    // Like Eloquent's first(), but specific to where-by-slug, and throws detailed exception
    public static function findBySlug($slug)
    {
        //return with(new static)->getTable();
        //$record = \App\Models\Scheduleditem::where('slug',$slug)->first();
        $record = self::where('slug',$slug)->first();
        if ( empty($record) ) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Could not find record with slug '.$slug);
        }
        return $record;
    }

    // %FIXME: better implementation
    public static function findByPKID($pkid)
    {
        //return with(new static)->getTable();
        //$record = \App\Models\Scheduleditem::where('slug',$slug)->first();
        $record = self::find($pkid);
        if ( empty($record) ) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Could not find record with pkid '.$pkid);
        }
        return $record;
    }

    // child classes can override, but impl should call parent
    public function renderFieldKey($key)
    {
        $key = trim($key);
        switch ($key) {
            case 'guid':
                $key = 'GUID';
                break;
            case 'id':
                $key = 'PKID';
                break;
            case 'created_at':
                $key = 'Created';
                break;
            case 'updated_at':
                $key = 'Updated';
                break;
            default:
                $key = ucwords($key);
        }
        return $key;
    }

    // child classes can override, but impl should call parent
    public function renderField($field)
    {
        $key = trim($field);
        switch ($key) {
            case 'guid':
                return strtoupper($this->{$field});
            case 'created_at':
            case 'updated_at':
            case 'deleted_at':
                return ViewHelpers::makeNiceDate($this->{$field},1,1); // number format, include time
            default:
                return $this->{$field};
        }
    }

    // $sluggableFields is an Array of table field names to use to create the slug
    public function slugify(Array $sluggableFields, String $slugField='slug', Bool $makeUnique=true)
    {
        $tablename = self::getTablename();

        // Get actual contents of the sluggable fields...
        $sluggable = [];
        foreach ($sluggableFields as $f) {
            $sluggable[] = $this->{$f};
        }
        return  self::slugifyByTable($tablename, $sluggable, $slugField, $makeUnique);
    }

    // $sluggable is an Array of strings, ints, values, etc  used to create the slug
    public static function slugifyByTable(String $table, Array $sluggable, String $slugField='slug', Bool $makeUnique=true)
    {
        $slug = implode('-',$sluggable);
        $slug = preg_replace('~[^\\pL\d]+~u', '-', $slug); // replace non letter or digits by -
        $slug = trim($slug, '-'); // trim
        //$slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug); // transliterate
        $slug = strtolower($slug); // lowercase
        $slug = preg_replace('~[^-\w]+~', '', $slug); // remove unwanted characters

        if ($makeUnique) {
    
            $iter = 1;
            $ogSlug = $slug;
            if (0) {
                $numMatches = DB::table($table)->where($slugField, '=', $slug)->count();
                $slug = $ogSlug.'-'.$numMatches;
            } else {
                do {
                    $numMatches = DB::table($table)->where($slugField, '=', $slug)->count();
                    if (($numMatches == 0) || ($iter > 10)) {
                        break; // already unique, or we've exceeded max tries
                    }
                    $slug = $ogSlug.'-'.rand(1, 999);
                } while ($numMatches > 0);
            }
        }

        return $slug;

    } // slugifyByTable()


}
