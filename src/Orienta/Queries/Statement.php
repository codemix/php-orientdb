<?php

namespace Orienta\Queries;

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
        'limit' => 'LIMIT',
        'offset' => 'OFFSET',
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
                if (method_exists($this, $methodName)) {
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

                    $parts[] = $keyword.' '.implode(', ', $clauseParts);
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

        return 'SELECT '.implode(', ', $clauseParts);
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
            }
            if (is_array($condition)) {
                foreach($condition as $key => $value) {
                    $stack[] = $key.' = :where_'.$i.'_'.$key;
                }
            }
            else {
                $stack[] = $condition;
            }
        }
        if (count($stack)) {
            $combined[] = '('.implode(' '.$currentOp.' ', $stack).')';
        }

        return 'WHERE '.implode(' AND ', $combined);
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
        return $this->params;
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
}
