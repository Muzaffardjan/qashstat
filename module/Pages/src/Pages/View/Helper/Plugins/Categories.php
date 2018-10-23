<?php  
namespace Pages\View\Helper\Plugins;

use Pages\View\Helper\PagesHelperPluginInterface;
use Pages\ArrayObject\Event as EventArrayObject;
use Zend\View\Renderer\PhpRenderer;
use Zend\ServiceManager\ServiceManager;

class Categories implements PagesHelperPluginInterface
{
	protected $serviceLocator;

	protected $view;

	protected $partial;

	protected $table;

	protected $currentSet;

	public function setView(PhpRenderer $view)
	{
		$this->view = $view;

		return $this;
	}

	public function setServiceLocator(ServiceManager $sm)
	{
		$this->serviceLocator = $sm;

		return $this;
	}

	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}

	public function setTable(\Pages\Tables\Categories $table)
	{
		$this->table = $table;
	}

	public function getTable()
	{
		if(null === $this->table)
		{
			$this->table = $this->getServiceLocator()->get('Pages\Tables\Categories');
		}

		return $this->table;
	}

	public function setPartial($partial)
	{
		$this->partial = $partial;

		return $this;
	}

	public function getPartial()
	{
		return $this->partial;
	}

	public function fetchAll($order = 'id DESC', $limit = null)
	{
		$table = $this->getTable();
		$where = array(
			'locale' 	=> $this->view->currentLocale()
		);

		$resultSet = $table->fetchAll($where, $order, $limit);
		$result = array();

		foreach($resultSet as $item)
		{
			$result[$item->id] = $item;
		}

		$this->currentSet = $result;

		return $this;
	}

	public function render()
	{
		if(null !== ($partial = $this->getPartial()))
		{
			return $this->view->partial(array('container' => $this->currentSet));
		}

		$result = '<ul>';

		foreach($this->currentSet as $item)
		{
			$result .= '<li>'.$item->title.'</li>';
		}

		$result .= '</ul>';

		return $result;
	}

	public function __toString()
	{
		return $this->render();
	}

	public function __invoke()
	{
		return $this;
	}

	public function getCurrentSet()
	{
		return $this->currentSet;
	}
}
?>