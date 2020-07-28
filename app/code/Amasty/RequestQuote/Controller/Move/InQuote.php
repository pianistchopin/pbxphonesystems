<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Move;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class InQuote extends AbstractMove
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $currentQuote = $this->getSession('checkout')->getQuote();
        try {
            $this->requestRepository->get($currentQuote->getId());
            $quoteAlreadyInCart = true;
        } catch (NoSuchEntityException $entityException) {
            $quoteAlreadyInCart = false;
        }
        if ($currentQuote->getId() &&
            !$quoteAlreadyInCart
        ) {
            if ($this->swapQuote($this->getQuote(), $currentQuote, false, true)) {
                $result->setUrl($this->_url->getUrl('amasty_quote/cart'));
            } else {
                $result->setUrl($this->_url->getUrl('checkout/cart'));
            }
        } else {
            $this->messageManager->addErrorMessage(
                __('This is approved quote.')
            );
            $result->setUrl($this->_url->getUrl('checkout/cart'));
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getType()
    {
        return 'request';
    }
}
