
This repository contains the following components to get you started,

 *  PHP 8.3 Docker Image
 *  Laravel 11 Application
 *  PHPUnit
  * 
#### Creating new Migrations
You can run the following command to create a new Doctrine Diff Migration file, this will take care of removing any extra CURRENT_TIMESTAMP issues that Doctrine doesn't handle by default.  
```
composer create-migration
```
#### Running PHPUnit Tests
This framework as the relevant testing helpers and listeners already defined to get up and running with PHPUnit testing, alongside Doctrine.  
Make sure you have correctly populated the `DATABASE_NAME` variable within `phpunit.xml`.  
NOTE: This should differ to your actual database name (consider appending _testing to the name), as this will be recreated whenever the test suite is ran.  
```
vendor/bin/phpunit tests
```