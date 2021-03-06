<?php

//require_once 'PHPUnit/Extensions/Database/TestCase.php';
//require_once 'PHPUnit/Extensions/Database/DataSet/DefaultDataSet.php';
require_once dirname(__FILE__) . '/TestUtil.php';

use Doctrine\ORM\EntityManager;

require_once dirname(__FILE__) . '/bootstrap.php';
//require_once dirname(__FILE__) . '/../../lib/Gocdb_Services/PI/QueryBuilders/ScopeQueryBuilder.php';
//require_once dirname(__FILE__) . '/../../lib/Gocdb_Services/PI/QueryBuilders/Helpers.php';
require_once dirname(__FILE__) . '/../../lib/Gocdb_Services/PI/GetNGI.php';
require_once dirname(__FILE__) . '/../../lib/Gocdb_Services/PI/GetSite.php';
require_once dirname(__FILE__) . '/../../lib/Gocdb_Services/PI/GetService.php';
require_once dirname(__FILE__) . '/../../lib/Gocdb_Services/PI/GetServiceGroup.php';
//
require_once dirname(__FILE__) . '/../../lib/Gocdb_Services/Factory.php';
require_once dirname(__FILE__) . '/../../lib/Gocdb_Services/PI/QueryBuilders/ExtensionsParser.php';


/**
 * Creates selected <code>IPIQuery</code> objects that perform queries on 
 * entity objects that own extension properties. Assert that the query objects return 
 * the expected number of entities when querying against known fixture data using 
 * different extension queries. 
 * <p>
 * Covers the following IPIQuery objects: GetSite 
 * 
 * @author David Meredith
 */
class ExtensionProps_IPIQuery_Test1 extends PHPUnit_Extensions_Database_TestCase {

    private $em;

    /**
     * Overridden. 
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        echo "\n\n-------------------------------------------------\n";
        echo "Executing ExtensionProps_IPIQuery_Test1. . .\n";
    }

    /**
     * Overridden. Returns the test database connection.
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection() {
        require_once dirname(__FILE__) . '/bootstrap_pdo.php';
        return getConnectionToTestDB();
    }

    /**
     * Overridden. Returns the test dataset.  
     * Defines how the initial state of the database should look before each test is executed. 
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet() {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/truncateDataTables.xml');
        // Use below to return an empty data set if we don't want to truncate and seed
        //return new PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
    }

    /**
     * Overridden. 
     */
    protected function getSetUpOperation() {
        // CLEAN_INSERT is default
        //return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
        //return PHPUnit_Extensions_Database_Operation_Factory::UPDATE();
        //return PHPUnit_Extensions_Database_Operation_Factory::NONE();
        //
        // Issue a DELETE from <table> which is more portable than a 
        // TRUNCATE table <table> (some DBs require high privileges for truncate statements 
        // and also do not allow truncates across tables with FK contstraints e.g. Oracle)
        return PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL();
    }

    /**
     * Overridden. 
     */
    protected function getTearDownOperation() {
        // NONE is default
        return PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    /**
     * Sets up the fixture, e.g create a new entityManager for each test run
     * This method is called before each test method is executed.
     */
    protected function setUp() {
        parent::setUp();
        $this->em = $this->createEntityManager();
    }

    /**
     * @return EntityManager
     */
    private function createEntityManager() {
        require dirname(__FILE__) . '/bootstrap_doctrine.php';
        return $entityManager;
    }

    /**
     * Called after setUp() and before each test. Used for common assertions
     * across all tests.
     */
    protected function assertPreConditions() {
        $con = $this->getConnection();
        $fixture = dirname(__FILE__) . '/truncateDataTables.xml';
        $tables = simplexml_load_file($fixture);

        foreach ($tables as $tableName) {
            //print $tableName->getName() . "\n";
            $sql = "SELECT * FROM " . $tableName->getName();
            $result = $con->createQueryTable('results_table', $sql);
            //echo 'row count: '.$result->getRowCount() ; 
            if ($result->getRowCount() != 0)
                throw new RuntimeException("Invalid fixture. Table has rows: " . $tableName->getName());
        }
    }


    public function xtestExtensionsParser(){
        print __METHOD__ . "\n";
        $rawQuery = "(V0=1)OR(VO2=bar)(VO2=baz)"; 
        $extParser = new \org\gocdb\services\ExtensionsParser(); 
        $normalisedQuery = $extParser->parseQuery($rawQuery); 
        //print_r($normalisedQuery); 
        foreach($normalisedQuery as $sub){
            print_r ($sub); 
        }        
    }
   
    /**
     * Run queries against Sites with extension properties. 
     */
    public function testSiteExtensions() {
        print __METHOD__ . "\n";
        include __DIR__ . '/resources/sampleFixtureData3.php';

        $query = new \org\gocdb\services\GetSite($this->em);
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => 'NOT(key=val)'), 5); 
         
        $sites = $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(s1p1=v1)'), 1); 
        $this->assertEquals('Site1', $sites[0]->getShortName());  
        $sites = $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(s1p1=)'), 1); 
        $this->assertEquals('Site1', $sites[0]->getShortName());  
        
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(s2p1=)(s2p2=)'), 1); 
        $sites = $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(s2p1=v1)(s2p2=v2)'), 1); 
        $this->assertEquals('Site2', $sites[0]->getShortName());  
        
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=)'), 2); 
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=foo)'), 2); 
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=)(VO2=)'), 2); 
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=foo)(VO2=)'), 2); 
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO2=)NOT(VO=foo)'), 0); 
        
        $sites = $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=)(VO2=)NOT(VO2=baz)'), 1); 
        $this->assertEquals('Site3', $sites[0]->getShortName());  
        
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=)(VO2=)NOT(VO2=)(VO=)'), 0); 
        
        
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => 'OR(VO2=bar)(VO2=baz)'), 2); 
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=food)OR(VO2=bar)(VO2=baz)'), 2); 
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=food)OR(VO2=bar)OR(VO2=baz)'), 2); 
        
        $sites = $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=food)OR(VO2=bar)OR(VO2=baz)AND(s4p1=v1)'), 1); 
        $this->assertEquals('Site4', $sites[0]->getShortName());  
        
        $sites = $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=food)(s4p1=v1)OR(VO2=bar)(VO2=baz)'), 2); 
        $this->assertEquals('Site3', $sites[0]->getShortName()); 
        $this->assertEquals('Site4', $sites[1]->getShortName()); 
        
        $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO=food)(s4p1=v1)OR(VO2=baz)AND(VO2=bling)'), 0); 
        
        $sites = $this->queryForIScopedEntity($query, array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO2=bing)AND(VO2=baz)AND(VO=bar)OR(s1p1=v1)'), 2); 
        $this->assertEquals('Site1', $sites[0]->getShortName()); 
        $this->assertEquals('Site4', $sites[1]->getShortName()); 

        /*$params = array('scope' => '', 'scope_match' => 'all', 'extensions' => '(VO2=bing)AND(VO2=baz)AND(VO=bar)OR(s1p1=v1)');
        $query->validateParameters($params);
        $dql = $query->createQuery();
        print $dql->getDql(); 
        $results = $query->executeQuery();
        print count($results);
        $this->assertTrue(2 == count($results));
        foreach ( $results as $site ) {
            print_r('site: ['.$site->getShortName().']'); 
        }
        */
    }



    private function queryForIScopedEntity(\org\gocdb\services\IPIQuery $query, $params, $expectedCount) {
        $query->validateParameters($params);
        $query->createQuery();
        $results = $query->executeQuery();
        $this->assertTrue($expectedCount == count($results));
        return $results; 
    }

}

//close class
?>
