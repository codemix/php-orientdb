<?php

namespace OrientDB\Queries\Types;

class Async extends Sync
{
    /**
     * @inheritDoc
     */
    public function getOrientClass()
    {
        return 'com.orientechnologies.orient.core.sql.query.OSQLAsynchQuery';
    }

}
