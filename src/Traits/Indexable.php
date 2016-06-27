<?php

namespace Marquine\Metis\Traits;

trait Indexable
{
    /**
     * Make the array indexes equals to the provided key's values.
     *
     * @param  array $items
     * @param  array $keys
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
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
            mt_rand(16384, 20479), mt_rand(32768, 49151),
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
       );
    }
}
