<?php
namespace PsgcLaravelPackages\Utils;

class Image
{
    // http://stackoverflow.com/questions/14649645/resize-image-in-php
    // http://php.net/manual/en/function.imagecreatefromjpeg.php
    // ** http://stackoverflow.com/questions/13596794/resize-images-with-php-support-png-jpg
    // * http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
    // http://stackoverflow.com/questions/718491/resize-animated-gif-file-without-destroying-animation
    // https://github.com/Sybio/GifFrameExtractor/blob/master/src/GifFrameExtractor/GifFrameExtractor.php
    public static function resizeFromUrl($file, $w, $h)
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($w / $h > $r) {
            $newwidth = $h * $r;
            $newheight = $h;
        } else {
            $newheight = $w / $r;
            $newwidth = $w;
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        imagedestroy($src); // %PSG

        return $dst;
    }

    // $newfilename is w/o extension
    // %PSG: pass $h = 0 to keep proportionate
    public static function resize($imgFile, $w, $h, $newFilename, $path, $forceJPEG = 0)
    {
        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            throw new \Exception('GD is not loaded', E_USER_WARNING);

            return false;
        }

        //Get Image size info
        // see: http://php.net/manual/en/function.getimagesize.php
        $imgInfo = getimagesize($imgFile);
        switch ($imgInfo[2]) {
            case 1:
                $im = imagecreatefromgif($imgFile);
                $ext = 'gif';
                break;
            case 2:
                $im = imagecreatefromjpeg($imgFile);
                $ext = 'jpg';
                break;
            case 3:
                $im = imagecreatefrompng($imgFile);
                $ext = 'png';
                break;
            default:
                throw new \Exception('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        //If image dimension is smaller, do not resize
        //if ($imgInfo[0] <= $w && $imgInfo[1] <= $h)
        if (
                 (!empty($h) && ($imgInfo[0] <= $w && $imgInfo[1] <= $h))
              || (empty($h)  && ($imgInfo[0] <= $w))
           ) {
            $nHeight = $imgInfo[1];
            $nWidth = $imgInfo[0];
        } else {
            //yeah, resize it, but keep it proportional
            if (empty($h) || ($w / $imgInfo[0] > $h / $imgInfo[1])) { // %PSG
                $nWidth = $w;
                $nHeight = $imgInfo[1] * ($w / $imgInfo[0]);
            } else {
                $nWidth = $imgInfo[0] * ($h / $imgInfo[1]);
                $nHeight = $h;
            }
        }
        $nWidth = round($nWidth);
        $nHeight = round($nHeight);

        $newImg = imagecreatetruecolor($nWidth, $nHeight);

        /* Check if this image is PNG or GIF, then set if Transparent*/
        if (($imgInfo[2] == 1) or ($imgInfo[2] == 3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

        if ($forceJPEG) {
            $ext = 'jpg';
            $newFilename .= '.'.$ext;
            $newFilepath = $path.'/'.$newFilename;
            imagejpeg($newImg, $newFilepath);
        } else {
            $newFilename .= '.'.$ext;
            $newFilepath = $path.'/'.$newFilename;
            //Generate the file, and rename it to $newfilename
            switch ($imgInfo[2]) {
                case 1:
                    imagegif($newImg, $newFilepath); // in,out
                    break;
                case 2:
                    imagejpeg($newImg, $newFilepath);
                    break;
                case 3:
                    imagepng($newImg, $newFilepath);
                    break;
                default:
                    throw new \Exception('Failed resize image', E_USER_WARNING);
            }
        }

//imagedestroy($im);

        //return $newFilepath;
        return $newFilename;
    } // resize()
}
