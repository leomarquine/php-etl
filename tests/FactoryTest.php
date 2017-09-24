<?php

namespace Tests;

use Tests\TestCase;
use Marquine\Etl\Factory;
use InvalidArgumentException;
use Marquine\Etl\Loaders\LoaderInterface;
use Marquine\Etl\Extractors\ExtractorInterface;
use Marquine\Etl\Transformers\TransformerInterface;

class FactoryTest extends TestCase
{
    /** @test */
    function extractor()
    {
        $extractor = Factory::extractor(FactoryFakeExtractor::class, ['property' => 'value']);

        $this->assertInstanceOf(ExtractorInterface::class, $extractor);
        $this->assertEquals($extractor->property, 'value');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Extractor must implement 'Marquine\Etl\Extractors\ExtractorInterface");

        Factory::extractor('Random\Class');
    }

    /** @test */
    function transformer()
    {
        $transformer = Factory::transformer(FactoryFakeTransformer::class, ['property' => 'value']);

        $this->assertInstanceOf(TransformerInterface::class, $transformer);
        $this->assertEquals($transformer->property, 'value');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Transformer must implement 'Marquine\Etl\Transformers\TransformerInterface");

        Factory::transformer('Random\Class');
    }

    /** @test */
    function loader()
    {
        $loader = Factory::loader(FactoryFakeLoader::class, ['property' => 'value']);

        $this->assertInstanceOf(LoaderInterface::class, $loader);
        $this->assertEquals($loader->property, 'value');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Loader must implement 'Marquine\Etl\Loaders\LoaderInterface");

        Factory::loader('Random\Class');
    }
}


class FactoryFakeExtractor implements ExtractorInterface {
    public $property;
    public function extract($source) {}
}

class FactoryFakeTransformer implements TransformerInterface {
    public $property;
    public function handler() {}
}

class FactoryFakeLoader implements LoaderInterface {
    public $property;
    public function load($destination, $items) {}
}
