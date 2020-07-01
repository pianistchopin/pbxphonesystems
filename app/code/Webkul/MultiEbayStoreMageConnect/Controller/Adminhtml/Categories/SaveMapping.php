<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MultiEbayStoreMageConnect\Api\EbayaccountsRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Api\EbaycategoryRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

class SaveMapping extends Categories
{

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\Data
     */
    protected $_helper;

    /**
     * @var Webkul\MultiEbayStoreMageConnect\Model\EbaycategoryRepository
     */
    protected $_ebayCategoryRepository;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Categoriesspecification
     */
    protected $_categorySpecification;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Ebaycategorymap
     */
    protected $_ebayCategoryMap;

    /**
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data                   $dataHelper
     * @param \Magento\Framework\View\Result\PageFactory                      $resultPageFactory
     * @param EbaycategoryRepositoryInterface                                 $ebayCategoryRepository
     * @param \Webkul\MultiEbayStoreMageConnect\Model\Categoriesspecification $categorySpecification
     * @param \Webkul\MultiEbayStoreMageConnect\Model\Ebaycategorymap         $ebayCategoryMap
     * @param JsonFactory                                                     $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $dataHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        EbaycategoryRepositoryInterface $ebayCategoryRepository,
        \Webkul\MultiEbayStoreMageConnect\Model\Categoriesspecification $categorySpecification,
        \Webkul\MultiEbayStoreMageConnect\Model\Ebaycategorymap $ebayCategoryMap,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_ebayCategoryRepository = $ebayCategoryRepository;
        $this->_categorySpecification = $categorySpecification;
        $this->_ebayCategoryMap = $ebayCategoryMap;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $status = true;
        $mappingData = $this->getRequest()->getParams();

        $helper = $this->_dataHelper;

        $leafEbayCategory = $mappingData['ebayLeafCate'];
        $leafMageCategory = $mappingData['mageLeafCate'];

        $msgResponse = $this->_getResponceMsgAsRequest(
            $leafEbayCategory,
            $leafMageCategory,
            $helper,
            $mappingData['id']
        );
        $response = [];
        if ($msgResponse) {
            $status = false;
            $msg = $msgResponse;
        } else {
            try {
                $ebayExistCatColl = $this->_ebayCategoryRepository->getCollectionByEbayCateId($leafEbayCategory);

                if ($ebayExistCatColl->getSize()) {
                    $ebayStoreConfig = $helper->geteBayStoreDetails($mappingData['id']);
                    $attributeSetId = $ebayStoreConfig->getAttributeSetId();
                    foreach ($ebayExistCatColl as $ebayExistCat) {
                        $ebayExistCat = $ebayExistCat;
                    }
                    $ebayCategoryName = $ebayExistCat->getEbayCatName();

                    $client = $helper->getEbayAPI($mappingData['id']);

                    /**for get ebay category condition and variations **/

                    $params = ['Version' => 891,
                                'DetailLevel' => 'ReturnAll',
                                'WarningLevel' => 'High',
                                'CategoryID' => $leafEbayCategory,
                                'ViewAllNodes' => true,
                              ];
                    $results = $client->GetCategoryFeatures($params);

