<?php

namespace Orienta\Queries;

/**
 * Class Statement
 *
 * @method \Orienta\Queries\Statement or() or($condition) Add an `OR` condition.
 * @method \Orienta\Queries\Statement and() and($condition) Add an `AND` condition.
 *
 * @package Orienta\Queries
 */
class Statement implements ExpressionInterface
{
    /**
     * @var array The individual clauses that make up the statement.
     */
    protected $clauses = [];

    /**
     * @var array The bound parameters for the statement.
     */
    protected $params = [];

    /**
     * @var string[] The clause names, in order.
     */
    protected $clauseOrder = [
        'begin' => 'BEGIN;',
        'let' => 'LET',
        'traverse' => 'TRAVERSE',
        'select' => 'SELECT',
        'update' => 'UPDATE',
        'insert' => 'INSERT',
        'delete' => 'DELETE',
        'from' => 'FROM',
        'to' => 'TO',
        'into' => 'INTO',
        'set' => 'SET',
        'where' => 'WHERE',
        'groupBy' => 'GROUP BY',
        'orderBy' => 'ORDER BY',
        'skip' => 'SKIP',
        'limit' => 'LIMIT',
        'fetch' => 'FETCHPLAN',
        'timeout' => 'TIMEOUT',
        'lock' => 'LOCK',
        'commit' => 'COMMIT',
        'return' => 'RETURN',
    ];

    /**
     * Add a `SELECT` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function select($expression = '*')
    {
        $args = func_get_args();
        $args[0] = $expression;
        return $this->addClause('select', $args);
    }

    /**
     * Add an `INSERT` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function insert($expression = null)
    {
        if ($expression !== null) {
            $this->addClause('set', func_get_args());
        }
        return $this->addClause('insert', []);
    }

    /**
     * Add an `UPDATE` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function update($expression)
    {
        return $this->addClause('update', func_get_args());
    }


    /**
     * Add a `DELETE` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function delete($expression = null)
    {
        if ($expression !== null) {
            $this->addClause('from', func_get_args());
        }
        return $this->addClause('delete', []);
    }


    /**
     * Add a `SET` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function set($expression)
    {
        return $this->addClause('set', func_get_args());
    }

    /**
     * Add an `INTO` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function into($expression)
    {
        return $this->addClause('into', func_get_args());
    }

    /**
     * Add a `FROM` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function from($expression)
    {
        return $this->addClause('from', func_get_args());
    }

    /**
     * Add a `TO` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function to($expression)
    {
        return $this->addClause('to', func_get_args());
    }

    /**
     * Add a `WHERE` clause.
     *
     * @param mixed $condition The condition to add.
     * @param string $operator The operator, defaults to `AND`.
     *
     * @return $this The current object.
     */
    public function where($condition, $operator = 'AND')
    {
        return $this->addCondition($condition, $operator);
    }

    /**
     * Add a `GROUP BY` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function groupBy($expression)
    {
        return $this->addClause('groupBy', func_get_args());
    }

    /**
     * Add a `LIMIT` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function limit($expression)
    {
        return $this->addClause('limit', func_get_args());
    }

    /**
     * Add a `SKIP` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function skip($expression)
    {
        return $this->addClause('skip', func_get_args());
    }

    /**
     * Add a `SKIP` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function offset($expression)
    {
        return $this->addClause('skip', func_get_args());
    }

    /**
     * Add a `SKIP` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function fetch($expression)
    {
        return $this->addClause('fetch', func_get_args());
    }

    /**
     * Add a `TIMEOUT` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function timeout($expression)
    {
        return $this->addClause('timeout', func_get_args());
    }

    /**
     * Add a `LOCK` clause.
     *
     * @param mixed $expression, $... The expressions for this part of the statement.
     *
     * @return $this The current object.
     */
    public function lock($expression = 'default')
    {
        return $this->addClause('lock', [$expression]);
    }

    /**
     * Add a condition.
     *
     * @param mixed $condition The condition to add.
     * @param string $operator The operator for the condition, defaults to `AND`.
     *
     * @return $this The current object.
     */
    public function addCondition($condition, $operator = 'AND')
    {
        if (!isset($this->clauses['where'])) {
            $this->clauses['where'] = [];
        }
        $this->clauses['where'][] = [$operator, $condition];
        return $this;
    }

