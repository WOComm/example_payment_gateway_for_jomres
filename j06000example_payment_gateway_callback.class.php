<?php
/**
 * Core file.
 *
 * @author Vince Wooll <sales@jomres.net>
 *
 *  @version Jomres 10.2.2
 *
 * @copyright	2005-2022 Vince Wooll
 * Jomres (tm) PHP, CSS & Javascript files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly
 **/

// ################################################################
defined('_JOMRES_INITCHECK') or die('');
// ################################################################
	
	/**
	 * @package Jomres\Core\Minicomponents
	 *
	 * 
	 */

class j06000bitpay_callback
{	
	/**
	 *
	 * Constructor
	 * 
	 * Main functionality of the Minicomponent 
	 *
	 * 
	 * 
	 */
	 
	public function __construct()
	{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance('mcHandler');
		if ($MiniComponents->template_touch) {
			$this->template_touchable = false;

			return;
		}
		$plugin="example_payment_gateway";

		// This holds the booking's details. In Jomres the
		$tmpBookingHandler =jomres_getSingleton('jomres_temp_booking_handler');

		$ePointFilepath = get_showtime('ePointFilepath');

		$settingArray = [];
		$query		= "SELECT setting,value FROM #__jomres_pluginsettings WHERE prid = ".$tmpBookingHandler->tmpbooking['property_uid']." AND plugin = '".$plugin."' ";
		$settingsList = doSelectSql( $query );
		if ( count ($settingsList) > 0)  {
			foreach ( $settingsList as $set ) {
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
			$resourceUrl = 'https://test.test.com/invoices';
		} else {
			$resourceUrl = 'https://www.test.com/invoices';
		}

		// Here we will call the gateway back using the invoice id that was stored at the end of the 00605 script to confirm successful payment
		$curlCli = curl_init($resourceUrl . '/' . $tmpBookingHandler->tmpbooking['example_gateway_invoice_id']. '?token=' . $api_token);

		curl_setopt($curlCli, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curlCli, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlCli, CURLOPT_HTTPHEADER, [
			'X-Accept-Version: 2.0.0',
			'Content-Type: application/json'
		]);

		$result = curl_exec($curlCli);
		$resultData = json_decode($result, TRUE);
		curl_close($curlCli);

		// If the gateway service confirms that the deposit has been paid then we will go ahead and insert the booking. This is your end goal.
		if ( $resultData['data']['status'] == 'paid' || $resultData['data']['status'] == 'confirmed' || $resultData['data']['status'] == 'complete') {
			insertInternetBooking(get_showtime('jomressession'),true,true);
		} else {
			property_header($tmpBookingHandler->tmpbooking['property_uid']);

			$booking_form_url = get_booking_url($tmpBookingHandler->tmpbooking['property_uid']);

			$pageoutput	= [];
			$output 		= [];

			// If available you could provide more information as to why the payment failed
			$output['EXAMPLE_GATEWAY_PAYMENT_FAILED']=jr_gettext('EXAMPLE_GATEWAY_PAYMENT_FAILED', 'EXAMPLE_GATEWAY_PAYMENT_FAILED', false);
			$output['EXAMPLE_GATEWAY_PAYMENT_FAILED_BLURB']=jr_gettext('EXAMPLE_GATEWAY_PAYMENT_FAILED_BLURB', 'EXAMPLE_GATEWAY_PAYMENT_FAILED_BLURB', false);
			$output['EXAMPLE_GATEWAY_PAYMENT_FAILED_BUTTON']=jr_gettext('EXAMPLE_GATEWAY_PAYMENT_FAILED_BUTTON', 'EXAMPLE_GATEWAY_PAYMENT_FAILED_BUTTON', false);

			$output['BOOKING_FORM_URL'] =  $booking_form_url ;

			$pageoutput[]=$output;
			$tmpl = new patTemplate();

			$tmpl->setRoot( $ePointFilepath.'templates'.JRDS.find_plugin_template_directory() );
			$tmpl->readTemplatesFromInput( 'payment_failed.html');
			$tmpl->addRows( 'pageoutput',$pageoutput);
			$tmpl->displayParsedTemplate();

		}

	}


	public function getRetVals()
	{
		return null;
	}
}
