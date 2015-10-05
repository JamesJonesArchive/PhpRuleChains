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
    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection() {
//        $pdo = new \PDO('sqlite::memory:');
//        return $this->createDefaultDBConnection($pdo, ':memory:');
        if ($this->chain === null) {
            
        }
        
        if ($this->conn === null) {
            
            if (self::$db == null) {
                
                self::$db = new Medoo\Medoo([
                    'databaseType' => 'sqlite',
                    'databaseFile' => ':memory:'
                ]);
                
                $this->createTable();
            }
            $this->conn = $this->createDefaultDBConnection(self::$db->pdo, ':memory:');
        }
        return $this->conn;
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
        ConnectionsRC::getConnection("SQL", "localhost");
        self::$db->pdo->exec($sql);
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
        $chain = new Chain([
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
                "executeType" => "ROW",
                "rule" => "SELECT 1 FROM DUAL"
            ]
        ],[],true);
        ConnectionsRC::$connections["SQL"]["localhost"] = $this->createDefaultDBConnection(ConnectionsRC::getConnection("SQL", "localhost")->pdo, ':memory:');
//        $this->usfARMapi = $this->getMockBuilder('\USF\IdM\UsfARMapi')
//        ->setMethods(array('getARMdb','getARMaccounts','getARMroles'))
//        ->getMock();
//        
//        $this->usfARMapi->expects($this->any())
//        ->method('getARMdb')
//        ->will($this->returnValue($this->getMongoConnection()));
        parent::setUp();
    }
    
    //put your code here
    public function testConversion() {
        $chain = new Chain([
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
                "executeType" => "ROW",
                "rule" => "SELECT 1 FROM DUAL"
            ]
        ],[],true);
        // print_r($chain->rules);
        // Test rule count
        $this->assertCount(1, $chain->rules);
        // Test class conversion
        $this->assertTrue($chain->rules[0] instanceof \CF\RuleChains\SQL);
    }

}
