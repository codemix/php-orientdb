<?php

namespace OrientDB\Queries;

class Expression implements ExpressionInterface
{
    /**
     * @var string The expression text.
     */
    protected $text;

    /**
     * @var array The parameters for the expression.
     */
    protected $params = [];

    /**
     * @param string $text The expression text.
     * @param array $params The expression parameters.
     */
    public function __construct($text, array $params = [])
    {
        $this->text = $text;
        $this->params = $params;
    }


    /**
     * Sets the expression text.
     *
     * @param string $text The expression text.
     *
     * @return $this The current object.
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Gets the expression text.
     *
     * @return string The expression text.
     */
    public function getText()
    {
        return $this->text;
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

}
