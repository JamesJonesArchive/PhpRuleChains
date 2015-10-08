<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CF\RuleChains;
/**
 * Description of ChainTest
 *
 * @author james
 */
class ChainTest extends \PHPUnit_Extensions_Database_TestCase {
    protected static $db = null;
    protected $conn = null;
    public $chain;
    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection() {
        return $this->createDefaultDBConnection(ConnectionsRC::getConnection("SQL", "localhost"), ':memory:');
        // return ConnectionsRC::getConnection("SQL", "localhost");
    }
    public function createTable() {
        $sql = 'CREATE TABLE IF NOT EXISTS `account` (
            `user_id` int(11),
            `user_name` varchar(50) NOT NULL,
            `email` varchar(50) NOT NULL,
            `age` int(11) NULL,
            `birthday` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            `city` varchar(20) NULL,
            `promoted` int(5) NULL,
            `lang` varchar(50) NULL,
            PRIMARY KEY (`user_id`)
          );';
        ConnectionsRC::getConnection("SQL", "localhost")->exec($sql);
    }
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet() {
        return $this->createArrayDataSet([
            'account' => [
                ['email' => 'joe@gmail.com', 'user_name' => 'joe', 'birthday' => '2010-04-24 17:15:23'],
                ['email' => 'mark@gmail.com',   'user_name' => 'mark',  'birthday' => '2010-04-26 12:14:20'],
            ],
        ]);
    }
    
    public function setUp() {
        $this->chain = new Chain([
            'SQL' => [
                'localhost' => [
                    'database_type' => 'sqlite',
                    'database_file' => ':memory:'
                ]
            ]
        ],[
            [
                "type" => "SQL",
                "name" => "localhost",
                "executeType" => "NONE",
                "resultType" => "RECORDSET",
                "linkType" => "LOOP",
                "rule" => "SELECT * FROM account"
            ],
            [
                "type" => "SQL",
                "name" => "localhost",
                "executeType" => "ROW",
                "resultType" => "ROW",
                "linkType" => "NONE",
                "rule" => "SELECT :email,:user_name,:birthday,:age,:city,:promoted,:lang,:user_id"
            ]
        ],[],true);
        $this->createTable();
        parent::setUp();
    }
    
    //put your code here
    public function testConversion() {
        // print_r($chain->rules);
        // Test rule count
        $this->assertCount(2, $this->chain->rules);
        // Test class conversion
        $this->assertTrue($this->chain->rules[0] instanceof \CF\RuleChains\SQL);
        
        $sth = ConnectionsRC::getConnection("SQL", "localhost")->prepare("SELECT * FROM account");
        $sth->execute();
        // print_r($sth->fetchAll());
    }
    
    public function testExecute() {
        $this->chain->execute();
        print_r($this->chain->getChainResult());
        print_r($this->chain->getChainResult());
        print_r($this->chain->getChainResult());
        print_r($this->chain->getChainResult());
    }

}
