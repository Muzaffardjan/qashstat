<?php  
namespace Pages\Tables;

use Pages\AlternativesChain\Adapter\DatabaseTable\ItemsTableProvider;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class News implements ItemsTableProvider, EventManagerAwareInterface
{
    /**
     * @const EVENT_NEWS_ADDED News added event name
     */
    const EVENT_NEWS_ADDED = 'news.added';

    /**
     * @var EventManager $events Event manager holder
     */
    protected $events;

    protected $tablegateway;
    protected $serviceManager;

    public function __construct(
        \Zend\Db\TableGateway\TableGateway $tablegateway,
        \Zend\ServiceManager\ServiceManager $sm
    )
    {
        $this->setTablegateway($tablegateway);
        $this->setServiceLocator($sm);
    }

    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(
            [
                __CLASS__,
                get_class($this)
            ]
        );
        
        $this->events = $events;
    }

    public function getEventManager()
    {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
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

    public function fetchAll($where = null, $sort = 'added DESC', $limit = null)
    {
        return $this->getTablegateway()->select(
            function($select) use($where, $sort, $limit) {
                if ($where) {
                    $select->where($where);
                }

                if ($limit) {
                    $select->limit($limit);
                }

                $select->order($sort);
            }
        );
    }

    public function getNewsById($id)
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

    public function add($news)
    {
        if (!is_array($news) && !($news instanceof \Pages\ArrayObject\News)) {
            throw new \Exception('Data must be array or Pages\ArrayObject\Page');
        }

        if (is_array($news)) {
            $temp = $this->getTablegateway()->getResultSetObject()->getArrayObjectPrototype();
            $news = $temp->setData($news);
        }

        $conflicts = $this->getTablegateway()->select(
            function($select) use ($news) {
                $select->where(
                    function($where) use ($news) {
                        $where->equalTo('url', $news->url)
                            ->greaterThanOrEqualTo(
                                'added',
                                mktime(
                                    0,
                                    0,
                                    0,
                                    date('n', $news->added),
                                    date('j', $news->added),
                                    date('Y', $news->added)
                                )
                            )
                            ->lessThanOrEqualTo(
                                'added',
                                mktime(
                                    23,
                                    59,
                                    59,
                                    date('n', $news->added),
                                    date('j', $news->added),
                                    date('Y', $news->added)
                                )
                            );
                    }
                );
            }
        );

        if ($conflicts->count()) {
            $news->url .= '-'.date('d-m-Y-His');
        }

        $this->getTablegateway()->insert($news->getData());
        $news->id = $this->getTablegateway()->lastInsertValue;

        // Trigger news added event
        /*$this->getEventManager()->trigger(
            self::EVENT_NEWS_ADDED, 
            // Target will be new added news
            $news
        );*/
    }

    public function edit($news, $where = array())
    {
        if(!is_array($news) && $news instanceof \Pages\ArrayObject\News)
        {
            $where      = array('id' => $news->id);
            $news   = $news->getData();

            if(isset($news['id']))
            {
                unset($news['id']);
            }
        }

        if(!is_array($news))
        {
            throw new \Exception('Data must be array or Pages\ArrayObject\News');
        }

        $this->getTablegateway()->update($news, $where);
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