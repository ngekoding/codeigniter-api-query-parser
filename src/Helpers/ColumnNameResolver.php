<?php

namespace Ngekoding\CodeIgniterApiQueryParser\Helpers;

use PHPSQLParser\PHPSQLParser;

class ColumnNameResolver
{
    protected $queryBuilder;
    protected $methodResolver;

    public function __construct($queryBuilder, CodeIgniterMethodResolver $methodResolver)
    {
        $this->queryBuilder = $queryBuilder;
        $this->methodResolver = $methodResolver;
    }

    /**
     * Get column aliases from query builder statement
     * 
     * @return array
     */
    public function getColumnAliases()
    {
        $replection = new \ReflectionProperty($this->queryBuilder, $this->methodResolver->get('QBSelect'));
        $replection->setAccessible(TRUE);

        $qbSelect = $replection->getValue($this->queryBuilder);

        if (empty($qbSelect)) return [];

        $sql = 'SELECT '.implode(', ', $qbSelect);
        $parser = new PHPSQLParser();
        $parsed = $parser->parse($sql);

        $columnAliases = [];
        foreach ($parsed['SELECT'] as $select) {
            if ($select['alias']) {
                $alias = $select['alias']['name'];
                if ($select['expr_type'] == 'colref') {
                    $key = $select['base_expr'];
                } elseif (strpos($select['expr_type'], 'function') !== FALSE) {
                    $parts = [];
                    foreach ($select['sub_tree'] as $part) {
                        $parts[] = $part['base_expr'];
                    }
                    $key =  $select['base_expr'].'('.implode(', ', $parts).')';
                }
                $columnAliases[$alias] = $key;
            }
        }

        return $columnAliases;
    }
}
