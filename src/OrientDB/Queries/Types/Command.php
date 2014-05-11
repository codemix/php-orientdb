<?php

namespace OrientDB\Queries\Types;


use OrientDB\Common\Binary;

/**
 * # Synchronous Query
 *
 * @package OrientDB\Queries
 */
class Command extends AbstractQueryType
{

    /**
     * Get the name or alias of the remote OrientDB query class that this class represents.
     * @return string
     */
    public function getOrientClass()
    {
        return 'com.orientechnologies.orient.core.sql.OCommandSQL';
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

        if (count($this->params)) {
            $bytes .= Binary::packByte(1);
            $bytes .= Binary::packString($this->serializeParams());
        }
        else {
            $bytes .= Binary::packInt(0);
        }
        $bytes .= Binary::packInt(0);
        return $bytes;
    }

}
