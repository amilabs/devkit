<?php

namespace AmiLabs\DevKit;

/**
 * Utility methods.
 */
class Utils
{
    /**
     * Sanitize filename: remove all restricted characters and sequences
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
        $filename=preg_replace("([\n\r])", '', $filename);
        return $filename;
    }

    /**
     * Saves file.
     *
     * @param  string $path     Path
     * @param  string $content  Content
     * @param  int    $flags    Flags ({@see http://php.net/manual/en/function.file-put-contents.php})
     * @param  int    $mode     File mode ({@see http://php.net/manual/en/function.chmod.php})
     * @return bool
     */
    public static function saveFile($path, $content, $flags, $mode = 0666){
        $res = FALSE;
        $res = file_put_contents($path, $content, $flags);
        if($res){
            @chmod($path, $mode);
        }

        return $res;
    }
}