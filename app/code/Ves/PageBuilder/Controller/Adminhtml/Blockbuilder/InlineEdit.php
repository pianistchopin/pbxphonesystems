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
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Controller\Adminhtml\Blockbuilder;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;

use Ves\PageBuilder\Model\Block as BlockModel;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PageRepository  */
    protected $brandRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /** @var brandModel */
    protected $blockModel;

    /**
     * @param Context $context
     * @param PageRepository $brandRepository
     * @param JsonFactory $jsonFactory
     * @param Ves\Pagebuilder\Model\Block $blockModel
     */
    public function __construct(
        Context $context,
        PageRepository $brandRepository,
        JsonFactory $jsonFactory,
        BlockModel $blockModel
        ) {
        parent::__construct($context);
        $this->pageRepository = $brandRepository;
        $this->jsonFactory = $jsonFactory;
        $this->blockModel = $blockModel;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
                ]);
        }

        foreach (array_keys($postItems) as $blockId) {
            /** @var \Ves\Pagebuilder\Model\block $block */
            $block = $this->_objectManager->create('Ves\PageBuilder\Model\Block');
            $blockData = $postItems[$blockId];

            try {
                $block->load($blockId);
                $block->setData(array_merge($block->getData(), $blockData));
                $block->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithgroupId($block, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithgroupId($block, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPageId(
                    $page,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => 'abc',
            'error' => 'def'
            ]);
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $brand
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithgroupId($block, $errorText)
    {
        return '[Page ID: ' . $block->getId() . '] ' . $errorText;
    }
}