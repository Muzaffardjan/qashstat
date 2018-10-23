<?php  
namespace Pages\Tables;

use Pages\AlternativesChain\Adapter\DatabaseTable\ItemsTableProvider;

class Events implements ItemsTableProvider
{
    protected $tablegateway;
    protected $serviceManager;

    public function __construct(\Zend\Db\TableGateway\TableGateway $tablegateway, \Zend\ServiceManager\ServiceManager $sm)
    {
        $this->setTablegateway($tablegateway);
        $this->setServiceLocator($sm);
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceManager $sm)
    {
        $this->serviceManager = $sm;

        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    public function setTablegateway(\Zend\Db\TableGateway\TableGateway $tablegateway)
    {
        $this->tablegateway = $tablegateway;

        return $this;
    }

    public function getTablegateway()
    {
        return $this->tablegateway;
    }

    /**
     * {@inheritDoc}
     */
    public function saveState(array $items)
    {
        $tablegateway   = $this->getTablegateway();
        $adapter        = $tablegateway->getAdapter();
        $sql            = $tablegateway->getSql();
        $updates        = array();

        foreach($items as $item) {
            $update = $sql->update();
            $update->set(array('chain' => $item->getChainId()))
                ->where(array('id' => $item->getItemId()));
            $updates[] = $sql->buildSqlString($update);
        }

        $adapter->query(join($updates, ';'), $adapter::QUERY_MODE_EXECUTE);
    }

    public function fetchAll($where = null, $sort = 'id DESC', $limit = null)
    {
        return $this->getTablegateway()
            ->select(function($select)use($where,$sort,$limit) {
                if($where) {
                    $select->where($where);
                }

                if($limit) {
                    $select->limit($limit);
                }

                $select->order($sort);
            });
    }

    public function getEventById($id)
    {
        return $this->getTablegateway()->select(array('id' => $id))->current();
    }

    public function getByUrl($url)
    {
        return $this->getTablegateway()->select(array('url' => $url))->current();
    }

    public function getWith($where)
    {
        if($where instanceof \Zend\Db\Sql\Select) {
            return $this->getTablegateway()->selectWith($where);
        }

        return $this->getTablegateway()->select($where);
    }

    public function add($event)
    {
        if(!is_array($event) && !($event instanceof \Pages\ArrayObject\Event)) {
            throw new \Exception('Data must be array or Pages\ArrayObject\Page');
        }

        if(is_array($event)) {
            $temp = $this->getTablegateway()
                ->getResultSetObject()
                ->getArrayObjectPrototype();
            $event = $temp->setData($event);
        }

        $conflicts = $this->getTablegateway()
            ->select(function($select)use($event) {
                $select->where(function($where)use($event){
                    $where->equalTo('url', $event->url)
                        ->greaterThanOrEqualTo(
                            'added', 
                            mktime(
                                0, 
                                0, 
                                0, 
                                date('n', $event->added), 
                                date('j', $event->added), 
                                date('Y', $event->added)
                            )
                        )
                        ->lessThanOrEqualTo(
                            'added', 
                            mktime(
                                23, 
                                59, 
                                59, 
                                date('n', $event->added), 
                                date('j', $event->added), 
                                date('Y', $event->added)
                            )
                        );
                });
            });

        if($conflicts->count()) {
            $event->url .= '-'.date('d-m-Y-His');
        }

        $this->getTablegateway()->insert($event->getData());
    }

    public function edit($event, $where = array())
    {
        if(!is_array($event) && $event instanceof \Pages\ArrayObject\Event) {
            $where = array('id' => $event->id);
            $event = $event->getData();

            if(isset($event['id'])) {
                unset($event['id']);
            }
        }

        if(!is_array($event)) {
            throw new \Exception('Data must be array or Pages\ArrayObject\Event');
        }

        $this->getTablegateway()->update($event, $where);
    }

    /**
     * @param int|Zend\Db\Sql\Where|Clojure|string $where
     */
    public function delete($where)
    {
        if(is_int($where) || $where * 1 > 0) {
            $where = array('id' => $where);
        }

        $this->getTablegateway()->delete($where);
    }
}
?>