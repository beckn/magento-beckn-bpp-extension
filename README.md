# Project Update

|Projects/Products|Version|Versioning Scheme|Manager|Responsible|Accountable|Consulted|Informed|Last update|Last updated date|Next upcoming release date
|-----------------|-------|-----------------|-------|-----------|-----------|---------|--------|-----------|-----------------|--------------------------
|Retail Reference BPP|0.1.1|[Version Management](https://docs.google.com/document/d/1HjXV4W2STirMUa2_L8bGWB0ORn9SeYRvJSyUPbntbXY/edit#heading=h.b06d3jp4draa)|Nirmal|Nirmal|Nirmal|Open Community, Indglobal|General Public|End-to-end functionality testing in progress|14 Feb 2022|18 Feb 2022

## Work queue
|Release|Start Date|End Date|Status|Delay in Days|REQ|Documentation|Code|Logs
|-------|----------|--------|------|-------------|---|-------------|----|----
|0.1.1 |25 Jan 2022|18 Feb 2022|In progress| |[Task List](https://github.com/beckn/magento-beckn-bpp-extension/issues?q=milestone%3Av0.1.1+)|[Wiki](https://github.com/beckn/beckn-in-a-box/wiki)|[Codebase](https://github.com/beckn/magento-beckn-bpp-extension)|[Logs](https://github.com/beckn/magento-beckn-bpp-extension/commits/main)


## Beckn Magento 2 Extension
Beckn Magento 2 Extension. I contain 5 module to connect over Beckn network.

### Extension List
1. Beckn_Core
2. Beckn_Search
3. Beckn_Select
4. Beckn_Checkout
5. Beckn_CancelOrder
6. Beckn_Support
7. Beckn_Razorpay

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

This extension is managing all the order related stuff with order place and order status.

### 5. Beckn_CancelOrder
This extension contains following API

* /get_cancellation_reasons
* /cancel

This extension is managing all the order cancel stuff.

### 6. Beckn_Support
This extension contains following API

* /support

This extension is managing all the support related API.

### 7. Beckn_Razorpay
This extension is a basically a patch for Razorpay_Magento to work both website and Beckn network.
This extension doesn't contain any API.

