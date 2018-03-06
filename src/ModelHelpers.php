<?php
namespace PsgcLaravelPackages\Utils;

trait ModelHelpers
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
}
