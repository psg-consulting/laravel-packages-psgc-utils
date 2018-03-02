<?php
namespace PsgcLaravelPackages\Utils;

class Mime
{
    //http://hul.harvard.edu/ois/systems/wax/wax-public-help/mimetypes.htm
    public static $audio = [
        'application/x-troff-msvideo' => 'avi',
        'video/avi' => 'avi',
        'video/msvideo' => 'avi',
        'audio/mpeg3' => 'mp3',
        'audio/x-mpeg-3' => 'mp3',
        'audio/wav' => 'wav',
        'audio/x-wav' => 'wav',
    ];
    public static $video = [
        'video/quicktime' => 'mov',
        'video/mpeg' => 'mpe',
        'video/mpeg' => 'mpg',
    ];
    public static $image = [
        'image/gif' => 'gif',
        'image/jpeg' => 'jpe',
        'image/pjpeg' => 'jpe',
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/tiff' => 'tif',
        'image/x-tiff' => 'tif',
    ];

    public static function isImage($mimetype)
    {
        return array_key_exists($mimetype, self::$image);
    }
    public static function isAudio($mimetype)
    {
        return array_key_exists($mimetype, self::$audio);
    }
    public static function isVideo($mimetype)
    {
        return array_key_exists($mimetype, self::$video);
    }
}
