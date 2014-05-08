<?php

namespace Orienta\Query;


interface QueryInterface
{
    /**
     * Get the name or alias of the remote OrientDB query class that this class represents.
     * @return string
     */
    public function getOrientClass();


    /**
     * Get a binary representation of the query, ready to send to orient.
     *
     * @return string A binary serialized version of the query class.
     */
    public function binarySerialize();
}
