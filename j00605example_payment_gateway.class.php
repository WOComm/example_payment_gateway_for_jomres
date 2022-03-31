<?php
/**
* Jomres CMS Agnostic Plugin
* @author Woollyinwales IT <sales@jomres.net>
* @version Jomres 9 
* @package Jomres
* @copyright	2005-2015 Woollyinwales IT
* Jomres (tm) PHP files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project.
**/

// ################################################################
defined( '_JOMRES_INITCHECK' ) or die( 'Direct Access to this file is not allowed.' );
// ################################################################


class j00605example_payment_gateway{
	function __construct($componentArgs)
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=false; return;
			}
		$plugin="example_payment_gateway";

		// This should be called at the beginning of the minicomponent constructor. It gives the path to this script. If it's included later in the script it's possible that other scripts will have changed this value, set it right at the beginning.
		$ePointFilepath=get_showtime('ePointFilepath');
		// This gives the relative path to the plugin's directory, which you could use for including javascript for example.
		$eLiveSite = get_showtime('eLiveSite');

		$tmpBookingHandler =jomres_getSingleton('jomres_temp_booking_handler');
		$siteConfig = jomres_getSingleton('jomres_config_site_singleton');
		$jrConfig=$siteConfig->get();
		$mrConfig = getPropertySpecificSettings($tmpBookingHandler->tmpbooking['property_uid']);

		$settingArray = [];
		$query		= "SELECT setting,value FROM #__jomres_pluginsettings WHERE prid = ".$tmpBookingHandler->tmpbooking['property_uid']." AND plugin = '".$plugin."' ";
		$settingsList = doSelectSql( $query );
		if ( count ($settingsList) > 0)
			{
			foreach ( $settingsList as $set )
				{
				$settingArray[ $set->setting ] = trim($set->value);
				}
			}

		$test_mode = false;
		$api_token = trim($settingArray[ 'api_token' ]); // Copy pasta from browsers can introduce spaces
		if ( $settingArray[ 'test_mode' ] == '1' ) {
			$test_mode = true;
			$api_token = trim($settingArray[ 'test_api_token' ]);
		}

		// Try to fall back to test mode
		if ( $settingArray[ 'api_token' ] == '' ) {
			$test_mode = true;
			$api_token = trim($settingArray[ 'test_api_token' ]);
		}

		if ( $test_mode && $settingArray[ 'test_api_token' ] =='' ) {
			$test_mode = true;
		}

		if ($test_mode) {
			$SDK = new "Do something here to setup the SDK, apply API keys, in test mode"; // This line is intentionally faulty
		} else {
			$SDK = new "Do something here to setup the SDK, apply API keys, in production mode"); // This line is intentionally faulty
		}

		// Do something here to either generate an invoice on the gateway service, or build the credit card form locally and then pass the details to the gateway service. It's impossible to provide detailed examples of what to do exactly at this point because gateway services vary wildly in the different implementations that they offer. Some require you to build a card form locally to capture card details, some require you to use their checkout page. In this example we are just going to redirect the payer to the gateway service's checkout page, after using the hypothetical SDK of the gateway service to generate an invoice number (see the Jomres Bitpay plugin as an example).

		// $tmpBookingHandler contains the information about the booking. It's stored in the sessions table in between page views and typically has a lifetime of about 24 hours.

		// In this example we have provided some values offered by $tmpBookingHandler for you to use

		$currency = $mrConfig[ 'property_currencycode' ];
		if ( $jrConfig[ 'useGlobalCurrency' ] == "1" )  { // If admin has determined that the site will use a global currency code, we will set that here
			$currency = $jrConfig[ 'globalCurrencyCode' ];
		}

		$deposit				= (float)$tmpBookingHandler->tmpbooking['deposit_required'];
		$contract_total			= (float)$tmpBookingHandler->tmpbooking['contract_total']; // Included for completeness, however you should not use it. Instead use deposit_required as that's the deposit as calculated based on the property's settings


		$booking_number			= $tmpBookingHandler->tmpbooking['booking_number'];
		$callback_url			= JOMRES_SITEPAGE_URL_NOSEF.'&task=bitpay_callback';
		$invoice_description	= jr_gettext('_JOMRES_AJAXFORM_BILLING_ROOM_TOTAL' , '_JOMRES_AJAXFORM_BILLING_ROOM_TOTAL' , false );

		$guest_name				= $tmpBookingHandler->_tmpguest['firstname']." ".$tmpBookingHandler->tmpguest['surname'];
		$guest_email			= $tmpBookingHandler->tmpguest["email"];
		$guest_address_1		= $tmpBookingHandler->_tmpguest['house']." ".$tmpBookingHandler->_tmpguest['street'];
		$guest_country			= $tmpBookingHandler->_tmpguest['country'];
		$guest_locality			= $tmpBookingHandler->_tmpguest['town'];
		$guest_phone			= $tmpBookingHandler->_tmpguest['tel_mobile'];
		$guest_postcode			= $tmpBookingHandler->_tmpguest['postcode'];
		$guest_state			= $tmpBookingHandler->_tmpguest['region'];	// This will be the Jomres database index of the region, you can use find_region_name($tmpBookingHandler->_tmpguest[ 'region' ] to get the name of the region

		// An example of a gateway's invoice id (really this would be provided by the payment gateway):
		$example_gateway_invoice_id = 1000;

		// This value is used later by the callback script and the 03200 script therefore we will save the value to the temp booking handler object
		$tmpBookingHandler->tmpbooking['example_gateway_invoice_id'] = $example_gateway_invoice_id;
		$tmpBookingHandler->close_jomres_session();  // Make sure that the invoice id is saved

		// Redirect to the gateway service
		$url = 'https://test.com/checkout?invoice_id='.$example_gateway_invoice_id;

		jomresRedirect($url);

		}


	/**
	#
	 * Must be included in every mini-component
	#
	 * Returns any settings the the mini-component wants to send back to the calling script. In addition to being returned to the calling script they are put into an array in the mcHandler object as eg. $mcHandler->miniComponentData[$ePoint][$eName]
	#
	 */
	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}

