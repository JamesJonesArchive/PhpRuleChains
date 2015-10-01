<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CF\RuleChains;
/**
 * Description of Rule
 *
 * @author James Jones <jamjon3@gmail.com>
 */
abstract class Rule {
    public $name;
    private $inputReorder;
    private $outputReorder;
    private $input = [];
    private static $connections; // MySQLi-Connection, same for all subclasses
    private static $config;
    /**
     * Sets the input reorder closure
     * 
     * @param \Closure $inputReorder
     */
    public function setInputReorder(\Closure $inputReorder) {
        $this->inputReorder = Closure::bind($inputReorder, $this, get_class());
    }
    /**
     * Sets the output reorder closure
     * 
     * @param \Closure $outputReorder
     */
    public function setOutputReorder(\Closure $outputReorder) {
        $this->outputReorder = Closure::bind($outputReorder, $this, get_class());
    }
    /**
     * Sets the input object/array
     * 
     * @param mixed $input
     */
    public function setInput($input =[]) {
        $this->input = $input;
    }
    /**
     * Get the stored input object/array
     * 
     * @return mixed
     */
    public function getInput() {
        return $this->input;
    }
    /**
     * Get the specified connection by type and name. If it doesn't exist
     * the connection will be pulled in from the config values (if possible)
     * or an exception will be thrown
     * 
     * @param string $type
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public static function getConnection(string $type,string $name) {
        if(!isset(self::$config)) {
            self::$config = new Configula\Config('/path/to/app/config');
        }
        if(!isset(self::$connections)) {
            self::$connections = [];
        }
        if(!isset(self::$connections[$type])) {
            self::$connections[$type] = [];
        }
        if(!isset(self::$connections[$type][$name])) {
            // Try to load it from config            
            switch ($type) {
                case 'sql':
                    if(!isset(self::$config->getItem('ruleChainsConnections', [])[$type])) {
                        throw new Exception("No config for connection type: $type");
                    } else if(!isset(self::$config->getItem('ruleChainsConnections', [])[$type][$name])) {
                        throw new Exception("No config for named connection name '$name' for type '$type'");
                    }
                    self::$connections[$type][$name] = new \medoo(self::$config->ruleChainsConnections[$type][$name]); 
                    break;
                default:
                    throw new Exception("No connection type defined for type: $type");
                    break;
            }
        }
        return self::$connections[$type][$name];        
    }
    /**
     * Executes the current rule
     * 
     * @return mixed the result of the execution
     */
    abstract public function execute();
}
