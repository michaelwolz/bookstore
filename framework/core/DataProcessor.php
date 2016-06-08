<?php

namespace maw\core;

class DataProcessor
{
    public static function getJSONAsArray($file) {
        if (file_exists($file))
            return json_decode(file_get_contents($file));
        else
            return null;
    }
}