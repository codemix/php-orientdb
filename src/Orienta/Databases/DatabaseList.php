<?php

namespace Orienta\Databases;

use Orienta\Client;
use Orienta\Common\MapInterface;
use Orienta\Common\MapIterator;
use Orienta\Common\MapTrait;

class DatabaseList implements MapInterface
{
    use MapTrait;

    protected $items;


    /**
     * @var Client The client the list of databases belongs to.
     */
    protected $client;

    /**
     * @var int The number of databases on the server.
     */
    protected $count;

    /**
     * # Constructor
     * Set the client for the list of databases.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Reload the list of databases.
     *
     * @return $this The reloaded database list.
     */
    public function reload()
    {
        $this->items = [];
        foreach($this->client->execute('dbList')['databases'] as $key => $value) {
            $this->items[$key] = new Database($this->client, $key, $value);
        }
        return $this;
    }


    /**
     * Create a database with the given name.
     *
     * @param string $name The name of the database to create.
     * @param string $storage The storage type for the database, e.g. 'memory' or 'plocal', defaults to 'plocal'.
     * @param string $type The database type, defaults to 'graph'.
     *
     * @return Database The created database instance.
     */
    public function create($name, $storage = 'plocal', $type = 'graph')
    {
        if ($name instanceof Database) {
            $type = $name->type;
            $storage = $name->storage;
            $name = $name->name;
        }
        $this->client->execute('dbCreate', [
            'database' => $name,
            'storage' => $storage,
            'type' => $type,
        ]);

        $this->reload();
        return $this->offsetGet($name);
    }

    /**
     * Drop the database with the given name.
     *
     * @param string $name The database name.
     * @param string $storage The database storage type, defaults to 'plocal'.
     *
     * @return boolean True if the database was dropped.
     */
    public function drop($name, $storage = 'plocal')
    {
        if ($name instanceof Database) {
            $storage = $name->storage;
            $name = $name->name;
        }
        return $this->client->execute('dbDrop', [
            'database' => $name,
            'storage' => $storage,
        ]);
    }


    /**
     * Determine whether a database with the given name exists.
     *
     * @param string $name The database name.
     * @param string $storage The database storage type, defaults to 'plocal'.
     *
     * @return boolean true if the database exists, otherwise false.
     */
    public function exists($name, $storage = 'plocal')
    {
        if ($name instanceof Database) {
            $storage = $name->storage;
            $name = $name->name;
        }
        return $this->client->execute('dbExists', [
            'database' => $name,
            'storage' => $storage
        ]);
    }

    /**
     * Returns an iterator for traversing the data.
     * This method is required by the SPL interface `IteratorAggregate`.
     * It will be implicitly called when you use `foreach` to traverse the collection.
     * @return MapIterator an iterator for traversing the cookies in the collection.
     */
    public function getIterator()
    {
        if ($this->items === null) {
            $this->reload();
        }
        return new MapIterator($this);
    }

    /**
     * Returns the number of data items.
     * This method is required by Countable interface.
     * @return integer number of data elements.
     */
    public function count()
    {
        if ($this->items === null) {
            $this->reload();
        }
        return count($this->items);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        if ($this->items === null) {
            $this->reload();
        }
        return isset($this->items[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return Database|null Can return all value types.
     */
    public function offsetGet($offset)
    {
        if ($this->items === null) {
            $this->reload();
        }
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($this->items === null) {
            $this->reload();
        }
        $this->items[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->items === null) {
            $this->reload();
        }
        unset($this->items[$offset]);
    }


}
