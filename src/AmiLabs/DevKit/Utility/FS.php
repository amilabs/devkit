<?php

namespace AmiLabs\DevKit\Utility;

/**
 * FileSystem utility methods.
 */
class FS
{
    /**
     * Sanitize filename: remove all restricted characters and sequences.
     *
     * @param mixed $filename  Filename to sanitize
     * @return string
     */
    public static function sanitizeFilename($filename)
    {
        // Remove special chars
        $filename = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $filename);
        // Remove double dots
        $filename = preg_replace("([\.]{2,})", '', $filename);
        // Remove linebreaks
        $filename = preg_replace("([\n\r])", '', $filename);
        return $filename;
    }

    /**
     * Saves file.
     *
     * @param  string $path     Path to file
     * @param  string $content  File content
     * @param  int    $flags    Flags ({@see http://php.net/manual/en/function.file-put-contents.php})
     * @param  int    $mode     Access mode ({@see http://php.net/manual/en/function.chmod.php})
     * @return bool
     */
    public static function saveFile($path, $content, $flags = 0, $mode = 0666){
        $res = file_put_contents($path, $content, $flags);
        if($res){
            @chmod($path, $mode);
        }
        return $res !== FALSE;
    }

    /**
     * Reads file contents, returns FALSE if file not found or not readable.
     *
     * @param  string $path  Path to file
     * @return mixed
     * @throws Exception
     */
    public static function readFile($path)
    {
        $res = FALSE;
        if(file_exists($path) && is_readable($path)){
            $res = file_get_contents($path);
        }else{
            throw new \Exception('File not found');
        }
        return $res;
    }

    /**
     * Removes file if file exists.
     *
     * @param  string $path  Path to file
     * @return bool
     */
    public static function deleteFile($path)
    {
        $res = FALSE;
        if(file_exists($path)){
            $res = unlink($path);
        }
        return $res;
    }

    /**
     * Removes file if file exists.
     *
     * @param  string $path  Path to file
     * @param  int    $mode  Access mode ({@see http://php.net/manual/en/function.chmod.php})
     * @return bool
     */
    public static function mkDir($path, $mode = 0x777)
    {
        $res = FALSE;
        if(!is_dir($path) && !file_exists($path)){
            $res = mkdir($path);
            @chmod($path, $mode);
        }
        return $res;
    }
}