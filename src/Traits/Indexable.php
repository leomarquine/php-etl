<?php

namespace Marquine\Etl\Traits;

trait Indexable
{
    /**
     * Make the array indexes equals to the provided key's values.
     *
     * @param array $items
     * @param array $keys
     * @return array
     */
    protected function index($items, $keys)
    {
        $results = [];

        $keys = array_flip($keys);

        foreach ($items as $item) {
            $key = implode('', array_intersect_key($item, $keys));

            if ($key == '') {
                $key = $this->guid();
            }

            $results[$key] = $item;
        }

        return $results;
    }

    /**
     * Generate a GUID
     *
     * @return string
     */
    protected function guid()
    {
        $uuid = openssl_random_pseudo_bytes(16);
        // set variant
        $uuid[8] = chr(ord($uuid[8]) & 0x39 | 0x80);
        // set version
        $uuid[6] = chr(ord($uuid[6]) & 0xf | 0x40);

        return preg_replace(
            '/(\w{8})(\w{4})(\w{4})(\w{4})(\w{12})/',
            '$1-$2-$3-$4-$5',
            bin2hex($uuid)
        );
    }
}
