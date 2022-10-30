# CodeIgniter API Query Parser

Simple request query parameter parser for REST-APIs based on CodeIgniter framework. Works for both CodeIgniter 3 and CodeIgniter 4.

Inspired by [Lumen API Query Parser](https://github.com/ngabor84/lumen-api-query-parser)

## Requirements

- PHP 5.6 or above
- CodeIgniter 3 or CodeIgniter 4
- PHPSQLParser ^4.5

## Installation

You just need to use composer and everything is done.

```sh
composer require ngekoding/codeigniter-api-query-parser
```

## Usage

Here is the example for CodeIgniter 3 and CodeIgniter 4. Actually there is no different by the use of the library, just different how to create the query builder.

### CodeIgniter 3 Example

```php
// CodeIgniter 3 Example

// Get a query builder
// Please note: we don't need to call ->get() here
$queryBuilder = $this->db->select('p.*, c.name category')
                    ->from('posts p')
                    ->join('categories c', 'c.id=p.category_id');

/**
 * The first parameter is the query builder instance
 * and the second is the codeigniter version (3 or 4) 
 */
$queryParser = new \Ngekoding\CodeIgniterApiQueryParser\QueryParser($queryBuilder);
$result = $queryParser->applyParams(); // done

print_r($result);
```

The above example will output an array with `data` and `pagination`:

```
Array
(
    [data] => Array
        (
            [0] => ...
            [1] => ...
        )
    [pagination] => Array
        (
            [current_page] => int
            [per_page] => int
            [from] => int
            [to] => int
            [total] => int
            [last_page] => int
            [links] => Array
                (
                    [first] => string
                    [prev] => string or null
                    [next] => string or null
                    [last] => string
                )
        )
)
```

### CodeIgniter 4 Example

The different only by the way to create the Query Builder.

```php
// CodeIgniter 4 Example

$db = db_connect();
$queryBuilder = $db->from('posts p')
                   ->select('p.*, c.name category')
                   ->join('categories c', 'c.id=p.category_id');

$queryParser = new \Ngekoding\CodeIgniterApiQueryParser\QueryParser($queryBuilder);
$result = $queryParser->applyParams(); // done

print_r($result);
```

## Query Syntax

### Filtering

Q: /users?filter[]=name:ct:admin    
R: will return the array of the users whose names contains the `admin` string

**Available filter options**

| Operator      | Description           | Example |
| ------------- | --------------------- | ------- |
| ct            | String contains       | name:ct:Peter |
| nct           | String NOT contains   | name:nct:Peter |
| sw            | String starts with    | username:sw:admin |
| ew            | String ends with      | email:ew:gmail.com |
| eq            | Equals                | level:eq:3 |
| ne            | Not equals            | level:ne:4 |
| gt            | Greater than          | level:gt:2 |
| ge            | Greater than or equal | level:ge:3 |
| lt            | Less than             | level:lt:4 |
| le            | Less than or equal    | level:le:3 |
| in            | In array              | level:in:1,2,3 |

### Sorting

Q: /users?sort[]=name:ASC   
R: will return the array of the users sort by their `names` ascending

### Pagination

Q: /users?limit=10&page=3   
R: will return a part of the array of the users (from the 21st to 30th)

## Column Alias

When working with SQL expression in selecting data or using join with ambiguous column name, **the library automatically will try to find the original column name or its expression** to use for the filter feature. But, you can still manually define the column alias for better use, expecially to resolving the ambiguous column name when using join.

For example, joining posts table with categories table in the example above will return an `id` from the posts table. So, when we try to get the data with `id` 1, 2 or 3 (`SQL WHERE IN`) using filter[]=id:in:1,2,3 we get the ambiguous column error.

Here is the **column alias** to solve it.

```php
$queryParser = new \Ngekoding\CodeIgniterApiQueryParser\QueryParser($queryBuilder);

// Tell that the `id` is `p.id` (posts table id)
$queryParser->addColumnAlias('id', 'p.id');

$result = $queryParser->applyParams(); // done
```