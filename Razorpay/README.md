## Beckn Razorpay Magento 2 Extension
Beckn Razorpay extension is a patch for Razorpay_Magento to checkout with both website and Beckn network.
Before installing this extension you need to install Razorpay_Magento extension first.

#### 1 - Installation
 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/Beckn/Razorpay
 * Copy the content from unzip folder
 
 #### 2 - Installation of extension.
    * php bin/magento module:enable Beckn_Checkout
    * php bin/magento setup:upgrade
    * php bin/magento setup:di:compile
    * php bin/magento setup:static-content:deploy -f
    * php bin/magento indexer:reindex
    * php bin/magento cache:flush
 