<?php

use Marquine\Etl\Container;

$container = Container::getInstance();

// Database
$container->singleton(Marquine\Etl\Database\Manager::class);
$container->alias(Marquine\Etl\Database\Manager::class, 'db');

// Extractors
$container->bind('collection_extractor', Marquine\Etl\Extractors\Collection::class);
$container->bind('csv_extractor', Marquine\Etl\Extractors\Csv::class);
$container->bind('fixed_width_extractor', Marquine\Etl\Extractors\FixedWidth::class);
$container->bind('json_extractor', Marquine\Etl\Extractors\Json::class);
$container->bind('query_extractor', Marquine\Etl\Extractors\Query::class);
$container->bind('table_extractor', Marquine\Etl\Extractors\Table::class);
$container->bind('xml_extractor', Marquine\Etl\Extractors\Xml::class);

// Transformers
$container->bind('callback_transformer', Marquine\Etl\Transformers\Callback::class);
$container->bind('convert_case_transformer', Marquine\Etl\Transformers\ConvertCase::class);
$container->bind('json_decode_transformer', Marquine\Etl\Transformers\JsonDecode::class);
$container->bind('json_encode_transformer', Marquine\Etl\Transformers\JsonEncode::class);
$container->bind('number_format_transformer', Marquine\Etl\Transformers\NumberFormat::class);
$container->bind('pad_transformer', Marquine\Etl\Transformers\Pad::class);
$container->bind('rename_columns_transformer', Marquine\Etl\Transformers\RenameColumns::class);
$container->bind('replace_transformer', Marquine\Etl\Transformers\Replace::class);
$container->bind('trim_transformer', Marquine\Etl\Transformers\Trim::class);
$container->bind('unique_rows_transformer', Marquine\Etl\Transformers\UniqueRows::class);

// Loaders
$container->bind('insert_loader', Marquine\Etl\Loaders\Insert::class);
$container->bind('insert_update_loader', Marquine\Etl\Loaders\InsertUpdate::class);
