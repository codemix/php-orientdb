<?php

namespace OrientDB\Common;

interface MapInterface extends ListInterface, MagicInterface
{
    /**
     * Get a list of keys in the map.
     *
     * @return string[] An array of keys.
     */
    public function keys();
}
