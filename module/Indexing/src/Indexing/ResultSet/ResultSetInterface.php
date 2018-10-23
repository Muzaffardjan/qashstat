<?php
namespace Indexing\ResultSet;

use Countable;
use Traversable;

interface ResultSetInterface extends Traversable, Countable
{
    /**
     * Can be anything traversable|array
     * @abstract
     * @param $traversable
     * @return mixed
     */
    public function initialize($traversable);
}
