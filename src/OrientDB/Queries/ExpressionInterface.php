<?php

namespace OrientDB\Queries;

interface ExpressionInterface
{

    /**
     * Gets the expression text.
     *
     * @return string The expression text.
     */
    public function getText();

    /**
     * Sets the parameters for the expression.
     *
     * @param array $params The expression parameters.
     *
     * @return $this The current object.
     */
    public function setParams($params);

    /**
     * Gets the parameters for the expression.
     *
     * @return array The expression parameters.
     */
    public function getParams();

}
