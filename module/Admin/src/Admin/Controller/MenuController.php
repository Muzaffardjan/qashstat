<?php  
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Admin\Form\Menu;

class MenuController extends AbstractActionController
{
	public function indexAction()
	{
		$this->layout('layout/admin');
		$config 	= $this->getServiceLocator()->get('config');
		$tableMenu 	= $this->getServiceLocator()->get('Menu\Tables\Menu');
		$menus 		= array();

		foreach($tableMenu->fetchAll() as $menu) {
			$menus[] = $menu;
		}

		$positions = array();

		foreach($config['menu']['positions'] as $id => $options) {
			$positions[$id] = $options['name'];
		}

		return array(
			'menus' 	=> $menus,
			'positions' => $positions,
			'locales' 	=> $config['translator']['locales'],
		);
	}

	public function addAction()
	{
		$this->layout('layout/admin');
		$menuForm = new Menu();
		$config = $this->getServiceLocator()->get('config');
		$positions = array();

		foreach($config['menu']['positions'] as $id => $options) {
			$positions[$id] = $options['name'];
		}

		$menuForm->get('position')->setValueOptions($positions);
		$menuForm->get('locale')->setValueOptions($config['translator']['locales']);

		if($this->getRequest()->isPost()) {
			$menuForm->setData($this->getRequest()->getPost());

			if($menuForm->isValid()) {
				$tableMenu = $this->getServiceLocator()->get('Menu\Tables\Menu');
				$menu = new \Menu\ArrayObject\Menu();

				$menu->setData($menuForm->getData());

				$array = $menu->getConfig();

				$menu->structure = \Zend\Json\Json::encode(
					$this->prepareStructure($menu->getConfig())
				);

				$tableMenu->add($menu);

				return $this->redirect()
					->toRoute(
						'admin/menu',
						array(
							'locale' => $this->params('locale')
						)
					);
			}
		}

		return array(
			'form' => $menuForm,
			'locales' => $config['translator']['locales'],
			'modules' => $config['menu']['modules'],
		);
	}

	public function editAction()
	{
		$this->layout('layout/admin');

		$tableMenu = $this->getServiceLocator()->get('Menu\Tables\Menu');
		$request = $this->getRequest();

		if(!$menu = $tableMenu->getMenuById($this->params('menu'))) {
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$menuForm = new Menu();
		$config = $this->getServiceLocator()->get('config');
		$positions = array();

		foreach($config['menu']['positions'] as $id => $options) {
			$positions[$id] = $options['name'];
		}

		$menuForm->get('position')->setValueOptions($positions);
		$menuForm->get('locale')->setValueOptions($config['translator']['locales']);

		if($this->getRequest()->isPost()) {
			$menuForm->setData($this->getRequest()->getPost());

			if($menuForm->isValid()) {
				$menu->exchangeArray($menuForm->getData());
				$array = $menu->getConfig();

				$menu->structure = \Zend\Json\Json::encode(
					$this->prepareStructure($menu->getConfig())
				);

				$tableMenu->edit($menu);

				return $this->redirect()
					->toRoute(
						'admin/menu',
						array(
							'locale' => $this->params('locale')
						)
					);
			}
		} else {
			$menuForm->setData($menu->getData());
		}

		return array(
			'form' 		=> $menuForm,
			'locales' 	=> $config['translator']['locales'],
			'modules'	=> $config['menu']['modules'],
			'menu'		=> $menu,
		);
	}

	public function deleteAction()
	{
		$this->layout('layout/admin');

		$tableMenu = $this->getServiceLocator()->get('Menu\Tables\Menu');
		$request = $this->getRequest();

		if(!$menu = $tableMenu->getMenuById($this->params('menu'))) {
			$this->getResponse()->setStatusCode(404);
			return;
		}

		if($request->getQuery('confirm')) {
			$tableMenu->delete($menu->id);

			return $this->redirect()
				->toRoute(
					'admin/menu',
					array(
						'locale' => $this->params('locale')
					)
				);
		}

		return array(
			'menu' => $menu,
		);
	}

	protected function prepareStructure($array)
	{
		foreach($array as $key => $item) {
			if(isset($item['id'])) {
				unset($array[$key]['id']);
			}

			if(isset($item['children']) && is_array($item['children'])) {
				$array[$key]['pages'] = $this->prepareStructure($item['children']);
			}
		}

		return $array;
	}
}