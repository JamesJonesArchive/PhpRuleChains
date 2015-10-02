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
    /**
     * @var string
     */
    public $name;
    /**
     * @var \Closure
     */
    public $executeType;
    public $resultType;
    public $linkType;
    private $inputReorder;
    /**
     * @var \Closure
     */
    private $outputReorder;
    /**
     * @var mixed
     */
    private $input = [];
    /**
     * @var mixed
     */
    private $output = [];
    /**
     * @var \CF\RuleChains  
     */
    private $connectionsRC;
    /**
     * @var array
     */
    protected static $connections; // MySQLi-Connection, same for all subclasses
    /**
     * @var array
     */
    protected static $config;
    /**
     * Set the connections into the rule
     * 
     * @param type $connectionsRC
     */
    public function setConnectionsRC(\CF\RuleChains $connectionsRC) {
        $this->connectionsRC = $connectionsRC;
    }
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
     * Sets the output object/array
     * 
     * @param mixed $output
     */
    public function setOutput($output =[]) {
        $this->output = $output;
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
     * Properly sets the execute type
     * 
     * @param type $executeType
     */
    public function setExecuteType($executeType) {
        if(in_array(strtoupper($executeType),["ROW","NONE"])) {
            $executeType=strtoupper($executeType);
        } else {
            $executeType = "NONE";
        }                
    }
    /**
     * Properly sets the result type
     * 
     * @param type $resultType
     */
    public function setResultType($resultType) {
        if(in_array(strtoupper($resultType),["ROW","RECORDSET","NONE"])) {
            $this->resultType=strtoupper($resultType);
        } else {
            $this->resultType = "NONE";
        }                
    }
    /**
     * Properly sets the link type
     * 
     * @param type $linkType
     */
    public function setLinkType($linkType) {
        if(in_array(strtoupper($linkType),["NONE","LOOP","ENDLOOP","NEXT"])) {
            $this->linkType=strtoupper($linkType);
        } else {
            $this->linkType = "NONE";
        }                
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
    public static function getConnection(string $name) {
        return ConectionsRC::getConnection(get_class($this), $name);
    }
    /**
     * Executes the current rule
     * 
     * @return mixed the result of the execution
     */
    abstract public function execute();
}
