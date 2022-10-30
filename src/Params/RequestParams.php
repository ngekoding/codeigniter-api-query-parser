<?php

namespace Ngekoding\CodeIgniterApiQueryParser\Params;

class RequestParams
{
    protected $filters = [];
    protected $sorts = [];
    protected $pagination;

    /**
     * Add filter
     * 
     * @param Filter $filter
     * @return self
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
        
        return $this;
    }

    /**
     * Check filter exists
     * 
     * @return bool
     */
    public function hasFilter()
    {
        return (bool) count($this->filters);
    }

    /**
     * Get filters
     * 
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Add sort
     * 
     * @param Sort $sort
     * @return self
     */
    public function addSort(Sort $sort)
    {
        $this->sorts[] = $sort;
        
        return $this;
    }

    /**
     * Check sort exists
     * 
     * @return bool
     */
    public function hasSort()
    {
        return (bool) count($this->sorts);
    }

    /**
     * Get sorts
     * 
     * @return array
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * Add pagination
     * 
     * @param Pagination $pagination
     * @return self
     */
    public function addPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;
        
        return $this;
    }

    /**
     * Check pagination exists
     * 
     * @return bool
     */
    public function hasPagination()
    {
        return $this->pagination !== null;
    }

    /**
     * Get pagination
     * 
     * @return Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}
