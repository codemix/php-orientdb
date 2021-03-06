<?php


namespace OrientDB\Queries\Types;

use OrientDB\Common\ConfigurableInterface;
use OrientDB\Common\ConfigurableTrait;
use OrientDB\Records\Serializer;

abstract class AbstractQueryType implements QueryTypeInterface, ConfigurableInterface
{
    use ConfigurableTrait;

    /**
     * @var string The query text.
     */
    public $text = '';

    /**
     * @var array The parameters for the query.
     */
    public $params = [];

    /**
     * Serialize the parameters for the query.
     *
     * > Note: There is a bug in OrientDB where special kinds of string values
     * > need to be twice quoted *in parameters*. Hence the need for this specialist function.
     *
     * @return string The serialized parameters.
     */
    protected function serializeParams()
    {
        $collected = [];
        foreach($this->params as $key => $value) {
            if (is_string($value) && strlen($value)) {
                $c = $value[0];
                $isQuotable = (
                    $c === '#'
                    || $c === '<'
                    || $c === '['
                    || $c === '('
                    || $c === '{'
                    || $c === '0'
                    || is_numeric($c)
                );
                if ($isQuotable) {
                    $collected[$key] = '"'.$value.'"';
                }
                else {
                    $collected[$key] = $value;
                }
            }
            else {
                $collected[$key] = $value;
            }
        }
        $serialized = Serializer::serialize($collected);
        return "params:".$serialized;
    }

}
