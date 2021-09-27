## Beckn Support Magento 2 Extension
Beckn Support extension helps to connect over Beckn network and get support API for beckn orders.
This extension is depends on Beckn_Core extension before install this extension please install Beckn_Core first.

#### 1 - Installation
 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/Beckn/Support
 * Copy the content from unzip folder
 
 #### 2 - Installation of extension.
    * php bin/magento module:enable Beckn_Support
    * php bin/magento setup:upgrade
    * php bin/magento setup:di:compile
    * php bin/magento setup:static-content:deploy -f
    * php bin/magento indexer:reindex
    * php bin/magento cache:flush
    
 #### 3 - Configure the extension by going 
    * Stores >> Configuration >> Beckn Protocol Configuration 