## Beckn Core Magento 2 Extension
Beckn Core extension helps to connect over Beckn network.

#### 1 - Installation
 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/Beckn/Core
 * Copy the content from unzip folder
 
 #### 2 - Installation of extension.
    * php bin/magento module:enable Beckn_Core
    * php bin/magento setup:upgrade
    * php bin/magento setup:di:compile
    * php bin/magento setup:static-content:deploy -f
    * php bin/magento indexer:reindex
    * php bin/magento cache:flush
    
 #### 3 - Configure the extension by going 
    * Stores >> Configuration >> Beckn 