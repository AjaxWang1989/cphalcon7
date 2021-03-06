<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

use Phalcon\Mvc\Model\Query as Query;

class Issue_2019 extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return "issue_2019";
	}
}

class ModelsQueryExecuteTest extends PHPUnit_Framework_TestCase
{

	public function __construct()
	{
		spl_autoload_register(array($this, 'modelsAutoloader'));
	}

	public function __destruct()
	{
		spl_autoload_unregister(array($this, 'modelsAutoloader'));
	}

	public function modelsAutoloader($className)
	{
		$className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
		$path = 'unit-tests/models/'.$className.'.php';
		if (file_exists($path)) {
			require $path;
		}
	}

	protected function _getDI()
	{

		Phalcon\DI::reset();

		$di = new Phalcon\DI();

		$di->set('modelsManager', function() {
			return new Phalcon\Mvc\Model\Manager();
		});

		$di->set('modelsMetadata', function() {
			return new Phalcon\Mvc\Model\Metadata\Memory();
		});

		$di->set('modelsQuery', 'Phalcon\Mvc\Model\Query');
		$di->set('modelsQueryBuilder', 'Phalcon\Mvc\Model\Query\Builder');
		$di->set('modelsCriteria', 'Phalcon\\Mvc\\Model\\Criteria');

		return $di;
	}

	public function testExecuteMysql()
	{
		require 'unit-tests/config.db.php';
		if (empty($configMysql)) {
			$this->markTestSkipped("Skipped");
			return;
		}

		$di = $this->_getDI();

		$di->set('db', function() {
			require 'unit-tests/config.db.php';
			return new Phalcon\Db\Adapter\Pdo\Mysql($configMysql);
		}, true);

		$this->_testSelectExecute($di);
		$this->_testSelectRenamedExecute($di);
		$this->_testInsertExecute($di);
		$this->_testInsertRenamedExecute($di);
		$this->_testUpdateExecute($di);
		$this->_testUpdateRenamedExecute($di);
		$this->_testDeleteExecute($di);
		$this->_testDeleteRenamedExecute($di);

		$this->_testIssue2019($di);
		$this->_testIssue1803($di);
	}

	public function testExecutePostgresql()
	{
		require 'unit-tests/config.db.php';
		if (empty($configPostgresql)) {
			$this->markTestSkipped("Skipped");
			return;
		}

		$di = $this->_getDI();

		$di->set('db', function() {
			require 'unit-tests/config.db.php';
			return new Phalcon\Db\Adapter\Pdo\Postgresql($configPostgresql);
		}, true);

		$this->_testSelectExecute($di);
		$this->_testSelectRenamedExecute($di);
		$this->_testInsertExecute($di);
		$this->_testInsertRenamedExecute($di);
		$this->_testUpdateExecute2($di);
		$this->_testUpdateRenamedExecute2($di);
		$this->_testDeleteExecute2($di);
		$this->_testDeleteRenamedExecute($di);

	}

	public function testExecuteSqlite()
	{
		require 'unit-tests/config.db.php';
		if (empty($configSqlite)) {
			$this->markTestSkipped("Skipped");
			return;
		}

		$di = $this->_getDI();

		$di->set('db', function() {
			require 'unit-tests/config.db.php';
			return new Phalcon\Db\Adapter\Pdo\Sqlite($configSqlite);
		}, true);

		$this->_testSelectExecute($di);
		$this->_testSelectRenamedExecute($di);
		$this->_testInsertExecute($di);
		$this->_testInsertRenamedExecute($di);
		$this->_testUpdateExecute($di);
		$this->_testUpdateRenamedExecute($di);
		$this->_testDeleteExecute($di);
		$this->_testDeleteRenamedExecute($di);

	}

	public function _testIssue2019($di)
	{
		$manager = $di->getShared('modelsManager');
		$di->getShared('db')->delete("issue_2019");

		//Test insert with single field
		$status = $manager->executeQuery('INSERT INTO Issue_2019 (column) VALUES (:column:)', array(
			"column" => "yeahyeah@hotmail.com",
		));
		$this->assertTrue($status->success() > 0);
	}

