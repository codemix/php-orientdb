<?php

namespace Orienta\Queries;

use Orienta\Databases\Database;
use Orienta\Queries\Types\Command;
use Orienta\Records\DocumentInterface;
use Orienta\Records\RecordInterface;

class Query extends Statement
{
    /**
     * @var Database The database the query is for.
     */
    protected $database;

    /**
     * @var ResultList The query results.
     */
    protected $results;

    /**
     * @param Database $database The database the query is for.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Get the first field of the first result.
     *
     * @return mixed
     */
    public function scalar()
    {
        return $this->getResults()->scalar();
    }

    /**
     * Get the first result.
     *
     * @return RecordInterface|DocumentInterface The first result.
     */
    public function one()
    {
        return $this->getResults()->one();
    }

    /**
     * Get the list of results.
     *
     * @return ResultList The list of results.
     */
    public function all()
    {
        return $this->getResults();
    }

    /**
     * Create an OrientDB query instance from this query.
     *
     * @return Command The orient query type instance.
     */
    public function prepare()
    {
        return Command::fromConfig([
            'text' => $this->getText(),
            'params' => $this->getParams()
        ]);
    }

    /**
     * Sets the results
     *
     * @param \Orienta\Queries\ResultList $results
     *
     * @return $this the current object
     */
    public function setResults($results)
    {
        $this->results = $results;
        return $this;
    }

    /**
     * Gets the results for the query.
     * @return \Orienta\Queries\ResultList
     */
    public function getResults()
    {
        if ($this->results === null) {
            $this->execute();
        }
        return $this->results;
    }


    /**
     * Execute the query and return the results.
     *
     * @return ResultList The query results.
     */
    public function execute()
    {
        $prepared = $this->prepare();
        $this->results = $this->database->query($prepared);
        return $this->results;
    }
}
