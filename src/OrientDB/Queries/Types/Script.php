<?php

namespace OrientDB\Queries\Types;

use OrientDB\Common\Binary;

class Script extends AbstractQueryType
{
    /**
     * @var string The script language.
     */
    public $language = 'sql';



    /**
     * Get the name or alias of the remote OrientDB query class that this class represents.
     * @return string
     */
    public function getOrientClass()
    {
        return 's';
    }

    /**
     * Get a binary representation of the query, ready to send to orient.
     *
     * @return string A binary serialized version of the query class.
     */
    public function binarySerialize()
    {
        $bytes = Binary::packString($this->getOrientClass());
        $bytes .= Binary::packString($this->language);
        $bytes .= Binary::packString($this->text);
        if (count($this->params)) {
            $bytes .= Binary::packString($this->serializeParams());
        }
        else {
            $bytes .= Binary::packInt(0);
        }
        $bytes .= Binary::packByte(0);
        return $bytes;
    }

}