	public function _testSelectExecute($di)
	{

		$manager = $di->getShared('modelsManager');

		$robots = $manager->executeQuery('SELECT * FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertEquals(count($robots), 3);
		$this->assertInstanceOf('Robots', $robots[0]);

		$robots = $manager->executeQuery('SELECT * FROM Robots ORDER BY 1');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertEquals(count($robots), 3);
		$this->assertInstanceOf('Robots', $robots[0]);
		$this->assertEquals($robots[0]->id, 1);

		$robots = $manager->executeQuery('SELECT * FROM Robots ORDER BY id');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertEquals(count($robots), 3);
		$this->assertInstanceOf('Robots', $robots[0]);
		$this->assertEquals($robots[0]->id, 1);

		$robots = $manager->executeQuery('SELECT * FROM Robots ORDER BY Robots.id');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertEquals(count($robots), 3);
		$this->assertInstanceOf('Robots', $robots[0]);
		$this->assertEquals($robots[0]->id, 1);

		$robots = $manager->executeQuery('SELECT Robots.* FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertInstanceOf('Robots', $robots[0]);
		$this->assertEquals(count($robots), 3);

		$robots = $manager->executeQuery('SELECT r.* FROM Robots r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertInstanceOf('Robots', $robots[0]);
		$this->assertEquals(count($robots), 3);

		$robots = $manager->executeQuery('SELECT * FROM Robots r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertInstanceOf('Robots', $robots[0]);
		$this->assertEquals(count($robots), 3);

		$robots = $manager->executeQuery('SELECT * FROM Robots AS r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertInstanceOf('Robots', $robots[0]);
		$this->assertEquals(count($robots), 3);

		$robots = $manager->executeQuery('SELECT * FROM Robots AS r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robots);
		$this->assertInstanceOf('Robots', $robots[0]);
		$this->assertEquals(count($robots), 3);

		$result = $manager->executeQuery('SELECT id, name FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);

		$result = $manager->executeQuery('SELECT Robots.name FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);

		$result = $manager->executeQuery('SELECT LENGTH(name) AS the_length FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->the_length));
		$this->assertEquals($result[0]->the_length, 8);

		$result = $manager->executeQuery('SELECT LENGTH(Robots.name) AS the_length FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->the_length));
		$this->assertEquals($result[0]->the_length, 8);

		$result = $manager->executeQuery('SELECT Robots.id+1 AS nextId FROM Robots WHERE id = 1');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robots.id+1 AS nextId FROM Robots WHERE id = ?0', array(0 => 1));
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robots.id+1 AS nextId FROM Robots WHERE id = :id:', array('id' => 1));
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robots.id+1 AS nextId FROM Robots WHERE id = "1"');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT r.year FROM Robots r WHERE TRIM(name) != "Robotina"');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);

		$result = $manager->executeQuery('SELECT Robots.id+1 AS nextId FROM Robots ORDER BY id');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(count($result), 3);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robots.id+1 AS nextId FROM Robots ORDER BY id LIMIT 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(count($result), 2);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robots.id+1 AS nextId FROM Robots ORDER BY id DESC LIMIT 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(count($result), 2);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 4);

		$result = $manager->executeQuery('SELECT r.name FROM Robots r ORDER BY r.name DESC LIMIT 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->name));
		$this->assertEquals($result[0]->name, 'Terminator');

		$result = $manager->executeQuery('SELECT name le_name FROM Robots ORDER BY name ASC LIMIT 4');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->le_name));
		$this->assertEquals($result[0]->le_name, 'Astro Boy');

		$result = $manager->executeQuery('SELECT r.name le_name FROM Robots r ORDER BY r.name ASC LIMIT 4');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->le_name));
		$this->assertEquals($result[0]->le_name, 'Astro Boy');

		$result = $manager->executeQuery('SELECT r.name le_name FROM Robots r ORDER BY r.name ASC LIMIT 1,2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->le_name));
		$this->assertEquals($result[0]->le_name, 'Robotina');

		$result = $manager->executeQuery('SELECT r.name le_name FROM Robots r ORDER BY r.name ASC LIMIT 2 OFFSET 1');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->le_name));
		$this->assertEquals($result[0]->le_name, 'Robotina');

		$result = $manager->executeQuery('SELECT r.type, COUNT(*) number FROM Robots r GROUP BY 1 ORDER BY r.type ASC');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals($result[1]->number, 2);

		$result = $manager->executeQuery('SELECT r.type, SUM(r.year-1000) age FROM Robots r GROUP BY 1 ORDER BY 2 DESC');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals($result[0]->age, 1924);

		$result = $manager->executeQuery('SELECT r.type, COUNT(*) number FROM Robots r GROUP BY 1 HAVING COUNT(*) = 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals($result[0]->number, 2);

		$result = $manager->executeQuery('SELECT r.type, COUNT(*) number FROM Robots r GROUP BY 1 HAVING COUNT(*) < 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(count($result), 1);
		$this->assertEquals($result[0]->number, 1);

		//Cast function
		//TODO: CHAR in postgresql is acually a single char, but I can't specify a length
		//for the field type like CHAR(10) because phalcon phql doesn't support it
		$cast_type = "CHAR";
		if ($di->get("db") instanceof Phalcon\Db\Adapter\Pdo\Postgresql)
			$cast_type = "TEXT";
		$result = $manager->executeQuery("SELECT CAST(year AS $cast_type) test FROM Robots LIMIT 1");
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(Robots::findFirst()->year, $result[0]->test);

		//Convert using... is supported only by mysql (and it's in the sql standards, bad postgresql/sqlite!)
		if ($di->get("db") instanceof Phalcon\Db\Adapter\Pdo\Mysql)
		{
			$result = $manager->executeQuery("SELECT CONVERT(year USING utf8) test FROM Robots LIMIT 1");
			$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
			$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
			$this->assertEquals(Robots::findFirst()->year, $result[0]->test);
		}

		//Nested Cast
		$result = $manager->executeQuery("SELECT CAST(CAST(year AS $cast_type) AS DECIMAL) test FROM Robots LIMIT 1");
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(Robots::findFirst()->year, $result[0]->test);

		$result = $manager->executeQuery('SELECT r.id, r.* FROM Robots r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertNotEquals(gettype($result[0]->id), 'object');
		$this->assertEquals(gettype($result[0]->r), 'object');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->id, 1);

		$result = $manager->executeQuery('SELECT Robots.*, RobotsParts.* FROM Robots JOIN RobotsParts ORDER BY Robots.id, RobotsParts.id');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->robots), 'object');
		$this->assertEquals(get_class($result[0]->robots), 'Robots');
		$this->assertEquals(gettype($result[0]->robotsParts), 'object');
		$this->assertEquals(get_class($result[0]->robotsParts), 'RobotsParts');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->robots->id, 1);
		$this->assertEquals($result[0]->robotsParts->id, 1);
		$this->assertEquals($result[1]->robots->id, 1);
		$this->assertEquals($result[1]->robotsParts->id, 2);

		$result = $manager->executeQuery('SELECT Robots.*, RobotsParts.* FROM Robots JOIN RobotsParts ON Robots.id = RobotsParts.robots_id ORDER BY Robots.id, RobotsParts.id');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->robots), 'object');
		$this->assertEquals(get_class($result[0]->robots), 'Robots');
		$this->assertEquals(gettype($result[0]->robotsParts), 'object');
		$this->assertEquals(get_class($result[0]->robotsParts), 'RobotsParts');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->robots->id, 1);
		$this->assertEquals($result[0]->robotsParts->id, 1);
		$this->assertEquals($result[1]->robots->id, 1);
		$this->assertEquals($result[1]->robotsParts->id, 2);

