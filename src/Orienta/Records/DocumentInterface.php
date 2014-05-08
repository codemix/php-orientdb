<?php

namespace Orienta\Records;


interface DocumentInterface extends RecordInterface
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
}
