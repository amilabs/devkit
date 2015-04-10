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
        $filename = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $filename);
        $filename = preg_replace("([\.]{2,})", '', $filename);
        return $filename;
    }
}