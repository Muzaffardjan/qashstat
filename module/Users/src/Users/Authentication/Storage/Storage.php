<?php  
namespace Users\Authentication\Storage;
use Zend\Authentication\Storage\StorageInterface;

use Zend\Authentication\Storage as ZendStorage;

class Storage extends ZendStorage\Session
{
    protected $identity = null;

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws Zend\Authentication\Exception\ExceptionInterface
     *               If reading contents from storage is impossible
     * @return mixed
     */

    public function read()
    {
        $contents = parent::read();

        if(!$contents)
        {
            return null;
        }

        if(!$this->identity || $this->identity->toString() != $contents)
        {
            $this->identity = new \Users\Identity\User();

            $this->identity->fromString($contents);
        }

        return $this->identity;
    }
}
?>