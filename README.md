## Introduction

This is an example payment gateway plugin you can use as a framework to build your own payment gateway for Jomres. It is not a working example of a payment gateway. 

It aims to be as concise as possible while still providing you with enough information to build a payment gateway. 

Can be used in conjunction with the [manual page here](https://www.jomres.net/manual/developers-guide-2/58-other-discussions/payment-gateways/281-gateway-aide-memoire).

See the individual file contents for more details.

Some notes:
- Gateway settings are usually specific to individual properties. The Jomres Stripe payment gateway is an exception to that rule, as is the Jomres Paypal backend configuration setting, but in general each property should have their own configuration settings such as API keys. Where-ever possible it's recommended that you create gateways that allow different properties to have different settings.
- When you rename these scripts you will need to rebuild the registry afterwards (Admin > Jomres > Tools > Rebuild registry) otherwise Jomres won't know that they exist and won't call them.
- If you do rename a script, remember also to rename its class name to the same name as the file.
- This example includes templates for Bootstrap 2, Bootstrap 3 & Bootstrap 5. This is to give you an idea of how to build the plugin if you intend to distribute your gateway to Jomres users. If you are building a gateway for your own use then some of them will be redundant. You can safely remove them.
- This gateway is licensed under the MIT license specifically so that developers can take the code, build their own payment gateways and if they want, to sell those gateways onwards.

### Installation

To test it on an installation of Jomres you have two ways you can install it : 
- Either zip up the contents of this directory and use the Jomres plugin manager's plugin installation tab to install the plugin. This is the best process to use if you want to create new tables (see the optional plugin_install.php).
- Manually move the entire contents into /jomres/remote_plugins so that you have the structure /jomres/remote_plugins/example_payment_gateway and then rebuild the registry to tell Jomres that the files exist. The plugin_install.php script will not be run so you will need to manually add tables if you need them.

### Gateway scripts

Trigger number explanation. These trigger numbers are presented in the order that the scripts are called, so setup first, then during the gateway's configuration, and then finally usage during payment. For more information on why these scripts are numbered this way, see [the manual page on minicomponents](https://www.jomres.net/manual/developers-guide-2/51-introduction/263-minicomponents).

You are not limited to the trigger numbers used here. You can use any Jomres trigger number you need in your gateway, so for example in theory you could create a 07310 [webhook watcher](https://www.jomres.net/manual/developers-guide-2/64-webhooks) script that looks for property_created webhooks and then automatically add gateway settings to that property, That's not recommended in a multi-vendor situation, but it's perfectly acceptable if all properties will use the same payment gateway settings.


#### *Always called*

**00005** This trigger point is used for setting up various files, for example including your gateway's SDK files if it has an SDK, including language files and anything else that needs to be done to initialise things. The 00005 script is always run, regardless of whether a Jomres plugin is called on a page (ie through a shortcode), if you are on a Jomres page, or if it's an ajax call.

#### *On the Property Configuration page*

**00509** Performs 2 tasks. It tells the Gateways tab in property configuration whether or not the gateway is active and provides the link for the popup that is used to configure the gateway. It also returns a setting called 'balance_payments_supported' which tells the list invoices script whether or not this gateway supports balance payments. An example of that is not included in this example gateway plugin, so this gateway would only support deposit payments.

**00510** Builds the edit gateway settings form which is shown when the user clicks on the Gateway name in the Property Configuration page.

#### *During the payment process of the deposit*

**03108** Tells the booking confirmation script that the gateway exists. If it returns null then the confirmation script knows that it should not offer this gateway. As a result you can use this script to ensure that API keys have been set and/or check that the value of the deposit meets the gateway service's minimum value, or any other checks that you want to do before deciding if this gateway should be offered to the guest during the payment steps.

**00605** This is the script that is called after the booking has been confirmed and it starts the payment process. Typically, it's used to initialise an SDK and generate the invoice on the gateway service. In this example the SDK allows us to define a return URL, so instead of using Jomres' 00610 script the guest making the payment will be returned to the 06000example_payment_gateway_callback script. It may be that you want to build your own payment form at this stage to capture credit card details and then hand them off to the gateway service. In that case instead of redirecting the payer to the gateway's checkout page you would build your form on this page. The Jomres Bitpay plugin uses the former method (redirecting to Bitpay's checkout page) whereas the Jomres Stripe payment gateway uses the latter (Build the form on the site).

**06000** This script is called when the payer is returned from the gateway service's website. It checks that the payment has been made by calling back the gateway service citing an invoice number generated when the 00605 script is run. 

**03200** This script is called after the booking has been inserted and is used to add a line item to the Jomres invoice that was created, to note that the deposit payment was paid successfully. This will then appear in the booking's invoice as a transaction.
