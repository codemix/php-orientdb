<?php

namespace Orienta\Query;

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
