DBUnit Testing
==============
This file is best viewed using a browser-plugin for markdown `.md` files.

GocDB comes with a suite of tests that can be run to validate that Doctrine and 
your chosen database operate as expected. If the test suite fails against your 
chosen DB, then GocDB will not work as expected. It is therefore recommended 
that you run the DBUnit tests to ensure GocDB works as expected against your chosen DB.  

Install PhpUnit
The tests require PHPUnit and its DBUnit extensions. 
See [PhpUnit install](https://phpunit.de) and ensure phpunit is on your PATH. 
For Win users, you may need to create a .bat file with the following contents 
`php "%~dp0phpunit.phar" %*` 

```bash
$phpunit --version
PHPUnit 4.6.6 by Sebastian Bergmann and contributors.
```

Deploy A Database For Testing
--------------------------
* We ***STRONGLY*** recommend that you deploy a second database account/user with different 
connection details that will be used for testing (e.g. GOCDB5TEST).
* See the `gocdbSrc/INSTALL.md` file for details on creating a new DB account/user 
for your chosen DB. 

Configure Doctrine and PDO for your Test DB
-------------------------------------------
* Copy `tests/doctrine/bootstrap_doctrine_TEMPLATE.php` to `tests/doctrine/bootstrap_doctrine.php` 
(note same dir).
* Modify `bootstrap_doctrine.php` to specify your GOCDB5TEST account/user. 
* Copy `tests/doctrine/bootstrap_pdo_TEMPLATE.php` to `tests/doctrine/bootstrap_pdo.php` 
  and modify the connection details for the ***same test database***. This second 
  file is used to assert that Doctrine performs the GocDB logic against your 
  DB in the expected way. It uses Php Data Objects (PDO) to connect to your DB 
  and runs plain SQL to perform assertions.     

Deploy Tables/Schema via Doctrine
---------------------------------------
See the `<gocdbSrc>/INSTALL.md` file for Doctrine installation. 
Use the doctrine command line tool to test doctrine's connection to the DB, 
then drop and create the DB schema using the doctrine orm:schema-tool:

```bash 
$ cd tests/doctrine
$ doctrine orm:schema-tool:drop --dump-sql
... sql shown here
$ doctrine orm:schema-tool:drop --force
Database schema dropped successfully!
$ doctrine orm:schema-tool:create
Creating database schema...
Database schema created successfully!
```

Running the Test Suite
----------------------
A test suite is provided that will test many core functions of GocDB and Doctrine. 
This suite itself executes a number of individual tests and can be ran using the command:

```bash 
$ cd tests
$ phpunit DoctrineTestSuite1.php 
```

Running Individual Tests
------------------------
Rather than running the whole test suite, individual tests can be run, e.g:

```
$ cd tests
$ phpunit doctrine/<testName>.php
```