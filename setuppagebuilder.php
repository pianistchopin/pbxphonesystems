<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Magento\Framework\App\Bootstrap;
    include('app/bootstrap.php');
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();


    $modules = array('Ves_All','Ves_Setup','Ves_BaseWidget','Ves_PageBuilder');
    try{
        /* Code to enable a module [ php bin/magento module:enable VENDORNAME_MODULENAME ] */
        $moduleStatus = $objectManager->create('Magento\Framework\Module\Status')->setIsEnabled(true,$modules);


        /* Code to run setup upgrade [ php bin/magento setup:upgrade ] */
        $installerFactory = $objectManager->create('Magento\Setup\Test\Unit\Console\Command\UpgradeCommandTest')->testExecute();

        echo "Setup Upgrade script ran successfully<br />";

        /* Code to clean cache [ php bin/magento:cache:clean ] */
        try{
            $_cacheTypeList = $objectManager->create('Magento\Framework\App\Cache\TypeListInterface');
            $_cacheFrontendPool = $objectManager->create('Magento\Framework\App\Cache\Frontend\Pool');
            $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
            foreach ($types as $type) {
                $_cacheTypeList->cleanType($type);
            }
            foreach ($_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            
            echo "Cache has been cleaned <br />";
        }catch(Exception $e){
            echo $msg = 'Error during cache clean: '.$e->getMessage();die();
        }   
    }catch(Exception $e){
        echo $msg = 'Error during module enabling : '.$e->getMessage();die();
    }
    