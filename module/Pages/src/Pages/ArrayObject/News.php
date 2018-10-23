<?php  
namespace Pages\ArrayObject;

use Indexing\Index\IndexInterface;

class News implements \Pages\AlternativesChain\ChainedItemInterface, IndexInterface
{
    public $id;
    public $locale;
    public $category;
    public $url;
    public $title;
    public $description;
    public $body;
    public $image;
    public $added;
    public $chain;

    private $_alternatives = array();

    public function exchangeArray($data)
    {
        if(!is_array($data) && !($data instanceof \Traversable)) {
            return;
        }

        foreach($this->getData() as $property => $value) {
            if(isset($data[$property])) {
                $this->{$property} = $data[$property];
            }
        }
    }

    public function setAlternatives($alternatives)
    {
        foreach($alternatives as $locale => $alternative) {
            if($alternative->getItemId() == $this->getItemId()) {
                continue;
            }

            $this->_alternatives[$locale] = $alternative;
        }

        return $this;
    }

    public function getAlternatives($locale = null)
    {
        if($locale) {
            if(isset($this->_alternatives[$locale])) {
                return $this->_alternatives[$locale];
            }

            return null;
        }

        return $this->_alternatives;
    }

    /* ChainedItemInterface interface methods */

    /**
     * {@inheritDoc}
     */
    public function getItemId()
    {
        return $this->id;
    }

    /**
     *  {@inheritDoc}
     */
    public function getItemLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritDoc}
     */
    public function getChainId()
    {
        return $this->chain;
    }

    /**
     * {@inheritDoc}
     */
    public function setChainId($chainId)
    {
        $this->chain = $chainId;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexSpecification()
    {
        $stripTags = new \Zend\Filter\StripTags(['allowTags' => ['a', 'img']]);
        
        return [
            'fields' => [
                [
                    'type'  => 'unindexed',
                    'name'  => 'type',
                    'value' => 'Pages\ArrayObject\News',
                ],
                [
                    'type'  => 'unindexed',
                    'name'  => 'page',
                    'value' => $this->id,
                ],
                [
                    'type'  => 'unindexed',
                    'name'  => 'url',
                    'value' => $this->url,
                ],
                [
                    'type'  => 'unindexed',
                    'name'  => 'chain',
                    'value' => $this->chain,
                ],
                [
                    'type'  => 'text',
                    'name'  => 'title',
                    'value' => $this->title,
                ],
                [
                    'type'  => 'text',
                    'name'  => 'description',
                    'value' => $stripTags->filter($this->description),
                ],
                [
                    'type'  => 'text',
                    'name'  => 'body',
                    'value' => $stripTags->filter($this->body),
                ],
            ],
        ];
    }

    public function flush()
    {
        $arrayCopy = $this->getData();

        foreach($arrayCopy as $key => $value) {
            $this->{$key} = null;
        }
    }

    /**
     * Exchanges array with cleaning
     *
     * @param array $array
     * @return Pages\ArrayObject\Page
     */
    public function setData($array)
    {
        $this->flush();
        $this->exchangeArray($array);

        return $this;
    }

    public function getData()
    {
        return \Application\Library\Stdlib::get_public_vars($this);
    }
}
?>