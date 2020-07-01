<?php


namespace ProVu\DownloadGuide\Cron;

class DownloadGuide
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $username = 'pbxphonesys.apitest';
		$password = 'mBB:q8K~S';
		$this->downloadGuide($username, $password);
    }
	
	public function downloadGuide($username, $password) 
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/provu.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Guide Download Started.');
		ini_set("memory_limit","-1");
		$server = 'https://secure.provu.co.uk/prosys/price_list.php?XML=yes';
		$context = stream_context_create(array(
				'http' => array(
					'header'  => "Authorization: Basic " . base64_encode("$username:$password")
				)
			)
		);
		$data = file_get_contents($server, false, $context);
		$xml = simplexml_load_string($data);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$directory_list = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
		$state = $objectManager->get('Magento\Framework\App\State');
		$scopeConfig = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
		$priceDiscount = $scopeConfig->getValue('products/options_price/price_discount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		
		if ($xml === false) {
			$logger->error('Failed loading XML.');
			foreach(libxml_get_errors() as $error) {
				$logger->error($error->message);
			}
		} else {
			$json  = json_encode($xml);
			$catalogData = json_decode($json, true);
			$catalogData = $catalogData['line'];
			$baseDomain = 'https://www.provu.co.uk/products/';
			foreach ($catalogData as $catalogElement) {
				$sku = $catalogElement['item'];
				$class = $catalogElement['class'];
				
				//DataSheet PDF
				$dataSheetUrl = $baseDomain.$class.'/'.$sku.'/'.$sku.'.pdf';
				$ch = curl_init($dataSheetUrl);
				curl_setopt($ch, CURLOPT_NOBODY, true);
				curl_exec($ch);
				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($code == 200) {
					$datasheetPath = $directory_list->getPath('media')."/guides/".basename($dataSheetUrl);
					file_put_contents($datasheetPath, file_get_contents($dataSheetUrl));
				}
				
				//Manufacturer's user Guide
				$userGuideUrl = $baseDomain.$class.'/'.$sku.'/'.$sku.'-userguide.pdf';
				$ch = curl_init($userGuideUrl);
				curl_setopt($ch, CURLOPT_NOBODY, true);
				curl_exec($ch);
				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($code == 200) {
					$userGuidePath = $directory_list->getPath('media')."/guides/".basename($userGuideUrl);
					file_put_contents($userGuidePath, file_get_contents($userGuideUrl));
				}
				
				//Quick Start Guide
				$qsGuideUrl = $baseDomain.$class.'/'.$sku.'/'.$sku.'-qsquide.pdf';
				$ch = curl_init($qsGuideUrl);
				curl_setopt($ch, CURLOPT_NOBODY, true);
				curl_exec($ch);
				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($code == 200) {
					$userGuidePath = $directory_list->getPath('media')."/guides/".basename($qsGuideUrl);
					file_put_contents($userGuidePath, file_get_contents($qsGuideUrl));
				}
			}
		}
		$logger->info('Guide Download Finished.');
	}
}
