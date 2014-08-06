<?php
namespace Jenga\Configurations;
/**
 * Database configurations.
 *
 * @author Chris Santos
 */
class Database {

    private static $conf;

    private static $engines = array();

    /**
     * TODO: Add checks for dups names.
     *
     * @param $name
     * @param $conf
     */
    public static function addConfiguration($name, $conf) {

       self::$conf[$name] = $conf;
    }

    /**
     * Returns instance of database engine based on configuration.
     *
     * @param string $name
     * @return mixed
     */
    public static function getDatabaseEngine($name) {

        if(isset(self::$engines[$name]))
            return self::$engines[$name];

        $conf = self::$conf[$name];
        return self::$engines[$name] = new $conf['engine']($conf['host'], $conf['port']);
    }
}