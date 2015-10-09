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
     * @var string
     */
    public $executeType;
    /**
     * @var string
     */
    public $resultType;
    /**
     * @var string
     */
    public $linkType;
    /**
     * @var \Closure
     */
    public $inputReorder;
    /**
     * @var \Closure
     */
    public $outputReorder;
    /**
     * @var mixed
     */
    public $input = [];
    /**
     * @var mixed
     */
    public $output = [];
    /**
     * Sets the input reorder closure
     * 
     * @param \Closure $inputReorder
     */
    public function setInputReorder($inputReorder) {
        if ($inputReorder instanceOf Closure) {
            $this->inputReorder = \Closure::bind($inputReorder, $this, \get_class());
        } else if((!isset($inputReorder))?(strlen(''.trim($inputReorder).'') > 1):false) {
            eval('$_function = function($input) {\n '.$inputReorder.'\n };');
            if (isset($_function) AND $_function instanceOf Closure) {
                $this->inputReorder = \Closure::bind($_function, $this, \get_class());
            }    
        }
    }
    /**
     * Sets the output reorder closure
     * 
     * @param \Closure $outputReorder
     */
    public function setOutputReorder($outputReorder) {
        if ($outputReorder instanceOf Closure) {
            $this->outputReorder = \Closure::bind($outputReorder, $this, \get_class());
        } else if((!isset($outputReorder))?(strlen(''.trim($outputReorder).'') > 1):false) {
            eval('$_function = function($output) {\n '.$outputReorder.'\n };');
            if (isset($_function) AND $_function instanceOf Closure) {
                $this->outputReorder = \Closure::bind($_function, $this, \get_class());
            }    
        }
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
    public function getOutput() {
        return $this->output;
    }
    /**
     * Properly sets the execute type
     * 
     * @param type $executeType
     */
    public function setExecuteType($executeType) {
        if(in_array(strtoupper($executeType),["ROW","NONE"])) {
            $this->executeType=strtoupper($executeType);
        } else {
            $this->executeType = "NONE";
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
    public static function getConnection($name) {
        $pieces = explode("\\", get_called_class());
        return \CF\RuleChains\ConnectionsRC::getConnection(array_pop($pieces), $name);
    }
    /**
     * Executes the current rule
     * 
     * @return mixed the result of the execution
     */
    abstract public function execute();
    /**
     * Returns next row in result array/iterator or returns false
     * 
     * @return mixed
     */
    abstract public function getNextResultRow();
}
