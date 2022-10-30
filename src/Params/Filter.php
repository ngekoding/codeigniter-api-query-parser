<?php

namespace Ngekoding\CodeIgniterApiQueryParser\Params;

class Filter
{
    protected $field;
    protected $operator;
    protected $value;

    public function __construct($field, $operator, $value)
    {
        $this->setField($field);
        $this->setOperator($operator);
        $this->setValue($value);
    }

    /**
     * Get the value of field
     * 
     * @return string
     */ 
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set the value of field
     *
     * @return self
     */ 
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get the value of operator
     */ 
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set the value of operator
     *
     * @return self
     */ 
    public function setOperator($operator)
    {
        $this->operator = strtolower($operator);

        return $this;
    }

    /**
     * Get the value of value
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return self
     */ 
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
