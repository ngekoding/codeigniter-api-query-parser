<?php

namespace Ngekoding\CodeIgniterApiQueryParser;

use Ngekoding\CodeIgniterApiQueryParser\Exceptions\UnprocessableEntityException;
use Ngekoding\CodeIgniterApiQueryParser\Helpers\CodeIgniterMethodResolver;
use Ngekoding\CodeIgniterApiQueryParser\Helpers\CodeIgniterUrlResolver;
use Ngekoding\CodeIgniterApiQueryParser\Helpers\CodeIgniterVersionResolver;
use Ngekoding\CodeIgniterApiQueryParser\Helpers\ColumnNameResolver;
use Ngekoding\CodeIgniterApiQueryParser\Params\Filter;
use Ngekoding\CodeIgniterApiQueryParser\Params\Pagination;
use Ngekoding\CodeIgniterApiQueryParser\Params\Sort;

class QueryParser
{
    protected $queryBuilder;
    protected $ciVersion;
    protected $methodResolver;
    protected $urlResolver;
    protected $columnAliases;

    /**
     * Initiate the query parser
     * 
     * @param $queryBuilder The CodeIgniter 3 or 4 query builder
     * @param int|null $ciVersion The CodeIgniter version
     */
    public function __construct($queryBuilder, $ciVersion = null)
    {
        $this->queryBuilder = $queryBuilder;
        $this->ciVersion = CodeIgniterVersionResolver::resolve($ciVersion);
        $this->methodResolver = new CodeIgniterMethodResolver($this->ciVersion);
        $this->urlResolver = new CodeIgniterUrlResolver($this->ciVersion);
        $this->columnAliases = (new ColumnNameResolver($this->queryBuilder, $this->methodResolver))->getColumnAliases();
    }

    /**
     * Add column alias
     * 
     * @param $alias The column alias
     * @param $expression The column name or expression
     * 
     * @return self
     */
    public function addColumnAlias($alias, $expression)
    {
        $this->columnAliases[$alias] = $expression;

        return $this;
    }
    
    /**
     * Apply query parser and generate the result
     * 
     * @return array
     */
    public function applyParams()
    {
        $params = (new RequestQueryParser())->parse();

        if ($params->hasFilter()) {
            foreach ($params->getFilters() as $filter) {
                $this->applyFilter($filter);
            }
        }

        if ($params->hasSort()) {
            foreach ($params->getSorts() as $sort) {
                $this->applySort($sort);
            }
        }

        // Get records total before applying pagination
        $countAllResults = $this->methodResolver->get('countAllResults');
        $recordsTotal = $this->queryBuilder->{$countAllResults}('', false);

        if ($params->hasPagination()) {
            $pagination = $params->getPagination();
            $this->queryBuilder->limit($pagination->getLimit(), $pagination->getOffset());
        } else {
            $pagination = null;
        }

        $data = $this->queryBuilder
                     ->{$this->methodResolver->get('get')}()
                     ->{$this->methodResolver->get('getResult')}();

        $paginationResponse = $this->buildPaginationResponse($recordsTotal, $pagination);
        
        return [
            'data' => $data,
            'pagination' => $paginationResponse,
        ];
    }

    protected function applyFilter(Filter $filter)
    {
        $field = $filter->getField();
        $field = isset($this->columnAliases[$field]) ? $this->columnAliases[$field] : $field;

        $operator = $filter->getOperator();
        $value = $filter->getValue();

        $clauseOperator = null;

        switch ($operator) {
            case 'ct':
                $value = '%' . $value . '%';
                $clauseOperator = 'LIKE';
                break;
            case 'nct':
                $value = '%' . $value . '%';
                $clauseOperator = 'NOT LIKE';
                break;
            case 'sw':
                $value = $value . '%';
                $clauseOperator = 'LIKE';
                break;
            case 'ew':
                $value = '%' . $value;
                $clauseOperator = 'LIKE';
                break;
            case 'eq':
                $clauseOperator = '=';
                break;
            case 'ne':
                $clauseOperator = '!=';
                break;
            case 'gt':
                $clauseOperator = '>';
                break;
            case 'ge':
                $clauseOperator = '>=';
                break;
            case 'lt':
                $clauseOperator = '<';
                break;
            case 'le':
                $clauseOperator = '<=';
                break;
            case 'in':
                // Handled in a different way
                break;
            default:
                throw new UnprocessableEntityException(sprintf('Filter operator not allowed: %s', $operator));
        }

        if ($operator == 'in') {
            $whereIn = $this->methodResolver->get('whereIn');
            $this->queryBuilder->{$whereIn}($field, explode(',', $value));
        } else {
            $where = $this->methodResolver->get('where');
            $this->queryBuilder->{$where}(sprintf('%s %s', $field, $clauseOperator), $value);
        }
    }

    protected function applySort(Sort $sort)
    {
        $this->queryBuilder->order_by($sort->getField(), $sort->getDirection());
    }

    /**
     * Build pagination response
     * 
     * @param int records total
     * @param Pagination|null $pagination
     * 
     * @return array
     */
    protected function buildPaginationResponse($recordsTotal, $pagination = null)
    {
        $currentPage = $from = $lastPage = 1;
        $perPage = $to = $recordsTotal;

        if ($pagination !== null) {
            $currentPage = $pagination->getPage();
            $from = $pagination->getOffset() + 1;
            $to = $from + $pagination->getLimit();
            $to = $to > $recordsTotal ? $recordsTotal : $to;
            $perPage = $pagination->getLimit();
            $lastPage = (int) ceil($recordsTotal / $perPage);
        }

        $firstLink = $this->getPaginationLink(1);
        $lastLink = $this->getPaginationLink($lastPage);
        $prevLink = $currentPage > 1 ? $this->getPaginationLink($currentPage - 1) : null;
        $nextLink = $currentPage < $lastPage ? $this->getPaginationLink($currentPage + 1) : null;
        
        return [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'from' => $from,
            'to' => $to,
            'total' => $recordsTotal,
            'last_page' => $lastPage,
            'links' => [
                'first' => $firstLink,
                'prev' => $prevLink,
                'next' => $nextLink,
                'last' => $lastLink
            ]
        ];
    }

    /**
     * Get pagination link based on given page
     * 
     * @param int|null $page
     * @return string|null
     */
    protected function getPaginationLink($page)
    {
        $url = null;
        if ($page !== null) {
            $currentUrl = $this->urlResolver->currentUrl();
            $params = $_GET;
            $params['page'] = $page;
            $params = urldecode(http_build_query($params));

            // Remove integer array index and build query params
            $params = preg_replace('/\[(\d+)\]/', '[]', $params);
            
            $url = $currentUrl.'?'.$params;
        }
        return $url;
    }
}
