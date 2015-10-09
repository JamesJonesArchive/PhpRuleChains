<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CF\RuleChains;

/**
 * Description of Chain
 *
 * @author james
 */
class Chain {
    public $rules;
    public function __construct($config,array $rules,$input = [],$parseRules = true) {
        if(isset($config)) {
            ConnectionsRC::setConfig($config);
        }
        if($parseRules) {
            $this->rules = \array_map(function($r) use($input) {                     
                $class = "\\CF\\RuleChains\\".((isset($r['type']))?((in_array($r['type'], ["SQL"]))?$r['type']:"SQL"):"SQL");
                $rule = new $class;
                foreach (\array_keys(\get_class_vars("\\CF\\RuleChains\\".$r['type'])) as $key) {
                    if(isset($r[$key])) {
                        switch($key) {
                            case "executeType":
                                $rule->setExecuteType($r[$key]);
                                break;
                            case "resultType":
                                $rule->setResultType($r[$key]);
                                break;
                            case "linkType":
                                $rule->setLinkType($r[$key]);
                                break;
                            case "outputReorder":
                                $rule->setOutputReorder($r[$key]);
                                break;
                            case "inputReorder":
                                $rule->setInputReorder($r[$key]);
                                break;
                            default:
                                $rule->{$key} = $r[$key];
                                break;
                        }
                        $rule->input = $input;
                    }
                }
                return $rule;
            }, $rules);
        } else {
            $this->rules = \array_map(function($r) use($input) {   
                $r->input = $input;
                return $r;
            }, $rules);
        }
    }
    /**
     * Find the index of the corresponding endloop
     * 
     * @param int $startindex
     * @return int
     */
    public function getEndLoopIndex($startindex) {
        $endindex = count($this->rules) - 1;
        if($endindex > $startindex) {
            $depthcount =0;
            try {
                for($i=$startindex; $i < $endindex; $i++) {
                    switch($this->rules[$i]->linkType) {
                        case "LOOP": 
                            $depthcount++;
                            break;
                        case "ENDLOOP": 
                            if($depthcount > 0) {
                                $depthcount--;
                            } else {
                                $endindex = $i;
                                throw new \Exception('End index found');
                            }
                            break;
                    }
                }
            } catch (\Exception $ex) {
                return $endindex;
            }
        }
        return $endindex;
    }
    /**
     * Get the final result after the chain execution
     * 
     * @return mixed
     */
    public function getChainResult() {
        if(empty($this->rules)) {
            return false;
        } else {
            return $this->rules[count($this->rules) -1]->getOutput();
        }
    }
    /**
     * Execute the chain
     */
    public function execute() {
        for($i=0; $i < count($this->rules); $i++) {
            $this->rules[$i]->execute();
            echo "LinkType: ".$this->rules[$i]->linkType."\n";
            switch($this->rules[$i]->linkType) {
                case "NONE": 
                    break;
                case "LOOP": 
                    // Find the matching endloop, extract the subarray as a new chain and loop
                    // execution for each row
                    echo "DETECTED LOOP\n";
                    if((count($this->rules)-($i+1)) > 0) {
//                    if(((count($this->rules)-1)-$i) > 0) {
                        $endindex = $this->getEndLoopIndex($i + 1);
                        echo "LOOP START\n";
                        echo $i.": LOOP RULE Index\n";
                        $loopcount=0;
                        $loopchain = NULL;
                        while($row = $this->rules[$i]->getNextResultRow()) {
                            echo ($i+1).": INNER RULE Index\n";
                            echo $endindex.": Endindex\n";
                            $loopcount++;
                            echo "INNER RULE ITERATION COUNTER: ".$loopcount."\n";
                            echo "Offset: ".($i + 1)."\n";
                            echo "Length: ".(($endindex - $i))."\n";
                            $loopchain = new self(null,\array_slice($this->rules,($i + 1),($endindex - $i)),$row,FALSE);
                            $loopchain->execute();
                            print_r($loopchain->rules[count($loopchain->rules)-1]);
                            if(in_array($this->rules[$endindex]->resultType, ['ROW','RECORDSET'])) {
                                $this->rules[$endindex]->setOutput($loopchain->getChainResult());
                            }
                        }
                        echo "LOOP END\n";
                        $i = ($endindex+1);
                        echo $i.": Current Index\n";
                        print_r($this->rules[$endindex]->getOutput());
                    }
                    break;
                case "ENDLOOP": 
                    break;
                case "NEXT":
                    if((count($this->rules)-$i) > 1) {
                        $this->rules[$i+1]->input = clone $this->rules[$i]->input;
                    }
                    break;
                default:
                    // do nothing
                    break;
            }
        }
    }
}