    /**
     * Gets the expression text.
     *
     * @return string The expression text.
     */
    public function getText()
    {
        $parts = [];
        foreach($this->clauseOrder as $name => $keyword) {
            if (isset($this->clauses[$name])) {
                $methodName = 'process'.$name;
                if ($name === 'insert' || $name === 'delete') {
                    $parts[] = $keyword;
                }
                else if (method_exists($this, $methodName)) {
                    $parts[] = $this->{$methodName}($this->clauses[$name]);
                }
                else {
                    $clauseParts = [];
                    foreach($this->clauses[$name] as $key => $value) {
                        if ($value instanceof ExpressionInterface) {
                            $clauseParts[$key] = $value->getText();
                        }
                        else {
                            $clauseParts[$key] = $value;
                        }
                    }

                    $parts[] = $keyword.' '.implode(',', $clauseParts);
                }
            }
        }
        return implode(' ', $parts);
    }

    protected function processSelect($items)
    {
        $clauseParts = [];
        foreach($items as $key => $value) {
            if ($value instanceof ExpressionInterface) {
                $value = '('.$value->getText().')';
            }
            if (is_int($key)) {
                $clauseParts[] = $value;
            }
            else {
                $clauseParts[] = $value.' AS '.$key;
            }
        }

        return 'SELECT '.implode(',', $clauseParts);
    }

    protected function processWhere($items)
    {
        $combined = [];
        $currentOp = 'AND';
        $stack = [];
        foreach($items as $i => $item) {
            list($operation, $condition) = $item;
            if ($operation !== $currentOp) {
                if (count($stack)) {
                    $combined[] = '('.implode(' '.$currentOp.' ', $stack).')';
                }
                $stack = [];
                $currentOp = $operation;
                $combined[] = $currentOp;
            }
            if (is_array($condition)) {
                foreach($condition as $key => $value) {
                    $stack[] = $key.' = :paramwhere'.$i.$key;
                }
            }
            else {
                $stack[] = $condition;
            }
        }
        if (count($stack)) {
            $combined[] = '('.implode(' '.$currentOp.' ', $stack).')';
        }

        return 'WHERE '.implode(' ', $combined);
    }

    protected function processSet($items)
    {
        $clauseParts = [];
        foreach($items as $key => $value) {
            if (is_int($key)) {
                $clauseParts[] = $value;
            }
            else {
                $clauseParts[] = $key.' = :paramset'.$key;
            }
        }

        return 'SET '.implode(',', $clauseParts);
    }

    protected function processFetch($items)
    {
        $clauseParts = [];
        foreach($items as $key => $value) {
            if (is_int($key)) {
                $clauseParts[] = $value;
            }
            else {
                $clauseParts[] = $key.':'.$value;
            }
        }

        return 'FETCHPLAN '.implode(',', $clauseParts);
    }

    /**
     * Sets the parameters for the expression.
     *
     * @param array $params The expression parameters.
     *
     * @return $this The current object.
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Gets the parameters for the expression.
     *
     * @return array The expression parameters.
     */
    public function getParams()
    {
        $params = $this->params;
        if (isset($this->clauses['where'])) {
            foreach($this->clauses['where'] as $i => $item) {
                list($operation, $condition) = $item;
                if (is_array($condition)) {
                    foreach($condition as $key => $value) {
                        $params['paramwhere'.$i.$key] = $value;
                    }
                }
            }
        }
        if (isset($this->clauses['set'])) {
            foreach($this->clauses['set'] as $key => $value) {
                if (is_int($key)) {
                    $clauseParts[] = $value;
                }
                else {
                    $params['paramset'.$key] = $value;
                }
            }
        }
        return $params;
    }


    /**
     * Add a clause to the statement.
     *
     * @param string $type The type of clause to add.
     * @param array $clauses The arguments for the clause.
     *
     * @return $this The current object.
     */
    protected function addClause($type, $clauses)
    {
        if (!isset($this->clauses[$type])) {
            $this->clauses[$type] = [];
        }
        foreach($clauses as $key => $value) {
            if (is_array($value)) {
                foreach($value as $key2 => $value2) {
                    if (is_int($key2)) {
                        $this->clauses[$type][] = $value2;
                    }
                    else {
                        $this->clauses[$type][$key2] = $value2;
                    }
                }
            }
            else {
                if (is_int($key)) {
                    $this->clauses[$type][] = $value;
                }
                else {
                    $this->clauses[$type][$key] = $value;
                }
            }
        }
        return $this;
    }

    /**
     * Call a virtual method.
     *
     * @param string $name The name of the virtual method to call.
     * @param array $arguments The arguments to pass to the callee.
     *
     * @return mixed The result of the call.
     * @throws \BadMethodCallException If no such method can be found.
     */
    public function __call($name, $arguments)
    {
        if ($name === 'or') {
            return $this->where($arguments[0], 'OR');
        }
        else if ($name === 'and') {
            return $this->where($arguments[0], 'AND');
        }
        else {
            throw new \BadMethodCallException(get_called_class().' does not have a method called "'.$name.'"');
        }
    }
}
