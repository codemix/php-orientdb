<?php

namespace OrientDB\Records;


interface DocumentInterface extends RecordInterface, \ArrayAccess, \Countable
{
    /**
     * Sets the attributes for the document.
     *
     * @param array $attributes The document attributes.
     *
     * @return $this the current object.
     */
    public function setAttributes($attributes);

    /**
     * Gets the attributes for the document.
     *
     * @return array The document attributes.
     */
    public function getAttributes();


    /**
     * Replace RIDs with their concrete instances.
     *
     * @param RecordInterface[] $items The record / document instances.
     */
    public function resolveReferences($items);
}
