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
    }
}