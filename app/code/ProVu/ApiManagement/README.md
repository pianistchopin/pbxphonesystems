# Mage2 Module ProVu ApiManagement

    ``provu/module-apimanagement``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Relation between Magento store and ProVu Api

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/ProVu`
 - Enable the module by running `php bin/magento module:enable ProVu_ApiManagement`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require provu/module-apimanagement`
 - enable the module by running `php bin/magento module:enable ProVu_ApiManagement`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - Runtime Period (products/options/runtime_period)


## Specifications

 - Cronjob
	- provu_apimanagement_catalogsync

 - Console Command
	- ImportCatalog


## Attributes



