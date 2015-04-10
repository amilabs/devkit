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
}