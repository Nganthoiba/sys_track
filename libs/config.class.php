<?php

/**
 * Description of Config
 *
 * @author Nganthoiba
 */
class Config {
    protected static $setting = array();
    public static function get($key){
        return isset(self::$setting[$key])?self::$setting[$key]:null;
    }
    public static function set($key, $value){
        self::$setting[$key] = $value;
    }
}
