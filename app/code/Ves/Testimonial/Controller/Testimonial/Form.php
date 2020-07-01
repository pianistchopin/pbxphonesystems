<?php 
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Testimonial
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Testimonial\Controller\Testimonial; 

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class Form extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
    * @var \Ves\Testimonial\Model\Testimonial
    **/
    protected $testimonialCollection;

    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     ":?"
     */
     protected $_stdTimezone;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
    * @var \Ves\Testimonial\Helper\Data
    */
    protected $_helper;

    private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";
    private $_secret;
    private static $_version = "php_1.0";
    
    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context       $context               
     * @param \Magento\Framework\View\Result\PageFactory  $resultPageFactory     
     * @param \Ves\Testimonial\Model\Testimonial          $testimonialCollection 
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone           
     * @param \Magento\Store\Model\StoreManager           $storeManager          
     * @param \Magento\Framework\Filesystem               $filesystem            
     * @param \Ves\Testimonial\Helper\Data                $helper                
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ves\Testimonial\Model\Testimonial $testimonialCollection,
        \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Ves\Testimonial\Helper\Data $helper
        ){
        $this->resultPageFactory = $resultPageFactory;
        $this->testimonialCollection = $testimonialCollection;
        $this->_stdTimezone = $stdTimezone;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $context->getObjectManager();
        $this->_fileSystem = $filesystem;
        $this->_helper = $helper;
        parent::__construct($context);
    } 
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $param=$this->getRequest()->getParams();

        // reCaptcha begin
        if(isset($_POST['g-recaptcha-response']) && ((int)$_POST['g-recaptcha-response']) === 0) {
            $this->messageManager->addError(__('Please check reCaptcha and try again.'));
            $this->_redirect($param['return_url']);
            return;
        }
        if(isset($_POST['g-recaptcha-response'])){
            $captcha=$_POST['g-recaptcha-response'];
            $secretKey = $this->_helper->getCaptchaSecretKey();
            $ip = $_SERVER['REMOTE_ADDR'];
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
            $responseKeys = json_decode($response,true);
            if(intval($responseKeys["success"]) !== 1) {
                $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                $this->_redirect($param['return_url']);
                return;
            }
        }
        // reCaptcha End
        $store = $this->_storeManager->getStore();
        $param=$this->getRequest()->getParams();
        $dateTimeNow = $this->_stdTimezone->date()->format('Y-m-d H:i:s');
        $param['is_active'] = '0';
        $param['create_time'] = $dateTimeNow;
        $param['image'] = $this->uploadImage();
        $param['stores'] = [$store->getId()];

        $this->testimonialCollection->setData($param);
        try{
            $this->testimonialCollection->save();
            $this->messageManager->addSuccess(__('Thank for your testimonial.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving your testimonial.'));
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;

    }
    public function uploadImage()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (isset($_FILES['image']) && $_FILES['image']['name']!='') 
        {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => 'image')
                );

            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'ves/testimonial/';
            try {
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                    );

                return $mediaFolder.$result['name'];
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addError($e->getMessage());
            }
        }
        return;
    }
}