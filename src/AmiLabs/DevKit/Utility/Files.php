<?php

namespace AmiLabs\DevKit\Utility;

/**
 * Files utility class.
 *
 * @package AmiLabs/DevKit/Utility
 */
class Files{
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
