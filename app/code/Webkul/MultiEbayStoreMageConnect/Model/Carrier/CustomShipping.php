<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\Xml\Security;

class CustomShipping extends AbstractCarrierOnline implements CarrierInterface
{
    const CODE = 'wk_multiebayship';
    protected $_code = self::CODE;
    protected $_result;
    protected $_baseCurrencyRate;
    protected $_xmlAccessRequest;
    protected $_localeFormat;
    protected $_logger;
    protected $_errors = [];
    protected $_isFixed = true;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig,
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory,
     * @param \Psr\Log\LoggerInterface                                    $logger,
     * @param Security                                                    $xmlSecurity,
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory            $xmlElFactory,
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateFactory,
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
     * @param \Magento\Shipping\Model\Tracking\ResultFactory              $trackFactory,
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory        $trackErrorFactory,
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory       $trackStatusFactory,
     * @param \Magento\Directory\Model\RegionFactory                      $regionFactory,
     * @param \Magento\Directory\Model\CountryFactory                     $countryFactory,
     * @param \Magento\Directory\Model\CurrencyFactory                    $currencyFactory,
     * @param \Magento\Directory\Helper\Data                              $directoryData,
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface        $stockRegistry,
     * @param \Magento\Framework\Locale\FormatInterface                   $localeFormat,
     * @param \Magento\Backend\Model\Session                              $backendSession,
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Backend\Model\Session $backendSession,
        array $data = []
    ) {
        $this->_localeFormat = $localeFormat;
        $this->_backendSession = $backendSession;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
    }

    public function getAllowedMethods()
    {
    }

    public function collectRates(RateRequest $request)
    {
        //get shipping data from session
        $shipInfo = $this->_backendSession->getMultiEbayShipDetail();
        if ($shipInfo) {
            $result = $this->_rateFactory->create();

            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($shipInfo['method']);
            // Use method name
            $method->setMethod($this->_code);
            $method->setMethodTitle($shipInfo['method']);
            $method->setCost($shipInfo['cost']);
            $method->setPrice($shipInfo['cost']);
            $result->append($method);
            return $result;
        } else {
            return false;
        }
    }

    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        return true;
    }
}
