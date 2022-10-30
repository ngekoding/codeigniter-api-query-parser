<?php

namespace Ngekoding\CodeIgniterApiQueryParser;

use Ngekoding\CodeIgniterApiQueryParser\Exceptions\UnprocessableEntityException;
use Ngekoding\CodeIgniterApiQueryParser\Params\Filter;
use Ngekoding\CodeIgniterApiQueryParser\Params\Pagination;
use Ngekoding\CodeIgniterApiQueryParser\Params\RequestParams;
use Ngekoding\CodeIgniterApiQueryParser\Params\Sort;

class RequestQueryParser
{
    protected $requestParams;

    public function __construct()
    {
        $this->requestParams = new RequestParams();
    }

    /**
     * Parse the request
     * 
     * @return RequestParams
     */
    public function parse()
    {
        $this->parseFilters();
        $this->parseSorts();
        $this->parsePagination();

        return $this->requestParams;
    }

    /**
     * Parse filter from request
     */
    protected function parseFilters()
    {
        if ($filters = Request::get('filter')) {
            foreach ($filters as $filter) {
                $filterParts = explode(':', $filter, 3);
                if (count($filterParts) < 3) {
                    throw new UnprocessableEntityException('Filter must contains field and value.');
                }
                list($field, $operator, $value) = $filterParts;
                $this->requestParams->addFilter(new Filter($field, $operator, $value));
            }
        }
    }

    /**
     * Parse sort from request
     */
    protected function parseSorts()
    {
        if ($sorts = Request::get('sort')) {
            foreach ($sorts as $sort) {
                $sortParts = explode(':', $sort);
                $field = $sortParts[0];
                $direction = isset($sortParts[1]) ? $sortParts[1] : 'ASC';
                if (empty($field)) {
                    throw new UnprocessableEntityException('Sort must contains field.');
                }
                $this->requestParams->addSort(new Sort($field, $direction));
            }
        }
    }

    /**
     * Parse pagination from request
     */
    protected function parsePagination()
    {
        if ($limit = Request::get('limit')) {
            $page = Request::get('page') ?: 1;
            $this->requestParams->addPagination(new Pagination($page, $limit));
        }
    }
}
