<?php  
namespace TypoReport\View\Helper;

use Zend\View\Helper\AbstractHelper;

class TypoAlert extends AbstractHelper
{
	/**
	 * @var array Reports
	 */
	protected $reports;

	/**
	 * Gets TypoReport service
	 *
	 * @return TypoReport\TypoReport
	 */
	public function getTypoReport()
	{
		return $this->getView()
				->getHelperPluginManager()
				->getServiceLocator()
				->get('TypoReport\TypoReport');
	}

	/**
	 * Gets not corrected typos
	 *
	 * @param mixed $order
	 * @param mixed $limit 
	 * @return array
	 */
	public function getUncorrected($order = 'id DESC', $limit = null)
	{
		if(null === $this->reports)
		{
			$this->reports = array();

			foreach($this->getTypoReport()->fetchAll($order, $limit) as $report)
			{
				$this->reports[] = $report;
			}
		}

		return $this->reports;
	}

	/**
	 * Invoke
 	 *
 	 * @param string $partial
 	 * @return Partial|TypoAlert
	 */
	public function __invoke($partial = null)
	{
		if(null === $partial)
		{
			return $this;
		}

		return $this->getView()->partial($partial, array('reports' => $this->getUncorrected()));
	}
}
?>