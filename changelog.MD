# Changelog

## 2.0

**New features**
* Improvement of the aggregator. Now an exception can be thrown if at the end of the iteration there are some remaining rows (optional). If this option is not enabled, the remaining rows are flagged as 'incomplete' and yield as before.
* Improvement of the CsvLoader: you can now ask to output the result in one single file.
* Improvement of the CsvExtractor: it uses the header of the CSV file to create the array, so the key is the corresponding column name.
* Improvement of the Transaction object along with the Insert and InsertUpdate loaders:
  * If using transactions, any row should open a PDO transaction if one is not already open.
  * If a Transaction class is destroyed, any open transactions should be closed.
  * Commit transactions in progress on error.
* Added a RowCallback transformer.
* Added a new method _pull()_ to get an attribute from the row and immediately remove it.


**Bugfixes**
*  Fix bug in calculating quarters in the DateDimension generator. Default date range starts on January 1 so it should end on December 31 to be non-overlapping.


**Miscellaneous**
* Dropped support for PHP < 7.4.
* Strong typing everywhere when possible.

Thanks to the contribution of :
* [kdebisschop](https://github.com/kdebisschop).
* [devfrey](https://github.com/devfrey).

## 1.3

**New features**
* Added PHP 8 support, but we keep the compatibility down to PHP 7.2.
* Added a CallbackTransformer. Contribution by [kdebisschop](https://github.com/kdebisschop).
* Added a CopycolumnsTransformer. Contribution by [kdebisschop](https://github.com/kdebisschop).
* Added a transformer to format UnixTimestamps into human readable dates. Contribution by [kdebisschop](https://github.com/kdebisschop).
* Added an extractor that call a generator callback. Contribution by [kdebisschop](https://github.com/kdebisschop).

**Bufixes**
* Fix inconsistency between Insert and InsertUpdate when selecting columns. Contribution by [kdebisschop](https://github.com/kdebisschop).
* Fix on the Row object: the support for PHP 8 highlighted a bad return type on the magic setters. This fix could lead to a BC if the developer rely on it.

**Miscellaneous**
* Tests are now covered by quality profiles. Contribution by [kdebisschop](https://github.com/kdebisschop).
* Fix various issues with precommit checks. Contribution by [kdebisschop](https://github.com/kdebisschop).

## 1.2.1

**Miscellaneous**
* Fix CI: utilization of the library with PHP 7.2 and 7.3 requires composer 1.x while an utilization with PHP 7.4
can work with Composer 2. This requirement comes from the _ocramius/package-versions_ package, a dependency of
_infection/infection_. This limitation should be removed in the next release with dropping the support of < PHP 7.4.

## 1.2

**New features**
* Add a _DateDimension_ feature with its own extractor. Contribution by [kdebisschop](https://github.com/kdebisschop).
* In the _DatabaseLoader_, add the possibility to skip updates. Contribution by [kdebisschop](https://github.com/kdebisschop).
* Add a _ColumnFilterTransformer_. Contribution by [ArthurHoaro](https://github.com/ArthurHoaro).
* Add a _MemoryLoader_. Contribution by [ArthurHoaro](https://github.com/ArthurHoaro).

**Miscellaneous**
* Fix PHPStan failures and cleanup. Add a new _XmlException_. Contribution by [kdebisschop](https://github.com/kdebisschop).
* PHPCS cleanup and force more strong typehinting. Contribution by [kdebisschop](https://github.com/kdebisschop).

## 1.1

**New features**
* Throw an exception when the CSV source file is not found during extraction.
* Add an optional parameter on the CSV extractor: if set to true it will raise an exception when trying to access non existing columns.
* Add a CSV loader, to be able to ETL from a CSV to another.
* Add the possibility to use multiple extractors and combine them.

**Bufixes**
* Fix an old bug on multilines CSV extraction.
* Fix on ignored discarded rows (only the loader was ignoring them).
* On the ConvertCase transformer : fix to properly handle the change of behavior between PHP 7.2 and 7.3.

**Miscellaneous**
* Improve CI with quality tools (PHPCS, PHPStan...).
* CI: unit tests with PHP 7.2 to 7.4.
* Code cleanup to progressively comply with modern standards.


## Changes for the release 1.0 (the first one since the fork)

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
