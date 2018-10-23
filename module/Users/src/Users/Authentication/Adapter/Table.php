<?php  
namespace Users\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

/*
    Result::SUCCESS
    Result::FAILURE
    Result::FAILURE_IDENTITY_NOT_FOUND
    Result::FAILURE_IDENTITY_AMBIGUOUS
    Result::FAILURE_CREDENTIAL_INVALID
    Result::FAILURE_UNCATEGORIZED

    Result::__construct($code, $identity, array $messages = array())
*/

class Table implements AdapterInterface
{
    /**
     * Holds login info
     * 
     * @var array $_credentials
     */
    protected $_credentials = array();

    /**
     * Holds database tabel provider instance
     * 
     * @var \Users\Authentication\DatabaseTableInterface $_databaseTableProvider
     */
    protected $_databaseTableProvider;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username = null, $password = null, $databaseTableProvider = null)
    {
        $this->_credentials = array(
            'username' => $username,
            'password' => $password,
        );

        $this->_databaseTableProvider = $databaseTableProvider;
    }

    public function setUsername($username)
    {
        $this->_credentials['username'] = $username;

        return $this;
    }

    public function getUsername()
    {
        return isset($this->_credentials['username'])? $this->_credentials['username']: null;
    }

    public function setPassword($password)
    {
        $this->_credentials['password'] = $password;
        
        return $this;
    }

    public function getPassword()
    {
        return isset($this->_credentials['password'])? $this->_credentials['password']: null;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *                             If authentication cannot be performed
     */
    public function authenticate()
    {
        if(!($this->_databaseTableProvider instanceof \Users\Authentication\DatabaseTableInterface))
            throw new \Exception('Users\Authentication\Adapter\ApplicationAdapter: Database Table Provider not provided');
        
        // Get user by login
        $user = $this->_databaseTableProvider->getUser(array('login' => $this->_credentials['username']));
        
        // Return identity not found result if resultset does not contain any record
        if(!is_object($user))
        {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $this->_credentials['username'], array("The user with the username isn't found"));
        }

        if(!($user instanceof \Users\Authentication\ResultIdentityInterface))
        {
            return new Result(Result::FAILURE_UNCATEGORIZED, $this->_credentials['username'], array(sprintf("Result must be instance of 'Users\Authentication\ResultIdentityInterface' -> '%s' given", gettype($user))));
        }

        if($user->getPassword() != $this->_credentials['password'])
        {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->_credentials['username']);
        }

        return new Result(Result::SUCCESS, $user->toString());
    }   

    /**
     * Sets DataBase table gateway
     * 
     * @param \Users\Authentication\DatabaseTableInterface $gateway
     * @return \Users\Authentication\Adapter\ApplicationAdapter
     */
    public function setDatabaseTableProvider(\Users\Authentication\DatabaseTableInterface $gateway)
    {
        $this->_databaseTableProvider = $gateway;

        return $this;
    }

    /**
     * Retruns dataBase table gateway or null
     * 
     * @return \Users\Authentication\Adapter\ApplicationAdapter|null
     */
    public function getDatabaseTableProvider()
    {
        return $this->_databaseTableProvider;
    }
}
?>