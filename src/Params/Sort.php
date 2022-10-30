<?php

namespace Ngekoding\CodeIgniterApiQueryParser\Params;

use Ngekoding\CodeIgniterApiQueryParser\Exceptions\InvalidSortDirectionException;

class Sort
{
    protected $field;
    protected $direction;

    public function __construct($field, $direction = 'ASC')
    {
        $this->setField($field);
        $this->setDirection($direction);
    }

    /**
     * Get the value of field
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
     * Get the value of direction
     */ 
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set the value of direction
     *
     * @return self
     */ 
    public function setDirection($direction)
    {
        $direction = strtoupper($direction);

        if ( ! in_array($direction, ['ASC', 'DESC'])) {
            throw new InvalidSortDirectionException();
        }

        $this->direction = $direction;

        return $this;
    }
}
