<?php

namespace Ngekoding\CodeIgniterApiQueryParser;

class Request
{
    /**
     * Get request value from $_GET by key.
     * 
     * @param string $key     The key
     * @param mixed  $default The default value if the parameter key does not exist
     * 
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
}
