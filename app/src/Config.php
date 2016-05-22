<?php
namespace DarthEv\Core;

/**
 * Utility to deal with configuration file
 * @author marcelbonnet
 *
 */
class Config
{

    public static $CONFIG_FILE= __DIR__ . '/../../conf/' . "config.ini";

    public static function get()
    {
        $conf = parse_ini_file( self::$CONFIG_FILE , true );
        if ($conf === false ) throw new \Exception("Error loading Application's configuration file: " . $CONFIG_FILE );
        return $conf;
    }

    /**
    * Makes sure that ini files are cached by APC
    * http://stackoverflow.com/questions/2120401/php-parse-ini-file-performance
    */
    function parse_ini_file_ext ($file, $sections = null) {
        ob_start();
        include $file;
        $str = ob_get_contents();
        ob_end_clean();
        return parse_ini_string($str, $sections);
    }

    public static function validate()
    {
        #TODO: check if basic props were set. throw \Exception if IO Err, return false if invalid.
        return true;
    }
}
