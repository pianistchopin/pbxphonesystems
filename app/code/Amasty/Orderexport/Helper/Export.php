<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderexport
 */

namespace Amasty\Orderexport\Helper;

/**
 * Class Export
 *
 * @package Amasty\Orderexport\Helper
 */
class Export extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TYPE_CSV = '0';
    const TYPE_XML = '2';
    const TYPE_MS_XML = '1';
    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $_directory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var  \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var  \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var  \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_cmsPage;

    /**
     * @var \Magento\Cms\Model\Block
     */
    protected $_cmsBlock;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $_category;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Action flag
     *
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_modelOrder;

    /**
     * Order Invoice Model
     *
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $_modelOrderInvoice;

    /**
     * Order Invoice Model
     *
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_modelOrderConfig;

    /**
     * @type mixed
     */
    private $fileHandler;

    /**
     * @type mixed
     */
    private $filePath;

    /**
     * @type mixed
     */
    private $attachmentFilePath;

    /**@type \Amasty\Orderexport\Model\Profiles */
    private $profile;

    /**@type \Amasty\Orderexport\Helper\Uploader */
    private $_helperUploader;

    /**@type \Amasty\Orderexport\Helper\File */
    private $_helperFile;

    /** @var Filter */
    protected $_helperFilter;

    /** @var  array */
    protected $_mapping;


    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Cms\Model\Page $cmsPage
     * @param \Magento\Sales\Model\Order $modelOrder
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\Order\Invoice $_modelOrderInvoice
     * @param \Magento\Sales\Model\Order\Config $_modelOrderConfig
     * @param \Magento\Cms\Model\Block $cmsBlock
     * @param File $helperFile
     * @param Uploader $helperUploader
     * @param Filter $helperFilter
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Page $cmsPage,
        \Magento\Sales\Model\Order $modelOrder,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Invoice $_modelOrderInvoice,
        \Magento\Sales\Model\Order\Config $_modelOrderConfig,
        \Magento\Cms\Model\Block $cmsBlock,
        \Amasty\Orderexport\Helper\File $helperFile,
        \Amasty\Orderexport\Helper\Uploader $helperUploader,
        \Amasty\Orderexport\Helper\Filter $helperFilter
    ) {
        parent::__construct($context);
        $this->_directory               = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $this->_registry                = $registry;
        $this->_resultPageFactory       = $resultPageFactory;
        $this->_objectManager           = $objectManager;
        $this->_messageManager          = $messageManager;
        $this->_localeDate              = $localeDate;
        $this->_filesystem              = $filesystem;
        $this->_storeManager            = $storeManager;
        $this->layoutFactory            = $layoutFactory;
        $this->_productTypeConfigurable = $catalogProductTypeConfigurable;
        $this->_product                 = $product;
        $this->_actionFlag              = $actionFlag;
        $this->_cmsPage                 = $cmsPage;
        $this->_cmsBlock                = $cmsBlock;
        $this->_modelOrder              = $modelOrder;
        $this->_modelOrderInvoice       = $_modelOrderInvoice;
        $this->_modelOrderConfig        = $_modelOrderConfig;
        $this->_helperFile              = $helperFile;
        $this->_helperUploader          = $helperUploader;
        $this->_helperFilter            = $helperFilter;
    }

    /**
     * @param \Amasty\Orderexport\Model\Profiles $profile
     * @return array|mixed
     */
    protected function _getMapping(\Amasty\Orderexport\Model\Profiles $profile)
    {
        if ($this->_mapping === null) {
            $this->_mapping = $profile->getData('field_mapping') ? @unserialize($profile->getData('field_mapping')) : [];
        }
        return $this->_mapping;
    }

    /**
     * @param array $mapping
     * @param array $orderData
     * @param array $orderItemsData
     * @return array
     */
    protected function _prepareOrderBasedRows(
        array $mapping,
        array $orderData,
        array $orderItemsData
    ){
        return [$this->_prepareOrderBasedFields($mapping, $orderData, $orderItemsData)];
    }

    /**
     * @param array $mapping
     * @param array $orderData
     * @param array $orderItemsData
     * @return array
     */
    protected function _prepareOrderItemBasedRows(
        array $mapping,
        array $orderData,
        array $orderItemsData
    ){

        $rows = [];

        foreach($orderItemsData as $orderItemId => $orderItemData){
            $row = [];

            if (count($mapping) > 0) {
                foreach ($mapping as $config) {
                    if (array_key_exists('value', $config)) {
                        $column = $config['value'];

                        if (array_key_exists($column, $orderItemData)){
                            $row[$column] = $this->_prepareValue($orderItemData[$column]);
                        } elseif (array_key_exists($column, $orderData)) {
                            $row[$column] = $this->_prepareValue($orderData[$column]);
                        }
                    }
                }
            } else { //all fields export
                foreach($orderItemData as $key => $val){
                    $row[$key] = $this->_prepareValue($val);
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }

    protected function _prepareValue($value)
    {
        return str_replace(["\n", "\r"], ' ', $value);
    }

    /**
     * @param array $mapping
     * @param array $orderData
     * @param array $orderItemsData
     * @return array
     */
    protected function _prepareOrderBasedFields(
        array $mapping,
        array $orderData,
        array $orderItemsData
    ){
        $fields = [];
        if (count($mapping) > 0){
            foreach($mapping as $config){
                if (array_key_exists('value', $config)){
                    $column = $config['value'];

                    if (array_key_exists($column, $orderData)){
                        $fields[$column] = $orderData[$column];
                    } else {
                        foreach($orderItemsData as $orderItemId => $orderItemData){
                            if (array_key_exists($column, $orderItemData)){
                                $fields[$column][$orderItemId] = $this->_prepareValue($orderItemData[$column]);
                            }
                        }
                    }
                }
            }
        } else { //all fields export
            foreach($orderData as $key => $val){
                $fields[$key] = $this->_prepareValue($val);
            }

            foreach($orderItemsData as $orderItemId => $orderItemData){
                foreach($orderItemData as $column => $value){
                    if (!array_key_exists($column, $fields)){
                        $fields[$column] = [];
                    }
                    $fields[$column][$orderItemId] = $this->_prepareValue($value);
                }
            }
            unset($fields['entity_id_track']);
        }

        return $fields;
    }

    /**
     * @param $splitOrderItems
     * @param array $orderData
     * @param array $ordersItemsData
     * @param array $mapping
     * @return array
     */
    protected function _getRows(
        $splitOrderItems,
        array $orderData,
        array $ordersItemsData,
        array $mapping
    ){
        $rows = [];

        $orderItemsData = array_key_exists($orderData['entity_id_track'], $ordersItemsData) ?
            $ordersItemsData[$orderData['entity_id_track']] : [];

        if ($splitOrderItems) {
            $rows = $this->_prepareOrderBasedRows($mapping, $orderData, $orderItemsData);
        } else {
            $rows = $this->_prepareOrderItemBasedRows($mapping, $orderData, $orderItemsData);
        }

        return $rows;
    }

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $ordersDataSelection
     * @param \Amasty\Orderexport\Model\Profiles                  $profile
     *
     * @return string
     */
    public function exportOrders($ordersDataSelection, &$profile)
    {
        $lastOrderId   = 0;
        $lastInvoiceId = 0;
        $this->profile = $profile;
        $exportType    = $this->profile->getData('format');
        $fileHead      = $this->profile->getData('export_include_fieldnames');
        $fileHeadCont  = false;
        $mapping = $this->_getMapping($profile);
        $splitOrderItems = $profile->getSplitOrderItems();
        $splitOrderItemsDelim = $profile->getSplitOrderItemsDelim();
        $xmlMainTag = $profile->getXmlMainTag();
        $xmlOrderTag = $profile->getXmlOrderTag();
        $xmlOrderItemTag = $profile->getXmlOrderItemTag();
        $xmlOrderItemsTag = $profile->getXmlOrderItemsTag();

        $staticFieldsBefore = [];
        $staticFieldsAfter = [];
        /** Collect before and after Static fields */
        if ($staticFields = $profile->getStaticFields()) {
            $staticFields = \Zend_Json::decode($staticFields);
            foreach ($staticFields['label'] as $i => $label) {
                if (!empty($label)) {
                    if (!empty($staticFields['position'][$i])) {
                        $staticFieldsAfter[$label] = $staticFields['value'][$i];
                    } else {
                        $staticFieldsBefore[$label] = $staticFields['value'][$i];
                    }
                }
            }
        }

        // initiate file for export
        if (!$this->_helperFile->openFileForWrite($this->profile)) {
            $this->_messageManager->addErrorMessage('We could not create file for export. Please, check file path and folder rights for write.');

            return false;
        }

        if ($exportType === self::TYPE_XML) {
            $this->_helperFile->openXmlFile($xmlMainTag);
        } else if ($exportType === self::TYPE_MS_XML) {
            $this->_helperFile->openMsXmlFile();
        }

        /**@var \Magento\Sales\Model\Order $order */
        // run through all orders and export them
        $pagesTotalNum = $ordersDataSelection->getLastPageNumber();
        $currentPage   = 1;

        do {
            // load collection page by page
            $ordersDataSelection->setCurPage($currentPage);
            $ordersDataSelection->load();

            $ordersItemsData = $this->_helperFilter->getItemsForOrders(
                $profile,
                $ordersDataSelection
            );

            $ordersItems = $this->_helperFilter->getOrdersCollection($ordersDataSelection)
                ->getItems();

            foreach ($ordersDataSelection->getData() as $orderData) {
                if (array_key_exists($orderData['entity_id_track'], $ordersItems)){
                    /** @var \Magento\Sales\Model\Order $order */
                    $order = $ordersItems[$orderData['entity_id_track']];

                    $rows = $this->_getRows($splitOrderItems, $orderData, $ordersItemsData, $mapping);

                    foreach($rows as $fields) {
                        // XML format
                        if (!empty($staticFieldsBefore)) {
                            $fields = $staticFieldsBefore + $fields;
                        }
                        if (!empty($staticFieldsAfter)) {
                            $fields = $fields + $staticFieldsAfter;
                        }
                        if ($exportType === self::TYPE_XML) {
                            // output line
                            $this->_helperFile->writeXML($fields, $xmlOrderTag, $xmlOrderItemsTag, $xmlOrderItemTag);

                        } else if ($exportType === self::TYPE_MS_XML) {
                            // prepare XML header if needed & first time output
                            if ($fileHead && !$fileHeadCont) {
                                $fileHeadCont = $this->_helperFile->writeMsXML(array_keys($fields));
                            }

                            // output line
                            $this->_helperFile->writeMsXML(array_values($fields), $splitOrderItemsDelim);
                        } //CSV format
                        else {
                            // prepare CSV header if needed & first time output
                            if ($fileHead && !$fileHeadCont) {
                                $fileHeadCont = $this->_helperFile->writeCSV(
                                    array_keys($fields),
                                    $this->profile->getData('csv_delim'),
                                    $this->profile->getData('csv_enclose')
                                );
                            }
                            
                            // output line
                            $this->_helperFile->writeCSV($fields,
                                $this->profile->getData('csv_delim'),
                                $this->profile->getData('csv_enclose'),
                                $splitOrderItemsDelim
                            );
                        }
                    }
                    // change order status
                    if ($this->profile->getData('post_status')) {
                        $history = $order->addStatusHistoryComment(__('Amasty Orderexport processing'), $this->profile->getData('post_status'));
                        $history->setIsVisibleOnFront(1);
                        $history->setIsCustomerNotified(0);
                        $history->save();
                        if (!$this->_registry->registry('amorderexport_auto_run_triggered')) {
                            $this->_registry->register('amorderexport_auto_run_triggered', true);
                        }
                        $order->save();
                    };

                    // save max order id and invoice id
                    if ($lastOrderId < $order->getId()) {
                        $lastOrderId = $order->getId();
                        $lastOrderIncrementId = $order->getIncrementId();
                    }
                    if ($lastInvoiceId < $order->getInvoiceCollection()->getLastItem()->getId()) {
                        $lastInvoiceId = $order->getInvoiceCollection()->getLastItem()->getId();
                        $lastInvoiceIncrementId = $order->getInvoiceCollection()->getLastItem()->getIncrementId();
                    }
                }
            }

            // increment vars
            $currentPage++;

            //clear collection and free memory
            $ordersDataSelection->clear();
        } while ($currentPage <= $pagesTotalNum);

        // save stats after run
        $this->profile->setData('run_records', $ordersDataSelection->count());
        $this->profile->unsetData('filename');
        if ($this->profile->getData('increment_auto') && $lastOrderId) {

            $this->profile->setData('filter_number_from', $lastOrderIncrementId);
            $this->profile->setData('filter_number_to', '');
        }
        if ($this->profile->getData('invoice_increment_auto') && $lastInvoiceId) {
            $this->profile->setData('filter_invoice_from', $lastInvoiceIncrementId);
            $this->profile->setData('filter_invoice_to', '');
        }

        // close all opened tags in XML file
        if ($exportType === self::TYPE_XML) {
            $this->_helperFile->closeXmlFile($xmlMainTag);
        } else if ($exportType === self::TYPE_MS_XML) {
            $this->_helperFile->closeMsXmlFile();
        }

        // archive file
        $this->attachmentFilePath = $this->profile->getData('file_path_full') . '.zip';
        $this->_helperFile->zip($this->profile->getData('file_path_full'), $this->attachmentFilePath);

        // send via Email if needed
        if ($this->profile->getData('email_use')) {
            $this->sendEmail();
        }

        // send via FTP if needed
        if ($this->profile->getData('ftp_use')) {
            $this->_helperUploader->uploadFile($this->profile, $this->profile->getData('file_path_full'));
        }

        // save inner var for outer use
        $this->profile->save();
        $profile = $this->profile;

        return true;
    }


    /**
     *  Send profile data row via Email
     */
    private function sendEmail()
    {
        if ($this->profile->getData('email_address')) {
            if ($this->profile->getData('last_increment_id')) {
                $emailSubject = $this->profile->getData('email_subject') . '_' . $this->profile->getData('last_increment_id');
            } else {
                $emailSubject = $this->profile->getData('email_subject');
            }
            $this->profile->setData('email_subject', $emailSubject);

            $this->attachmentFilePath = $this->profile->getData('file_path_full');
            if ($this->profile->getData('email_compress')) {
                $this->attachmentFilePath .= '.zip';
                $this->_helperFile->zip($this->profile->getData('file_path_full'), $this->attachmentFilePath);
            }
            $this->sendExported();
        }
    }

    /**
     *  Send exported file via Email
     */
    private function sendExported()
    {
        $fromValue = 'trans_email/ident_general/email';
        if  ($this->profile->getData('email_from'))
            $fromValue = $this->profile->getData('email_from');

        $from    = $this->scopeConfig->getValue($fromValue);
        $message = "";
        $headers = "From: $from";

        // boundary
        $semiRand = md5(time());
        $boundary = "==Multipart_Boundary_x{$semiRand}x";

        // headers for attachment
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$boundary}\"";

        // multiPart boundary
        $message .= "\r\n\r\n" . __("Message was sent from Amasty OrderExport and includes orders export file attached. \r\n\r\nPlease, do not answer on this email.") . "\r\n\r\n";
        $message = "--{$boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";

        // preparing attachments
        $message .= "--{$boundary}\n";
        $fp   = @fopen($this->attachmentFilePath, "rb");
        $data = @fread($fp, filesize($this->attachmentFilePath));
        @fclose($fp);
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Type: application/octet-stream; name=\"" . basename($this->attachmentFilePath) . "\"\n" .
            "Content-Description: " . basename($this->attachmentFilePath) . "\n" .
            "Content-Disposition: attachment;\n" . " filename=\"" . basename($this->attachmentFilePath) . "\"; size=" . filesize($this->attachmentFilePath) . ";\n" .
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        $message .= "--{$boundary}--";
        $returnpath = "-f" . $from;
        mail($this->profile->getData('email_address'), $this->profile->getData('email_subject'), $message, $headers, $returnpath);
    }
}
