## Beckn Magento 2 Extension
Beckn Magento 2 Extension. I contain 5 module to connect over Beckn network.

### Extension List
1. Beckn_Core
2. Beckn_Search
3. Beckn_Select
4. Beckn_Checkout
5. Beckn_Razorpay

### 1. Beckn_Core
This extension contains main configuration and all core function.
Also, this extension contains almost main functions and receiving the data and sending it back to the 3rd party URL and making the body response of APIs.

### 2. Beckn_Search
This extension contains following API 
* /search
All the search logic and sending search data to Bap_Uri is added to this extension.
Also, this extension is depends on Beckn_Core extension because it use the main function from this module.

### 3. Beckn_Select
This extension contains following API 
* /select
This extension is managing user cart. Added item into cart, updating cart, deleting items from cart.

### 4. Beckn_Checkout
This extension contains following API 

* /init
* /confirm
* /status
* /support
* /get_cancellation_reasons
* /cancel

This extension is managing all the order related stuff with order status and order support.

### 5. Beckn_Razorpay
This extension is a basically a patch for Razorpay_Magento to work both website and Beckn network.
This extension doesn't contain any API.

