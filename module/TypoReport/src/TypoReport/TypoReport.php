<?php  
namespace TypoReport;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

class TypoReport implements ServiceLocatorAwareInterface
{
	const TABLE_NAME = 'typo';

    protected $services;

    protected $tablegateway;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->services;
    }

    public function setTableGateway(TableGateway $table)
    {
    	$this->tablegateway = $table;

    	return $this;
    }

    public function getTableGateway()
    {
    	if(null === $this->tablegateway)
    	{
    		$dbAdapter = $this->getServiceLocator()->get('\Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();

            $resultSetPrototype->setArrayObjectPrototype(new ArrayObject\Typo());
           
            $tableGateway 		= new TableGateway(self::TABLE_NAME, $dbAdapter, null, $resultSetPrototype);
           	
            $this->setTableGateway($tableGateway);
    	}

    	return $this->tablegateway;
    }

    public function getById($id)
    {
        $select = $this->getTableGateway()->getSql()->select();
        $select->where(array('id' => $id));

        return $this->getTableGateway()->selectWith($select)->current();
    }

    public function fetchAll($order = 'id DESC', $limit = null)
    {
        $select = $this->getTableGateway()->getSql()->select();
        $select->order($order);

        if(null !== $limit)
        {
            $select->limit($limit);
        }

        return $this->getTableGateway()->selectWith($select);
    }

    public function report($typo)
    {
    	if(!(is_array($typo) || $typo instanceof ArrayObject\Typo))
    	{
    		throw new Exception\InvalidArgumentException;
    	}
    	elseif(is_array($typo))
    	{
    		$object = new ArrayObject\Typo();

    		$object->setData($typo);
    		$typo 	= $object;
    	}

    	$this->getTableGateway()->insert($typo->getData());
    }

    public function correct($typo)
    {
    	if(!is_object($typo) && (int) $typo > 0)
    	{
            $id = $typo;
    	}
    	elseif($typo instanceof ArrayObject\Typo)
    	{
    		$id = $typo->id;
    	}
    	else
    	{
            throw new Exception\InvalidArgumentException;
    	}

    	$this->getTableGateway()->delete(array('id' => $id));
    }
}
?>