                    if (isset($results->Ack)) {
                        $varsEnabled = 0;
                        if (isset($results->Category->VariationsEnabled)) {
                            $varsEnabled = (int) $results->Category->VariationsEnabled;
                        }
                        $conditionAttr = 'N/A';
                        if (isset($results->Category->ConditionValues)) {
                            $conditionAttr = $helper->createProConditionAttr(
                                $results->Category->ConditionValues,
                                $ebayCategoryName,
                                $attributeSetId
                            );
                        }

                        // get EAN and UPC status
                        $eanUpcStatus = $this->getEanUpcStatus($results->Category, $attributeSetId);

                        //get specification of category form eBay
                        $params = [
                          'CategorySpecific' => ['CategoryID' => $leafEbayCategory],
                          'MaxValuesPerName' => 2147483647,
                          'Version' => 891,
                        ];
                        
                        $results = $client->GetCategorySpecifics($params);
                        //Save specification and Attribute as category
                        $this->_saveCategorySpecificsAndAttribute(
                            $results,
                            $leafEbayCategory,
                            $leafMageCategory,
                            $ebayCategoryName,
                            $helper,
                            $attributeSetId
                        );
                        /* save maped data for eBay and store category**/
                        $mapData = [
                                   'mage_cat_id' => $leafMageCategory,
                                   'ebay_cat_id' => $leafEbayCategory,
                                   'ebay_cat_name' => $ebayCategoryName,
                                   'pro_condition_attr' => $conditionAttr,
                                   'variations_enabled' => $varsEnabled,
                                   'ean_status' => $eanUpcStatus['ean_status'],
                                   'upc_status' => $eanUpcStatus['upc_status'],
                                   'created' => date('Y-m-d H:i:s'),
                                   'rule_id' => $mappingData['id']
                                 ];
                        $mapEbayMageCat = $this->_ebayCategoryMap;
                        $mapEbayMageCat->setData($mapData)->save();
                        $msg = 'Category has been successfully mapped with eBay category';
                    } else {
                        $status = false;
                        $msg = 'Check your ebay details';
                    }
                } else {
                    $msg = 'Please first import category from eBay to your store.';
                }
            } catch (\Exception $e) {
                $msg = $e->getMessage();
            }
        }
        $response['status'] = $status;
        $response['msg'] = $msg;
        return $this->resultJsonFactory->create()->setData($response);
    }

    /**
     * @param int $leafEbayCategory
     * @param int $leafMageCategory
     * @param Webkul\Ebaymagentoconnect\Helper\Data $helper
     * @return false|string
     */
    public function _getResponceMsgAsRequest(
        $leafEbayCategory,
        $leafMageCategory,
        $helper,
        $ruleId
    ) {
        $msg = false;
        if ($leafEbayCategory == false && $leafMageCategory == false) {
            $msg = __('Please select atleast one category.');
        } elseif (!$leafEbayCategory) {
            $msg = __('Please select leaf category.');
        } elseif ($helper->isMageCategoryMapped($leafMageCategory, $ruleId)) {
            $msg = __('This Store category already mapped.');
        }
        return $msg;
    }

    /**
     * @param object $results
     * @param int $leafEbayCategory
     * @param int $leafMageCategory
     * @param string $ebayCategoryName
     * @param Webkul\Ebaymagentoconnect\Helper\Data $helper
     * @return void
     */
    private function _saveCategorySpecificsAndAttribute(
        $results,
        $leafEbayCategory,
        $leafMageCategory,
        $ebayCategoryName,
        $helper,
        $attributeSetId
    ) {
        if (isset($results->Ack) && $results->Ack == 'Success'
            && isset($results->Recommendations->NameRecommendation)) {
            foreach ($results->Recommendations->NameRecommendation as $eBaySpecification) {
                if (isset($eBaySpecification->ValueRecommendation)) {
                    $mageAttrCode = $helper->createProductAttribute(
                        $eBaySpecification,
                        $ebayCategoryName,
                        $attributeSetId
                    );
                    if ($mageAttrCode != '') {
                        $specficationData = [
                            'ebay_category_id' => $leafEbayCategory,
                            'ebay_specification_name' => $eBaySpecification->Name,
                            'mage_category_id' => $leafMageCategory,
                            'mage_product_attribute_code' => $mageAttrCode,
                        ];
                        $this->_saveSpecificationData($specficationData);
                    }
                }
            }
        }
    }

    /**
     * @param array $specficationData
     * @return void
     */
    private function _saveSpecificationData($specficationData)
    {
        $mapSynceSpecifi = $this->_categorySpecification;
        $mapSynceSpecifi->setData($specficationData)->save();
    }

    /**
     * @param Object $category
     * @param int $attributeSetId
     * @return array
     */
    private function getEanUpcStatus($category, $attributeSetId)
    {
        $eanStatus = 0;
        $upcStatus = 0;
        // EAN Status
        if (isset($category->EANEnabled)) {
            $eanStatus = strtolower($category->EANEnabled);
            $this->_dataHelper->createEanUpcAttribute('ean', $attributeSetId);
        }
        // UPC Status
        if (isset($category->UPCEnabled)) {
            $upcStatus = strtolower($category->UPCEnabled);
            $this->_dataHelper->createEanUpcAttribute('upc', $attributeSetId);
        }
        return ['ean_status' => $eanStatus, 'upc_status' => $upcStatus];
    }
}
