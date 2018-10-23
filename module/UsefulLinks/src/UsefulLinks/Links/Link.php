<?php  
namespace UsefulLinks\Links;

class Link extends AbstractLink
{
	/**
	 * @var string|int $id Id of link
	 */
	public $id;

	/**
	 * @var string $locale Locale string
	 */
	public $locale;

	/**
	 * @var string $url Url of link
	 */
	public $url;

	/**
	 * @var string $title Title of link
	 */
	public $title;

	/**
	 * @var string $image Image of link
	 */
	public $image;

	/**
	 * @var string|int $order Order in link
	 */
	public $order;
}
?>