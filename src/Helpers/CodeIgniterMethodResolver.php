<?php

namespace Ngekoding\CodeIgniterApiQueryParser\Helpers;

class CodeIgniterMethodResolver
{
    protected $ciVersion;

    /**
     * Map the method to call base on CodeIgniter version
     * We use version 4 as the references name
     */
    protected $methodsMapping = [
        3 => [
            'countAllResults' => 'count_all_results',
            'orderBy' => 'order_by',
            'where' => 'where',
            'whereIn' => 'where_in',
            'limit' => 'limit',
            'get' => 'get',
            'QBSelect' => 'qb_select',
            'getFieldNames' => 'list_fields',
            'getResult' => 'result',
            'getResultArray' => 'result_array',
        ]
    ];

    public function __construct($ciVersion = 4)
    {
        $this->ciVersion = $ciVersion;
    }

    /**
     * Get method name
     * 
     * @param string $name
     * 
     * @return string
     */
    public function get($name)
    {
        if (isset($this->methodsMapping[$this->ciVersion])) {
            return $this->methodsMapping[$this->ciVersion][$name];
        }
        return $name;
    }
}
