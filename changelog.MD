# Changelog

## Changes for future versions
* Refactor the code where the PHP function extract() is used

## Changes for release 1.0 (the first one since the fork)

* Remove all the old DI system of Laravel (Illuminate):
*   * to make it fully compatible with any PHP Application
    * to remove bad practices and rely only on DI instead of manual instanciation
* Now you have to provide the ETL component instead of calling them with a string
* Added a _setAttributes()_ method to the Row object
* Added a _clearAttributes()_ method to the Row object
* Changed all the namespaces to Wizaplace
* Upgraded to PHPUnit 8
* Fixed tests to remove deprecation to make them compatible with PHPUnit 9
* Added strong typehinting for variables wherever possible
* Added strong typehinting for return type wherever possible
* Switched a lot of method from public visibility to protected
* Using PHPCSFIXER to clean the code
* Validation with PHPSTAN, level 6, however without:
    * checkMissingIterableValueType
    * checkGenericClassInNonGenericObjectType
* Added @TODO warnings where the extract() php function is used. This part will need to be refactored
* Dropped compatibility with PHP < 7.2
