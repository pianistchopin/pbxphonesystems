<?php


namespace ProVu\ApiManagement\Cron;

class CatalogSync
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
		$this->productImport($username, $password);
		//$this->categoryImport($username, $password);
		//$this->removeProducts();
    }
	
	public function productImport($username, $password)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/provu.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Provu Product Management Started.');
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
		$scopeConfig = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
		$priceDiscount1 = $scopeConfig->getValue('products/options_price/price_discount1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$priceDiscount2 = $scopeConfig->getValue('products/options_price/price_discount2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$priceDiscount3 = $scopeConfig->getValue('products/options_price/price_discount3', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $priceDiscount = 0;


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
			$productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
			foreach ($catalogData as $catalogElement) {
				$sku = $catalogElement['item'];


//					xiao jin 20200501  discount price --start--
//                    real price is =>
                $price_each = floatval($catalogElement['price_each']);
                $buying_price = $price_each;

                if($price_each > 0 && $price_each < 50){
                    $priceDiscount = $priceDiscount1;
                }
                elseif($price_each >= 50  && $price_each < 101){
                    $priceDiscount = $priceDiscount2;
                }
                elseif($price_each >= 101){
                    $priceDiscount = $priceDiscount3;
                }

                $real_price = floatval($price_each * (100 + floatval($priceDiscount)) / 100); //real_price
//					xiao jin 20200501 discount price --end--


				try {
					$_product = $productRepository->get($sku);
                    $_product->setPrice($real_price);
					$_product->setStockData(array(
						   'is_in_stock' => 1,
						   'qty' => $catalogElement['free_stock'],
						   'is_qty_decimal' => 1));
					$_product->setData('provu_updated_time',date("Y/m/d"));
					$_product->setData('buying_price',$buying_price);
					$galleryImages = $_product->getMediaGalleryImages();
					if (count($galleryImages) == 0) {
						$imagePathArray = array();
						$baseImageUrl = $baseDomain.$catalogElement['class'].'/'.$sku.'/'.$sku.'-large.jpg';
						$ch = curl_init($baseImageUrl);
						curl_setopt($ch, CURLOPT_NOBODY, true);
						curl_exec($ch);
						$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						if ($code !== 200) {
						} else {
							$imagePath = $directory_list->getPath('media')."/import/".basename($baseImageUrl);
							file_put_contents($imagePath, file_get_contents($baseImageUrl));
							$_product->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), false, false);
							$imagePathArray[] = $imagePath;
							//Add Thumbnail Image
							$thumbnailImageUrl = $baseDomain.$catalogElement['class'].'/'.$sku.'/'.$sku.'-thumb.jpg';
							$ch = curl_init($thumbnailImageUrl);
							curl_setopt($ch, CURLOPT_NOBODY, true);
							curl_exec($ch);
							$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

							$imageAltUrls = array();
							for ($i = 1; $i < 5; $i++) {
								$imageUrl = $baseDomain.$catalogElement['class'].'/'.$sku.'/'.$sku.'-'.$i.'-large.jpg';
								$ch = curl_init($imageUrl);
								curl_setopt($ch, CURLOPT_NOBODY, true);
								curl_exec($ch);
								$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
								if ($code == 200) {
									$imagePath = $directory_list->getPath('media')."/import/".basename($imageUrl);
									file_put_contents($imagePath, file_get_contents($imageUrl));
									$_product->addImageToMediaGallery($imagePath, array(), false, false);
									$imagePathArray[] = $imagePath;
								}
							}
							foreach ($imagePathArray as $imagePath) {
								unset($imagePath);
							}
						}
					}
					$productRepository->save($_product);
                    $logger->info('product save');
				} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
					$_product = $objectManager->create('\Magento\Catalog\Model\Product');
					$_product->setAttributeSetId(4);
					$_product->setSku($sku);
					$_product->setTypeId('simple');
					$_product->setStockData(array(
						   'is_in_stock' => 1,
						   'qty' => $catalogElement['free_stock'],
						   'is_qty_decimal' => 1));
					$_product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
					$_product->setName($catalogElement['web_name']);
					$_product->setWeight(floatval($catalogElement['weight']));
					$_product->setData('provu_updated_time',date("Y/m/d"));
					$_product->setData('buying_price',$buying_price);
					$_product->setWebsiteIds(array(1));
					$_product->setData('status',1);
					$_product->setTaxClassId(2);
					$_product->setShortDescription($catalogElement['description']);
					$_product->setPrice($real_price);
					$_product->setUrlKey($sku);
					$manufacturer = $catalogElement['class'];
					if ($manufacturer) {
						$manufacturer = ucfirst($manufacturer);
					}

					$this->setAttributeForProduct($_product, 'manufacturer', $manufacturer, $objectManager);
					//Set Description and Feature from scrapping
					$domain = 'https://secure.provu.co.uk/prosys/item.php?item='.$sku;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $domain);
					curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					$page = curl_exec($ch);
					$scrappedData = $this->scrape_between($page, '<h1>Webpage</h1>', '<img');
					$scrappedData = $this->scrape_between($scrappedData, 'href=', ' target');
					$domain = str_replace('"', '', $scrappedData);

					curl_close($ch);
					if ($domain) {
						$domain = preg_replace("/^http:/i", "https:", $domain);

						$ch = curl_init($domain);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$page = curl_exec($ch);

						$main_scrape_data = $this->scrape_between($page, '<div id="overview" class="tab-content">', '<div id="features" class="tab-content">');
						if ($main_scrape_data) {
							//Case for existing both overview and feature
							$thumbnail_scrape_data = $this->scrape_between($main_scrape_data, '</h2>', '<p>');
							$pos = strpos($main_scrape_data, $thumbnail_scrape_data);
							$main_scrape_data = str_replace(substr($main_scrape_data, $pos, strlen($thumbnail_scrape_data)), '', $main_scrape_data);
							$main_scrape_data = substr($main_scrape_data, 0, -9);
							$featureData = $this->scrape_between($page, '<div id="features" class="tab-content">', '</div>');
							$_product->setDescription($main_scrape_data);
							$_product->setFeature($featureData);
						} else {
							//Case for existing only overview
							$main_scrape_data = $this->scrape_between($page, '<div id="overview" class="tab-content">', '<div id="models" class="tab-content">');
							if ($main_scrape_data) {
								$thumbnail_scrape_data = $this->scrape_between($main_scrape_data, '</h2>', '<p>');
								$pos = strpos($main_scrape_data, $thumbnail_scrape_data);
								$main_scrape_data = str_replace(substr($main_scrape_data, $pos, strlen($thumbnail_scrape_data)), '', $main_scrape_data);
								$main_scrape_data = substr($main_scrape_data, 0, -9);
								$_product->setDescription($main_scrape_data);
							}
						}
						curl_close($ch);
					}
					$imagePathArray = array();
					$baseImageUrl = $baseDomain.$catalogElement['class'].'/'.$sku.'/'.$sku.'-large.jpg';
					$ch = curl_init($baseImageUrl);
					curl_setopt($ch, CURLOPT_NOBODY, true);
					curl_exec($ch);
					$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					if ($code !== 200) {

					} else {
						$imagePath = $directory_list->getPath('media')."/import/".basename($baseImageUrl);
						file_put_contents($imagePath, file_get_contents($baseImageUrl));
						$_product->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), false, false);
						$imagePathArray[] = $imagePath;
						//Add Thumbnail Image
						$thumbnailImageUrl = $baseDomain.$catalogElement['class'].'/'.$sku.'/'.$sku.'-thumb.jpg';
						$ch = curl_init($thumbnailImageUrl);
						curl_setopt($ch, CURLOPT_NOBODY, true);
						curl_exec($ch);
						$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						if ($code == 200) {
							$imagePath_thumb = $directory_list->getPath('media')."/import/".basename($thumbnailImageUrl);
							file_put_contents($imagePath_thumb, file_get_contents($thumbnailImageUrl));
							$_product->addImageToMediaGallery($imagePath_thumb, array('thumbnail'), false, false);
							$imagePathArray[] = $imagePath_thumb;
						}

						$imageAltUrls = array();
						for ($i = 1; $i < 5; $i++) {
							$imageUrl = $baseDomain.$catalogElement['class'].'/'.$sku.'/'.$sku.'-'.$i.'-large.jpg';
							$ch = curl_init($imageUrl);
							curl_setopt($ch, CURLOPT_NOBODY, true);
							curl_exec($ch);
							$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
							if ($code == 200) {
								$imagePath = $directory_list->getPath('media')."/import/".basename($imageUrl);
								file_put_contents($imagePath, file_get_contents($imageUrl));
								$_product->addImageToMediaGallery($imagePath, array(), false, false);
								$imagePathArray[] = $imagePath;
							}
						}
						foreach ($imagePathArray as $imagePath) {
							unlink($imagePath);
						}
					}
                    $logger->info('product save');
					$_product->save();
				}
			}
		}
		$logger->info('Provu Product Management Finished.');
	}
	
	public function categoryImport($username, $password)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/provu.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Provu Category Management Started.');
		$server = 'https://secure.provu.co.uk/prosys/categories.php?XML=yes';
		$context = stream_context_create(array(
				'http' => array(
					'header'  => "Authorization: Basic " . base64_encode("$username:$password")
				)
			)
		);
		$data = file_get_contents($server, false, $context);
		$xml = simplexml_load_string($data);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
		$categoryLinkManagement = $objectManager->get('Magento\Catalog\Api\CategoryLinkManagementInterface');
		$categoryFactory = $objectManager->create('Magento\Catalog\Model\CategoryFactory');
		if ($xml === false) {
			$logger->error('Failed loading XML.');
			foreach(libxml_get_errors() as $error) {
				$logger->error($error->message);
			}
		} else {
			$json  = json_encode($xml);
			$catalogData = json_decode($json, true);
			$catalogData = $catalogData['category'];
			$categoryIDArray = array();
			foreach ($catalogData as $categoryElement) {
				$categoryName = $categoryElement['name'];
				if ($categoryElement['name'] == 'IP Telephone Systems') {
					$categoryName = 'PBX Systems';
				}
				$collection = $categoryFactory->create()->getCollection()->addAttributeToFilter('name', $categoryName)->setPageSize(1);
				if ($collection->getSize()) {
					$categoryId = $collection->getFirstItem()->getId();
				} else {
					$categoryId = 2;
				}
		
				$items = $categoryElement['item'];
				$categoryIds = array();
				$categoryIds[] = $categoryId;
				if (is_array($items)) {
					foreach ($items as $item) {
						$categoryIDArray[$item][] = $categoryId;
					}
				} else {
					$categoryIDArray[$items][] = $categoryId;
				}
			}
			foreach ($categoryIDArray as $key => $value) {
				$_productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')->addFieldToFilter('sku',array("eq" => $key));
				if(count($_productCollection)>0){
					$_product = $_productCollection->getFirstItem();
					$product = $objectManager->create('Magento\Catalog\Model\Product')->load($_product->getId());
					if (count($product->getCategoryIds()) > 0) {
						continue;
					} else {
						$categoryLinkManagement->assignProductToCategories($key, $value);
					}
				}
			}
		}
		$logger->info('Provu Category Management Finished.');
	}
	
	public function removeProducts() {
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/provu.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		$collectionFactory = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		$productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
		$productCollection = $collectionFactory->create();
		$productCollection->addAttributeToSelect('*');
		foreach ($productCollection as $product) {
			try {
				$sku = $product->getSku();
				$product = $productRepository->get($sku);
				$productDate = substr($product->getData('provu_updated_time'), 0, 10);
				$curDate = date("Y-m-d");
				if ($productDate == $curDate) {
					//We don't need anything for this case
				} else {
					$productRepository->delete($product);
					$logger->info($sku.": Removed.");
				}
			} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
				
			}
		}
	}
	
	private function setAttributeForProduct($product, $attribute_code, $value, $objectManager)
	{
		$attributeOptionManagement = $objectManager->create('\Magento\Eav\Api\AttributeOptionManagementInterface');
		try {
			$attribute = $objectManager->create('\Magento\Eav\Model\AttributeRepository')->get('catalog_product', $attribute_code);
			$attribute_id = $attribute->getAttributeId();

			if($attribute->getData('frontend_input')=='select'){
					$options = $attributeOptionManagement->getItems('catalog_product', $attribute_id);
					$optionValue = 0;

					foreach($options as $option) {
							$compval = strtolower($value);
							$compval = str_replace('-','',$compval);
							$compval = str_replace(' ','',$compval);
							$optVal = strtolower($option->getLabel());
							$optVal = str_replace('-','',$optVal);
							$optVal = str_replace(' ','',$optVal);

							if ($optVal == $compval) {
									$optionValue = $option->getValue();
									break;
							}
					}



					if($optionValue == 0){
							$newOption = $objectManager->create('\Magento\Eav\Model\Entity\Attribute\Option');
							$attributeOptionLabel = $objectManager->create('\Magento\Eav\Api\Data\AttributeOptionLabelInterface');

							$attributeOptionLabel->setStoreId(0);
							$attributeOptionLabel->setLabel($value);
							$newOption->setLabel($value);
							$newOption->setStoreLabels([$attributeOptionLabel]);
							$newOption->setSortOrder(0);
							$newOption->setIsDefault(false);
							$attributeOptionManagement->add(\Magento\Catalog\Model\Product::ENTITY, $attribute_id, $newOption);
							$options_new = $attributeOptionManagement->getItems('catalog_product', $attribute_id);

							foreach($options_new as $option_new){
									if($option_new->getLabel() == $value){
											$optionValue = $option_new->getValue();
											break;
									}
							}
					}
					$product->setData($attribute_code, $optionValue);
			}else{
					$product->setData($attribute_code, $value);
			}
		}catch(\Magento\Framework\Exception\NoSuchEntityException $e) {
				//  attribute is not exists
			echo("Attribute not exist! : ". $attribute_code. "\n");
		}
	}
	
	public function scrape_between($data, $start, $end){
        $data = stristr($data, $start); // Stripping all data from before $start
        $data = substr($data, strlen($start));  // Stripping $start
        $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
        $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
        return $data;   // Returning the scraped data from the function
    }

}
