<?php  
namespace Pages\Tables;

use Pages\AlternativesChain\Adapter\DatabaseTable\ItemsTableProvider;

class Pages implements ItemsTableProvider
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

        foreach($items as $item)
        {
            $update = $sql->update();

            $update
            ->set(array(
                'chain'     => $item->getChainId()
            ))
            ->where(array('id' => $item->getItemId()));

            $updates[] = $sql->buildSqlString($update);
        }

        $adapter->query(join($updates, ';'), $adapter::QUERY_MODE_EXECUTE);
    }

    public function fetchAll($where = null, $sort = 'id DESC', $limit = null)
    {
        return $this->getTablegateway()->select(function($select)use($where,$sort,$limit)
            {
                if($where)
                {
                    $select->where($where);
                }

                if($limit)
                {
                    $select->limit($limit);
                }

                $select->order($sort);
            });
    }

    public function getPageById($id)
    {
        return $this->getTablegateway()->select(array('id' => $id))->current();
    }

    public function getByUrl($url)
    {
        return $this->getTablegateway()->select(array('url' => $url))->current();
    }

    public function getWith($where)
    {
        if($where instanceof \Zend\Db\Sql\Select)
        {
            return $this->getTablegateway()->selectWith($where);
        }

        return $this->getTablegateway()->select($where);
    }

    public function add($page)
    {
        if (!is_array($page) && !($page instanceof \Pages\ArrayObject\Page)) {
            throw new \Exception('Data must be array or Pages\ArrayObject\Page');
        }

        if (is_array($page)) {
            $temp = $this->getTablegateway()->getResultSetObject()->getArrayObjectPrototype();
            $page = $temp->setData($page);
        }

        $conflicts = $this->getTablegateway()->select(['url' => $page->url]);

        if ($conflicts->count()) {
            $page->url .= '-' . date('d-m-Y-His');
        }

        $this->getTablegateway()->insert($page->getData());
    }

    public function edit($page, $where = array())
    {
        if(!is_array($page) && $page instanceof \Pages\ArrayObject\Page)
        {
            $where      = array('id' => $page->id);
            $page   = $page->getData();

            if(isset($page['id']))
            {
                unset($page['id']);
            }
        }

        if(!is_array($page))
        {
            throw new \Exception('Data must be array or Pages\ArrayObject\Page');
        }

        $this->getTablegateway()->update($page, $where);
    }

    /**
     * @param int|Zend\Db\Sql\Where|Clojure|string $where
     */
    public function delete($where)
    {
        if(is_int($where) || $where * 1 > 0)
        {
            $where = array('id' => $where);
        }

        $this->getTablegateway()->delete($where);
    }
}
?>