<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CF\RuleChains;

/**
 * Description of SQL
 *
 * @author james
 */
class SQL extends Rule {
    public $rule;
    /**
     * Adds a : to the beginning of the array keys
     * 
     * @param array $row
     * @return array
     */
    public static function createPrepareArray($row) {
        $params = [];
        foreach($row as $k=>$v){
            $params[':'.$k] = $v;
        }
        return $params;
    }
    /**
     * Executes the SQL Rule
     */
    public function execute() {
        $rule = $this->rule;
        foreach(((isset($this->inputReorder))?call_user_func_array($this->inputReorder, $this->input):$this->input) as $key => $value) {
            $rule = str_replace('$'.$key, $value, $rule);   
        }            
        $stmt = self::getConnection($this->name)->prepare($rule);
        switch($this->executeType) {
            case "ROW":
                $stmt->execute(self::createPrepareArray((isset($this->inputReorder))?call_user_func_array($this->inputReorder, $this->input):$this->input));
                break;
            case "NONE":
                // Do not bind
                $stmt->execute();
                break;
            default:
                // Do not bind
                $stmt->execute();
                break;                
        }
        echo "RESULT TYPE: ".$this->resultType."\n";
        switch($this->resultType) {
            case "ROW": 
                $row = $stmt->fetch();
                print_r($row);
                $this->output = ($row === false)?[]:[ $row ];
                break;
            case "RECORDSET": 
                $this->output = &$stmt;
                break;
            case "NONE":
                $this->output = [];
                break;
            default:
                $this->output = [];
                break;
        }
    }
    /**
     * Get the next available row or return false
     * 
     * @return mixed
     */
    public function getNextResultRow() {
        // handle statements and arrays... optionally through the output reorder
        if(!isset($this->output)) {
            return false;
        } elseif ($this->output instanceof \PDOStatement) {
            if($row = $this->output->fetch()) {
                return (isset($this->outputReorder))?call_user_func_array($this->outputReorder, $row):$row;
            }
            $this->output->closeCursor();
            unset($this->output);
            return false;
        } elseif ((is_array($this->output))?!(array_keys($this->output) !== range(0, count($this->output) - 1)):false) {
            if(empty($this->output)) {
                unset($this->output);
                return false;
            }
            $row = array_shift($this->output);
            return (isset($this->outputReorder))?call_user_func_array($this->outputReorder, $row):$row;
        } else {
            $row = $this->output;
            unset($this->output);
            return (isset($this->outputReorder))?call_user_func_array($this->outputReorder, $row):$row;
        }
    }

}
