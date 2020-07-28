<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Move;

use Magento\Framework\Controller\ResultFactory;
use Amasty\RequestQuote\Model\Source\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ResponseInterface;

class InCart extends AbstractMove
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ((int)$this->getRequest()->getParam('quote_id', null)) {
            $approvedQuote = $this->getQuote();
            $currentQuote = $this->getSession('checkout')->getQuote();
            try {
                $this->requestRepository->get($currentQuote->getId());
                $quoteAlreadyInCart = true;
            } catch (NoSuchEntityException $entityException) {
                $quoteAlreadyInCart = false;
            }
            if ($approvedQuote->getId() &&
                $approvedQuote->getStatus() == Status::APPROVED &&
                !$quoteAlreadyInCart
            ) {
                $this->swapQuote($approvedQuote, $currentQuote, true, false);
                $result->setUrl($this->getRedirectUrl());
            } else {
                $this->messageManager->addErrorMessage(
                    __('It is possible to process one Quote at a time. You have already added Quote in your cart. Please proceed to checkout.')
                );
                $result->setUrl($this->getRedirectUrl());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Something wrong.'));
            $result->setUrl($this->_redirect->getRefererUrl());
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getType()
    {
        return 'checkout';
    }

    /**
     * @return string
     */
    private function getRedirectUrl()
    {
        $url = $this->getRequest()->getParam('redirect_url', 'checkout/cart');

        return $this->_url->getUrl($url);
    }
}
