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
        $this->rules = $rules;
        $this->rules = \array_map(function($r) {
            $class = "\\CF\\RuleChains\\".$r['type'];
            $rule = new $class;
            foreach (\array_keys(\get_class_vars("\\CF\\RuleChains\\".$r['type'])) as $key) {
                $rule->{$key} = $r[$key];
            }
            return $rule;
//            print($class."\n");
//            return \array_keys(\get_class_vars("\\CF\\RuleChains\\".$r['type']));
        }, $rules);
        print_r($this->rules);
    }
}
