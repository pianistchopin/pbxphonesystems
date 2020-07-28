<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Amasty\RequestQuote\Controller\Cart
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['qty'])) {
                $params['qty'] = $this->getLocateFilter()->filter($params['qty']);
            }

            $productId = (int)$this->getRequest()->getParam('product', false);;
            $related = $this->getRequest()->getParam('related_product');

            try{
                $product = $this->cart->getProductById($productId);
            } catch (NoSuchEntityException $e) {
                return $this->goBack();
            }

            $this->cart->addProduct($productId, $params);

            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }


            $this->cart->save();

            if (!$this->getCheckoutSession()->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your quote cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                }
                if ($returnUrl = $this->getRequest()->getParam('return_url_quote_added', null)) {
                    $returnUrl = $this->_url->getUrl($returnUrl);
                }

                return $this->goBack($returnUrl, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->getCheckoutSession()->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->getEscaper()->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->getEscaper()->escapeHtml($message)
                    );
                }
            }

            $url = $this->getCheckoutSession()->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->cartHelper->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t add this item to your quote cart right now.'));
            return $this->goBack();
        }
    }

    /**
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return $this->_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson(
            $this->jsonEncoder->encode($result)
        );
    }
}
