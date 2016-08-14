<?php

namespace Marquine\Metis\Database\Connectors;

use PDO;

abstract class Connector
{
    /**
    * The default PDO connection options.
    *
    * @var array
    */
   protected $options = [
       PDO::ATTR_CASE => PDO::CASE_NATURAL,
       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
       PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
       PDO::ATTR_STRINGIFY_FETCHES => false,
       PDO::ATTR_EMULATE_PREPARES => true,
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
   ];

   // TODO: method to merge custom user options

   abstract public function connect($config);


}
