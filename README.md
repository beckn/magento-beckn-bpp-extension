## Beckn Magento 2 Extension
Beckn Magento 2 Extension. I contain 3 module to connect over Beckn network.

### Extension List
1. Beckn_BPP
2. Beckn_Checkout
3. Beckn_Razorpay

### 1. Beckn_BPP
This extension contains main configuration and /search and /select API.
Also, this extension contains almost main functions and receiving the data and sending it back to the 3rd party URL and making the body response of APIs.

### 2. Beckn_Checkout
This extension contains following API 
* /init
* /confirm
* /status
* /cancel
* /get_cancellation_reasons
* /support

Also, this extension is depends on Beckn_Bpp extension because it use the main function from this module.

### 3. Beckn_Checkout
This extension is a basically a patch for Razorpay_Magento to work both website and Beckn network.
This extension doesn't contain any API.

