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
    private $rules;
    public function __construct(array $config,array $rules) {
        ConnectionsRC::setConfig($config);
        $this->rules = \array_map(function($r) {
            $class = "\\CF\\RuleChains\\".$r['type'];
            $rule = new $class;
            foreach (\array_keys(\get_class_vars("\\CF\\RuleChains\\".$r['type'])) as $key) {
                if(isset($r[$key])) {
                    switch($key) {
                        case "executeType":
                            print("$key is ".$r[$key]."\n");
                            $rule->setExecuteType($r[$key]);
                            break;
                        case "resultType":
                            $rule->setResultType($r[$key]);
                            break;
                        case "linkType":
                            $rule->setResultType($r[$key]);
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
                }
            }
            return $rule;
        }, $rules);
        print_r($this->rules);
    }
    public function execute() {
        for($i=0; $i < count($this->rules); $i++) {
            $this->rules[$i]->execute();
            switch($this->rules[$i]->linkType) {
                case "NONE": 
                    break;
                case "LOOP": 
                    break;
                case "ENDLOOP": 
                    break;
                case "NEXT":
                    break;
                default:
                    // do nothing
                    break;
            }
        }
    }
}
