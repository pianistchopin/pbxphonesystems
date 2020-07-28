<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey as DataFormKey;
use Magento\PageCache\Observer\FlushFormKey;

class CustomerFlushFormKey
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var DataFormKey
     */
    private $dataFormKey;

    public function __construct(Session $session, DataFormKey $dataFormKey)
    {
        $this->session = $session;
        $this->dataFormKey = $dataFormKey;
    }

    /**
     * @param FlushFormKey $subject
     * @param $proceed
     * @param $args
     */
    public function aroundExecute(FlushFormKey $subject, $proceed, ...$args)
    {
        $currentFormKey = $this->dataFormKey->getFormKey();
        $proceed(...$args);
        $beforeParams = $this->session->getBeforeRequestParams();
        if (isset($beforeParams['form_key']) && $beforeParams['form_key'] === $currentFormKey) {
            $beforeParams['form_key'] = $this->dataFormKey->getFormKey();
            $this->session->setBeforeRequestParams($beforeParams);
        }
    }
}
