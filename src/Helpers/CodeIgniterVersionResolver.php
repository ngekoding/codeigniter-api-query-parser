<?php

namespace Ngekoding\CodeIgniterApiQueryParser\Helpers;

class CodeIgniterVersionResolver
{
    /**
     * Resolve CodeIgniter version
     * 
     * @param string|int|null $ciVersion
     * @return int
     */
    public static function resolve($ciVersion)
    {
        if ( ! in_array($ciVersion, [3, 4])) {
            if (class_exists(\CodeIgniter\Database\BaseBuilder::class)) {
                return 4;
            }
            return 3;
        }
        return (int) $ciVersion;
    }
}
