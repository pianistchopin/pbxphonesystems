<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ves\PageBuilder\Controller\Livecss;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Generate extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $_dataHelper;
     /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filesystem;

    /**
     * Admin session model
     *
     * @var null|AuthSession
     */
    protected $_adminSession = null;

    /**
     * Retrieve admin session model
     *
     * @return AuthSession|Session|mixed|null
     */

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Model\Design $catalogDesign
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Ves\PageBuilder\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->_filesystem = $filesystem;
        $this->_adminSession = $authSession;
    }

    public function isAllowCurrentIp() {
        $allowedIPsString = $this->_dataHelper->getConfig("general/allowedIPs", null, "", "veslivecss");

        // remove spaces from string
        $allowedIPsString = preg_replace('/ /', '', $allowedIPsString);

        $allowedIPs = array();

        if ('' !== trim($allowedIPsString)) {
            $allowedIPs = explode(',', $allowedIPsString);
        }

        $currentIP = $_SERVER['REMOTE_ADDR'];

        $allowFrontendForAdmins = $this->_dataHelper->getConfig("general/allowFrontendForAdmins", null, "1", "veslivecss");

        $adminIp = null;

        if (1 == $allowFrontendForAdmins && $this->_isAdminLoggedIn()) {
            return true;
        }

        if(empty($allowedIPs) || in_array($currentIP, $allowedIPs)) {
            // current user allowed to access website?
            return true;
        }

        return false;
    }
    
    protected function _getSession()
    {
        if ($this->_adminSession === null) {
            $this->_adminSession = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');
        }
        return $this->_adminSession;
    }

    protected function _isAdminLoggedIn() {
        $admin_session = $this->_getSession();
        if($admin_session) {
            if($admin_user = $admin_session->getUser()) {
                $admin_user_id = $admin_user->getUserId();
                if(0 < (int)$admin_user_id) {
                    return true;
                }
            }
        } else {
            if(isset($_SESSION['logged_admin']) && $_SESSION['logged_admin']) {
                return (isset($_SESSION['logged_admin'][0]) && session_id($_SESSION['logged_admin'][0]))?true:false;
            }
        }
        return false;
    }

    private  function getPubDirPath( $path_type = "") {
        $path_type = $path_type?$path_type:DirectoryList::PUB;
        return $this->_filesystem->getDirectoryRead($path_type)->getAbsolutePath();
    }

    private function getCustomizePath( $custom_css_folder_path = ""){
        $path = $this->getPubDirPath() .'pagebuilder'.DIRECTORY_SEPARATOR.'livecss'.DIRECTORY_SEPARATOR.'customize'.DIRECTORY_SEPARATOR;


        if($custom_css_folder_path) {
            $custom_css_folder_path = $this->getPubDirPath().$custom_css_folder_path.DIRECTORY_SEPARATOR;
            if(is_dir($custom_css_folder_path)) {
                $path = $custom_css_folder_path;
            }
        }

        return $path;
    }

    /**
     * Category view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $show = $this->_dataHelper->getConfig('general/show', null, 0, 'veslivecss');
        $allow_save_profile = $this->_dataHelper->getConfig("general/allow_save_profile", null, "0", "veslivecss");

        if(!$show || !$allow_save_profile || !$this->isAllowCurrentIp()) {
            return $this->goBack();
        }
        
        if ($data = $this->getRequest()->getPost()) {
            $selectors = $data['customize'];
            $matches = $data["customize_match"];
            $save_path = $data['save_path'];
            $pattern_base_url = $data['pattern_base_url'];

            $output = '';
            $cache = array();

            $themeCustomizePath = $this->getCustomizePath( $save_path );

            try {
                foreach( $selectors as $match => $customizes  ){
                    $output .= "\r\n/* customize for $match */ \r\n";
                    foreach( $customizes as $key => $customize ){
                        if( isset($matches[$match]) && isset($matches[$match][$key]) ){
                            $tmp = explode("|", $matches[$match][$key]);

                            if( trim($customize) ) {
                                $output .= $tmp[0]." { ";
                                if( strtolower(trim($tmp[1])) == 'background-image'){
                                    $output .= $tmp[1] . ':url('.$customize .')!important';
                                } elseif( strtolower(trim($tmp[1])) == 'font-size' ){
                                    $output .= $tmp[1] . ':'.$customize.'px!important';   
                                } elseif(strtolower(trim($tmp[1])) == 'customcss'  ){
                                    $output .= $this->_compressCssCode( $customize );
                                } else {
                                    $output .= $tmp[1] . ':#'.$customize.'!important';   
                                }
                                
                                $output .= "} \r\n";
                            }
                            $cache[$match][] =  array('val'=>$customize,'selector'=>$tmp[0] );
                        }
                    }   

                }

                if(  !empty($data['saved_file'])  ){

                    if( $data['saved_file'] && file_exists($themeCustomizePath.$data['saved_file'].'.css') ){
                        unlink( $themeCustomizePath.$data['saved_file'].'.css' );
                    }
                    if( $data['saved_file'] && file_exists($themeCustomizePath.$data['saved_file'].'.json') ){
                        unlink( $themeCustomizePath.$data['saved_file'].'.json' );
                    }
                    $nameFile = $data['saved_file'];
                }else {
                    if( isset($data['newfile']) && empty($data['newfile']) ){
                        $nameFile = time();
                    }else {
                        $nameFile = preg_replace("#\s+#", "-", trim($data['newfile']));
                    }
                }

                if( $data['action-mode'] != 'save-delete' ){
                    if( !empty($output) ){
                        $this->_dataHelper->writeToCache( $themeCustomizePath, $nameFile, $output );
                    }
                    if( !empty($cache) ){
                        $this->_dataHelper->writeToCache(  $themeCustomizePath, $nameFile, json_encode($cache), "json" );
                    }

                    $message = __(
                        'Saved custom css file "%1" successfully!',
                        $nameFile
                    );
                    $this->messageManager->addSuccessMessage($message);
                }
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t save custom css file right now.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
            
        }

       return $this->goBack();
        
    }

    protected function goBack($backUrl = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($backUrl || $backUrl = $this->_redirect->getRefererUrl()) {
            $resultRedirect->setUrl($backUrl);
        }
        
        return $resultRedirect;
    }

    private function _compressCssCode( $input_text = "") {
        $output = str_replace(array("\r\n", "\r"), "\n", $input_text);
        $lines = explode("\n", $input_text);
        $new_lines = array();

        foreach ($lines as $i => $line) {
            if(!empty($line))
                $new_lines[] = trim($line);
        }
        return implode($new_lines);
    }
}