		$result = $manager->executeQuery('SELECT r.*, p.* FROM Robots r JOIN RobotsParts p ON r.id = p.robots_id ORDER BY r.id, p.id');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->r), 'object');
		$this->assertEquals(get_class($result[0]->r), 'Robots');
		$this->assertEquals(gettype($result[0]->p), 'object');
		$this->assertEquals(get_class($result[0]->p), 'RobotsParts');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->r->id, 1);
		$this->assertEquals($result[0]->p->id, 1);
		$this->assertEquals($result[1]->r->id, 1);
		$this->assertEquals($result[1]->p->id, 2);

		//Joins with namespaces
		$result = $manager->executeQuery('SELECT Some\Robots.*, Some\RobotsParts.* FROM Some\Robots JOIN Some\RobotsParts ON Some\Robots.id = Some\RobotsParts.robots_id ORDER BY Some\Robots.id, Some\RobotsParts.id');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->{'some\Robots'}), 'object');
		$this->assertEquals(get_class($result[0]->{'some\Robots'}), 'Some\Robots');
		$this->assertEquals(gettype($result[0]->{'some\RobotsParts'}), 'object');
		$this->assertEquals(get_class($result[0]->{'some\RobotsParts'}), 'Some\RobotsParts');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->{'some\Robots'}->id, 1);
		$this->assertEquals($result[0]->{'some\RobotsParts'}->id, 1);
		$this->assertEquals($result[1]->{'some\Robots'}->id, 1);
		$this->assertEquals($result[1]->{'some\RobotsParts'}->id, 2);

		//Joins with namespaces and aliases
		$result = $manager->executeQuery('SELECT r.*, p.* FROM Some\Robots r JOIN Some\RobotsParts p ON r.id = p.robots_id ORDER BY r.id, p.id');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->r), 'object');
		$this->assertEquals(get_class($result[0]->r), 'Some\Robots');
		$this->assertEquals(gettype($result[0]->p), 'object');
		$this->assertEquals(get_class($result[0]->p), 'Some\RobotsParts');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->r->id, 1);
		$this->assertEquals($result[0]->p->id, 1);
		$this->assertEquals($result[1]->r->id, 1);
		$this->assertEquals($result[1]->p->id, 2);

		$result = $manager->executeQuery('SELECT id, name FROM Some\Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);

		$result = $manager->executeQuery('SELECT id, name FROM Some\Robots ORDER BY name DESC LIMIT 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);

		$result = $manager->executeQuery('SELECT ALL estado FROM Personas LIMIT 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$result = $manager->executeQuery('SELECT DISTINCT estado FROM Personas');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$result = $manager->executeQuery('SELECT count(DISTINCT estado) FROM Personas');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(count($result), 1);
		$this->assertEquals(2, $result[0]->{"0"});
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$result = $manager->executeQuery('SELECT CASE 1 WHEN 1 THEN 2 END FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(2, $result[0]->{"0"});
		$result = $manager->executeQuery('SELECT CASE 2 WHEN 1 THEN 2 WHEN 2 THEN 3 END FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(3, $result[0]->{"0"});
		$result = $manager->executeQuery('SELECT CASE 2 WHEN 1 THEN 2 ELSE 3 END FROM Robots');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(3, $result[0]->{"0"});

		// Issue 1011
		$result = $manager->executeQuery('SELECT r.name le_name FROM Robots r ORDER BY r.name ASC LIMIT ?1,?2', array(1 => 1, 2 => 2), array(1 => \Phalcon\Db\Column::BIND_PARAM_INT, 2 => \Phalcon\Db\Column::BIND_PARAM_INT));
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->le_name));
		$this->assertEquals($result[0]->le_name, 'Robotina');
	}

	public function _testSelectRenamedExecute($di)
	{
		$manager = $di->getShared('modelsManager');

		$robotters = $manager->executeQuery('SELECT * FROM Robotters');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertEquals(count($robotters), 3);
		$this->assertInstanceOf('Robotters', $robotters[0]);

		$robotters = $manager->executeQuery('SELECT * FROM Robotters ORDER BY 1');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertEquals(count($robotters), 3);
		$this->assertInstanceOf('Robotters', $robotters[0]);
		$this->assertEquals($robotters[0]->code, 1);

		$robotters = $manager->executeQuery('SELECT * FROM Robotters ORDER BY code');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertEquals(count($robotters), 3);
		$this->assertInstanceOf('Robotters', $robotters[0]);
		$this->assertEquals($robotters[0]->code, 1);

		$robotters = $manager->executeQuery('SELECT * FROM Robotters ORDER BY Robotters.code');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertEquals(count($robotters), 3);
		$this->assertInstanceOf('Robotters', $robotters[0]);
		$this->assertEquals($robotters[0]->code, 1);

		$robotters = $manager->executeQuery('SELECT Robotters.* FROM Robotters');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertInstanceOf('Robotters', $robotters[0]);
		$this->assertEquals(count($robotters), 3);

		$robotters = $manager->executeQuery('SELECT r.* FROM Robotters r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertInstanceOf('Robotters', $robotters[0]);
		$this->assertEquals(count($robotters), 3);

		$robotters = $manager->executeQuery('SELECT * FROM Robotters r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertInstanceOf('Robotters', $robotters[0]);
		$this->assertEquals(count($robotters), 3);

		$robotters = $manager->executeQuery('SELECT * FROM Robotters AS r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertInstanceOf('Robotters', $robotters[0]);
		$this->assertEquals(count($robotters), 3);

		$robotters = $manager->executeQuery('SELECT * FROM Robotters AS r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $robotters);
		$this->assertInstanceOf('Robotters', $robotters[0]);
		$this->assertEquals(count($robotters), 3);

		$result = $manager->executeQuery('SELECT Robotters.theName FROM Robotters');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);

		$result = $manager->executeQuery('SELECT LENGTH(Robotters.theName) AS the_length FROM Robotters');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->the_length));
		$this->assertEquals($result[0]->the_length, 8);

		$result = $manager->executeQuery('SELECT Robotters.code+1 AS nextId FROM Robotters WHERE code = 1');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robotters.code+1 AS nextId FROM Robotters WHERE code = ?0', array(0 => 1));
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robotters.code+1 AS nextId FROM Robotters WHERE code = :code:', array('code' => 1));
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robotters.code+1 AS nextId FROM Robotters WHERE code = "1"');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT r.theYear FROM Robotters r WHERE TRIM(theName) != "Robotina"');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);

		$result = $manager->executeQuery('SELECT Robotters.code+1 AS nextId FROM Robotters ORDER BY code');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(count($result), 3);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robotters.code+1 AS nextId FROM Robotters ORDER BY code LIMIT 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(count($result), 2);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 2);

		$result = $manager->executeQuery('SELECT Robotters.code+1 AS nextId FROM Robotters ORDER BY code DESC LIMIT 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(count($result), 2);
		$this->assertTrue(isset($result[0]->nextId));
		$this->assertEquals($result[0]->nextId, 4);

		$result = $manager->executeQuery('SELECT r.theName FROM Robotters r ORDER BY r.theName DESC LIMIT 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->theName));
		$this->assertEquals($result[0]->theName, 'Terminator');

		$result = $manager->executeQuery('SELECT r.theName le_theName FROM Robotters r ORDER BY r.theName ASC LIMIT 4');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 3);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->le_theName));
		$this->assertEquals($result[0]->le_theName, 'Astro Boy');

		$result = $manager->executeQuery('SELECT r.theName le_theName FROM Robotters r ORDER BY r.theName ASC LIMIT 1,2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->le_theName));
		$this->assertEquals($result[0]->le_theName, 'Robotina');

		$result = $manager->executeQuery('SELECT r.theName le_theName FROM Robotters r ORDER BY r.theName ASC LIMIT 2 OFFSET 1');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertTrue(isset($result[0]->le_theName));
		$this->assertEquals($result[0]->le_theName, 'Robotina');

		$result = $manager->executeQuery('SELECT r.theType, COUNT(*) number FROM Robotters r GROUP BY 1 ORDER BY r.theType ASC');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals($result[1]->number, 2);

		$result = $manager->executeQuery('SELECT r.theType, SUM(r.theYear-1000) age FROM Robotters r GROUP BY 1 ORDER BY 2 DESC');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 2);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals($result[0]->age, 1924);

		$result = $manager->executeQuery('SELECT r.theType, COUNT(*) number FROM Robotters r GROUP BY 1 HAVING COUNT(*) = 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertEquals(count($result), 1);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals($result[0]->number, 2);

		$result = $manager->executeQuery('SELECT r.theType, COUNT(*) number FROM Robotters r GROUP BY 1 HAVING COUNT(*) < 2');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);
		$this->assertInstanceOf('Phalcon\Mvc\Model\Row', $result[0]);
		$this->assertEquals(count($result), 1);
		$this->assertEquals($result[0]->number, 1);

		$result = $manager->executeQuery('SELECT r.code, r.* FROM Robotters r');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertNotEquals(gettype($result[0]->code), 'object');
		$this->assertEquals(gettype($result[0]->r), 'object');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->code, 1);

		$result = $manager->executeQuery('SELECT Robotters.*, RobottersDeles.* FROM Robotters JOIN RobottersDeles ORDER BY Robotters.code, RobottersDeles.code');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->robotters), 'object');
		$this->assertEquals(get_class($result[0]->robotters), 'Robotters');
		$this->assertEquals(gettype($result[0]->robottersDeles), 'object');
		$this->assertEquals(get_class($result[0]->robottersDeles), 'RobottersDeles');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->robotters->code, 1);
		$this->assertEquals($result[0]->robottersDeles->code, 1);
		$this->assertEquals($result[1]->robotters->code, 1);
		$this->assertEquals($result[1]->robottersDeles->code, 2);

		$result = $manager->executeQuery('SELECT Robotters.*, RobottersDeles.* FROM Robotters JOIN RobottersDeles ON Robotters.code = RobottersDeles.robottersCode ORDER BY Robotters.code, RobottersDeles.code');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->robotters), 'object');
		$this->assertEquals(get_class($result[0]->robotters), 'Robotters');
		$this->assertEquals(gettype($result[0]->robottersDeles), 'object');
		$this->assertEquals(get_class($result[0]->robottersDeles), 'RobottersDeles');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->robotters->code, 1);
		$this->assertEquals($result[0]->robottersDeles->code, 1);
		$this->assertEquals($result[1]->robotters->code, 1);
		$this->assertEquals($result[1]->robottersDeles->code, 2);

		$result = $manager->executeQuery('SELECT r.*, p.* FROM Robotters r JOIN RobottersDeles p ON r.code = p.robottersCode ORDER BY r.code, p.code');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->r), 'object');
		$this->assertEquals(get_class($result[0]->r), 'Robotters');
		$this->assertEquals(gettype($result[0]->p), 'object');
		$this->assertEquals(get_class($result[0]->p), 'RobottersDeles');
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->r->code, 1);
		$this->assertEquals($result[0]->p->code, 1);
		$this->assertEquals($result[1]->r->code, 1);
		$this->assertEquals($result[1]->p->code, 2);

		$result = $manager->executeQuery('SELECT r.* FROM Robots r WHERE r.id NOT IN (SELECT p.id FROM Parts p WHERE r.id < p.id)');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Simple', $result);

		$result = $manager->executeQuery('SELECT * FROM Robots r JOIN RobotsParts rp WHERE rp.id IN (SELECT p.id FROM Parts p WHERE rp.parts_id = p.id)');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);

		$result = $manager->executeQuery('SELECT * FROM Robots r JOIN RobotsParts rp WHERE r.id IN (SELECT p.id FROM Parts p)');
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
	}

	public function _testInsertExecute($di)
	{

		$manager = $di->getShared('modelsManager');

		$di->getShared('db')->delete("subscriptores");

		$status = $manager->executeQuery('INSERT INTO Subscriptores (email, created_at, status) VALUES ("hideaway@hotmail.com", "2010-01-01 13:21:00", "P")');
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('INSERT INTO Subscriptores (email, created_at, status) VALUES (:email:, :created_at:, :status:)', array(
			"email" => "yeahyeah@hotmail.com",
			"created_at" => "2010-02-01 13:21:00",
			"status" => "P"
		));
		$this->assertTrue($status->success() > 0);
	}

	public function _testInsertRenamedExecute($di)
	{

		$manager = $di->getShared('modelsManager');

		$di->getShared('db')->delete("subscriptores");

		/**
		 * This test must pass
		 */
		$status = $manager->executeQuery('INSERT INTO Abonnes (courrierElectronique, creeA, statut) VALUES ("hideaway@hotmail.com", "2010-01-01 13:21:00", "P")');
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('INSERT INTO Abonnes (courrierElectronique, creeA, statut) VALUES (:courrierElectronique:, :creeA:, :statut:)', array(
			"courrierElectronique" => "yeahyeah@hotmail.com",
			"creeA" => "2010-02-01 13:21:00",
			"statut" => "P"
		));
		$this->assertTrue($status->success() > 0);

	}

	public function _testUpdateExecute($di)
	{

		$manager = $di->getShared('modelsManager');

		$di->getShared('db')->execute("UPDATE personas SET ciudad_id = NULL WHERE direccion = 'COL'");

		$status = $manager->executeQuery("UPDATE People SET direccion = 'COL' WHERE ciudad_id IS NULL LIMIT 25");
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('UPDATE People SET direccion = :direccion: WHERE ciudad_id IS NULL LIMIT 25', array(
			"direccion" => "MXN"
		));
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('UPDATE Subscriptores SET status = :status: WHERE email = :email:', array(
			"status" => "I",
			"email" => "le-marina@hotmail.com"
		));
		$this->assertTrue($status->success() > 0);

		// Issue 1011
		$status = $manager->executeQuery(
			'UPDATE Subscriptores SET status = :status: WHERE email = :email: LIMIT :limit:',
			array(
				"status" => "I",
				"email" => "le-marina@hotmail.com",
				"limit" => 1,
			),
			array('email' => \Phalcon\Db\Column::BIND_PARAM_STR, /*'limit' => \Phalcon\Db\Column::BIND_PARAM_INT*/)
		);
		$this->assertTrue($status->success() > 0);
	}

	public function _testUpdateExecute2($di)
	{

		$manager = $di->getShared('modelsManager');

		$di->getShared('db')->execute("UPDATE personas SET ciudad_id = NULL WHERE direccion = 'COL'");

		$status = $manager->executeQuery("UPDATE People SET direccion = 'COL' WHERE ciudad_id IS NULL");
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('UPDATE People SET direccion = :direccion: WHERE ciudad_id IS NULL', array(
			"direccion" => "MXN"
		));
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('UPDATE Subscriptores SET status = :status: WHERE email = :email:', array(
			"status" => "I",
			"email" => "le-marina@hotmail.com"
		));
		$this->assertTrue($status->success() > 0);

		// Issue 1011
		$status = $manager->executeQuery(
			'UPDATE Subscriptores SET status = :status: WHERE email = :email:',
			array(
				"status" => "I",
				"email" => "le-marina@hotmail.com",
			),
			array('email' => \Phalcon\Db\Column::BIND_PARAM_STR, /*'limit' => \Phalcon\Db\Column::BIND_PARAM_INT*/)
		);
		$this->assertTrue($status->success() > 0);
	}

	public function _testUpdateRenamedExecute($di)
	{

		$manager = $di->getShared('modelsManager');

		$di->getShared('db')->execute("UPDATE personas SET ciudad_id = NULL WHERE direccion = 'COL'");

		$status = $manager->executeQuery("UPDATE Personers SET adresse = 'COL' WHERE fodebyId IS NULL LIMIT 25");
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('UPDATE Personers SET adresse = :adresse: WHERE fodebyId IS NULL LIMIT 25', array(
			"adresse" => "MXN"
		));
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('UPDATE Abonnes SET statut = :statut: WHERE courrierElectronique = :courrierElectronique:', array(
			"statut" => "I",
			"courrierElectronique" => "le-marina@hotmail.com"
		));
		$this->assertTrue($status->success() > 0);

	}

	public function _testUpdateRenamedExecute2($di)
	{

		$manager = $di->getShared('modelsManager');

		$di->getShared('db')->execute("UPDATE personas SET ciudad_id = NULL WHERE direccion = 'COL'");

		$status = $manager->executeQuery("UPDATE Personers SET adresse = 'COL' WHERE fodebyId IS NULL");
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('UPDATE Personers SET adresse = :adresse: WHERE fodebyId IS NULL', array(
			"adresse" => "MXN"
		));
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('UPDATE Abonnes SET statut = :statut: WHERE courrierElectronique = :courrierElectronique:', array(
			"statut" => "I",
			"courrierElectronique" => "le-marina@hotmail.com"
		));
		$this->assertTrue($status->success() > 0);

	}

	public function _testDeleteExecute($di)
	{
		$manager = $di->getShared('modelsManager');

		$status = $manager->executeQuery('DELETE FROM Subscriptores WHERE email = "marina@hotmail.com"');
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('DELETE FROM Subscriptores WHERE status = :status: AND email <> :email:', array(
			'status' => "P",
			'email' => 'fuego@hotmail.com'
		));
		$this->assertTrue($status->success() > 0);

		// Issue 1011
		$status = $manager->executeQuery(
			'DELETE FROM Subscriptores WHERE status = :status: AND email <> :email: LIMIT :limit:',
			array(
				"status" => "P",
				"email" => "fuego@hotmail.com",
				"limit" => 1,
			),
			array('email' => \Phalcon\Db\Column::BIND_PARAM_STR,/* 'limit' => \Phalcon\Db\Column::BIND_PARAM_INT */)
		);
		$this->assertTrue($status->success() > 0);
	}

	public function _testDeleteExecute2($di)
	{
		$manager = $di->getShared('modelsManager');

		$status = $manager->executeQuery('DELETE FROM Subscriptores WHERE email = "marina@hotmail.com"');
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('DELETE FROM Subscriptores WHERE status = :status: AND email <> :email:', array(
			'status' => "P",
			'email' => 'fuego@hotmail.com'
		));
		$this->assertTrue($status->success() > 0);

		// Issue 1011
		$status = $manager->executeQuery(
			'DELETE FROM Subscriptores WHERE status = :status: AND email <> :email:',
			array(
				"status" => "P",
				"email" => "fuego@hotmail.com",
			),
			array('email' => \Phalcon\Db\Column::BIND_PARAM_STR)
		);
		$this->assertTrue($status->success() > 0);
	}

	public function _testDeleteRenamedExecute($di)
	{

		$manager = $di->getShared('modelsManager');

		$status = $manager->executeQuery('DELETE FROM Abonnes WHERE courrierElectronique = "marina@hotmail.com"');
		$this->assertTrue($status->success() > 0);

		$status = $manager->executeQuery('DELETE FROM Abonnes WHERE statut = :statut: AND courrierElectronique <> :courrierElectronique:', array(
			'statut' => "P",
			'courrierElectronique' => 'fuego@hotmail.com'
		));
		$this->assertTrue($status->success() > 0);

	}

	public function _testIssue1803($di)
	{
		$manager = $di->getShared('modelsManager');

		$result = $manager->executeQuery('SELECT r1.*, r2.*, r3.* FROM Robots AS r1 LEFT JOIN Robots AS r2 ON r2.id = r1.id LEFT JOIN Robots AS r3 ON r3.id = r1.id LIMIT 1');
		
		$this->assertInstanceOf('Phalcon\Mvc\Model\Resultset\Complex', $result);
		$this->assertEquals(gettype($result[0]->r1), 'object');
		$this->assertEquals(get_class($result[0]->r1), 'Robots');
		$this->assertEquals(count($result[0]), 3);
	}

}
