<?php

namespace Oriento\Record;

interface SerializableInterface
{
    /**
     * Return a representation of the class that can be serialized as an
     * OrientDB record.
     *
     * @return mixed
     */
    public function recordSerialize();
}
