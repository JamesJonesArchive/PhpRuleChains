<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CF\RuleChains;

/**
 * Description of ConnectionsRC
 *
 * @author james
 */
class ConnectionsRC {
    // The database connections
    protected static $connections;
    protected static $config;
    /**
     * Retrieves a connection of a type by the specified name
     * 
     * @param string $type The 'type' of connection
     * @param string $name The unique 'name' of the connection within the type
     * @return mixed A connection
     * @throws Exception Throws exception if config is missing, the type is undefined or the connection create fails
     */
    public static function getConnection(string $type,string $name) {
        if(!isset(self::$config)) {
            throw new Exception("No config object");
        } else if(!isset(self::$connections)) {
            self::$connections = [];
        }
        if(!isset(self::$connections[$type])) {
            self::$connections[$type] = [];
        }
        if(!isset(self::$connections[$type][$name])) {
            if(!isset(self::$config[$type])) {
                throw new Exception("No config for connection type: $type");
            } else if(!isset(self::$config[$type][$name])) {
                throw new Exception("No config for named connection name '$name' for type '$type'");
            }
            switch ($type) {
                case "SQL":
                    self::$connections[$type][$name] = new \medoo(self::$config[$type][$name]);
                    break;
                default:
                    throw new Exception("No connection type defined for type: $type");
                    break;
            }
        }
        return self::$connections[$type][$name]; 
    }
    /**
     * An array of connection types with their arrays of name/configs
     * 
     * @param array $config
     */
    public static function setConfig(array $config) {
        self::$config = $config;
        self::$connections = [];
    }
}
