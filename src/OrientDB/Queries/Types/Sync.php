<?php

namespace OrientDB\Queries\Types;


use OrientDB\Common\Binary;

/**
 * # Synchronous Query
 *
 * @package OrientDB\Queries
 */
class Sync extends AbstractQueryType
{

    /**
     * @var int The non-text limit for the query.
     */
    public $limit = -1;

    /**
     * @var string The fetch plan for the query.
     */
    public $fetchPlan = '';

    /**
     * Get the name or alias of the remote OrientDB query class that this class represents.
     * @return string
     */
    public function getOrientClass()
    {
        return 'q';
    }

    /**
     * Get a binary representation of the query, ready to send to orient.
     *
     * @return string A binary serialized version of the query class.
     */
    public function binarySerialize()
    {
        $bytes = Binary::packString($this->getOrientClass());
        $bytes .= Binary::packString($this->text);
        $bytes .= Binary::packInt($this->limit);
        $bytes .= Binary::packString($this->fetchPlan);

        if (count($this->params)) {
            $bytes .= Binary::packByte(1);
            $bytes .= Binary::packString($this->serializeParams());
        }
        else {
            $bytes .= Binary::packInt(0);
        }
        return $bytes;
    }

}
