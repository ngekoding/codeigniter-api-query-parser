<?php

namespace Ngekoding\CodeIgniterApiQueryParser\Params;

class Pagination
{
    protected $page;
    protected $limit;

    public function __construct($page = null, $limit = null)
    {
        $this->setPage($page);
        $this->setLimit($limit);
    }

    /**
     * Get the value of page
     * 
     * @return int
     */ 
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set the value of page
     * 
     * @return self
     */ 
    public function setPage($page)
    {
        $this->page = (int) $page;

        return $this;
    }

    /**
     * Get the value of limit
     * 
     * @return int
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return self
     */ 
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * Get pagination offset
     * 
     * @return int
     */
    public function getOffset()
    {
        if ($this->getLimit() !== null && $this->getPage() !== null) {
            return $this->getPage() * $this->getLimit() - $this->getLimit();
        }
        
        return 0;
    }
}
