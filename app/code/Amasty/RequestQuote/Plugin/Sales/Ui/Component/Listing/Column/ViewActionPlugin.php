<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Sales\Ui\Component\Listing\Column;

use Amasty\RequestQuote\Controller\Adminhtml\Quote\Create\FromOrder;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Ui\Component\Listing\Column\ViewAction;

/**
 * Class ViewActionPlugin
 */
class ViewActionPlugin
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    public function __construct(UrlInterface $urlBuilder, AuthorizationInterface $authorization)
    {
        $this->urlBuilder = $urlBuilder;
        $this->authorization = $authorization;
    }

    /**
     * @param ViewAction $subject
     * @param array $dataSource
     * @return array
     */
    public function afterPrepareDataSource(ViewAction $subject, $dataSource)
    {
        if ($this->authorization->isAllowed(FromOrder::ADMIN_RESOURCE)) {
            if (isset($dataSource['data']['items'])) {
                foreach ($dataSource['data']['items'] as & $item) {
                    if (isset($item['entity_id']) && $subject->getData('config/urlEntityParamName') == 'order_id') {
                        $item[$subject->getData('name')]['clone_as_quote'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'amasty_quote/quote_create/fromOrder',
                                ['order_id' => $item['entity_id']]
                            ),
                            'label' => __('Clone as Quote')
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
