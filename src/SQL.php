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
    public function execute() {
        self::getConnection($this->name);
    }

//put your code here
}
