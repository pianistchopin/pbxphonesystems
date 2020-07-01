<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_BaseWidget
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block\Widget;
use Ves\BaseWidget\Block\AbstractWidget;

class FeedReader extends AbstractWidget{
	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $logger;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->logger = $context->getLogger();

		if($this->hasData("template")) {
            $my_template = $this->getData("template");
        } elseif(isset($data['template']) && $data['template']) {
            $my_template = $data['template'];
        } else{
            $my_template = "widget/feedreader_sidebar.phtml";
        }
        $this->setTemplate($my_template);
	}

	/**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => 1800, //60*30 - 30 minutes
            'cache_tags' => ['ves_feedreader_widget']
            ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $uri = $this->getData('uri');
        $conditions = md5($uri);

        return [
        'VES_FEEDREADER_WIDGET',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
        $conditions
        ];
    }

	
	/**
	 * Returns the feed
	 * 
	 * Tries to create the feed from the URI.
	 * 
	 * @return Zend_Feed
	 */
	protected function getFeed()
	{
		$feed = $this->getData('feed');
		if (is_null($feed)) {
			$uri = $this->getData('uri');
			if (!is_null($uri)) {
				try {
					$feed = \Zend_Feed::import($uri);
					$this->setData('feed', $feed);
				}
				catch (\Zend_Http_Client_Exception $e) {
					$this->logger->critical($e);
				}
				catch (\Zend_Feed_Exception $e) {
					$this->logger->critical($e);
				}
			}
		}
		return $feed;
	}
	
	/**
	 * Returns the item count
	 * 
	 * @return int
	 */
	public function getItemCount()
	{
		$itemCount = 0;
		if (!is_null($this->getFeed())) {
			$itemCount = $this->getData('item_count');
			if ($this->getFeed()->count() < $itemCount || is_null($this->getData('item_count'))) {
				$itemCount = $this->getFeed()->count();
			}
		}
		return $itemCount;
	}
	
	/**
	 * Returns the feed title
	 * 
	 * If no feed is defined an empty string is returned.
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		$title = '';
		if (!is_null($this->getData('title'))) {
			$title = $this->getData('title');
		}
		else if (!is_null($this->getFeed())) {
			$title = $this->getFeed()->title();
		}
		return $title;
	}
	
	
	/**
	 * Returns the feed items
	 * 
	 * @return array
	 */
	public function getItems()
	{
		$return = array();
		if (!is_null($this->getFeed())) {
			$itemCount = 0;
			foreach ($this->getFeed() as $item) {
				$return[] = $item;
				if (++$itemCount >= $this->getItemCount()) {
					break;
				}
			}
		}
		return $return;
	}
	
}

