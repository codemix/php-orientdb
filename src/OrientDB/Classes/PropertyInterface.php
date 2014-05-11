<?php


namespace OrientDB\Classes;

/**
 *
 * @property string $name The name of the property.
 * @property int $type The property type.
 * @property bool $mandatory true if the property is mandatory.
 * @property bool $readonly true if the property is read only.
 * @property bool $notNull true if the property cannot contain null values.
 * @property int|null $min The minimum value, if any.
 * @property int|null $max The maximum value, if any.
 * @property string $regexp The regular expression for this property.
 * @property string $collate The collation for this property.
 * @property string $linkedClass The linked class for this property.
 * @property array $customFields The custom fields for the property.
 *
 * @package OrientDB\Classes
 */
interface PropertyInterface
{

}
