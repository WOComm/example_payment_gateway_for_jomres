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

class j03108bitpay
	{
	function __construct ()
	{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return 
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=false; return;
			}

		$plugin="example_payment_gateway";

		$this->gatewayname	='';
		$this->filepath		='';

		$tmpBookingHandler =jomres_getSingleton('jomres_temp_booking_handler');

		if (isset($tmpBookingHandler->tmpbooking['property_uid']) && $tmpBookingHandler->tmpbooking['property_uid'] > 0 ) {

			$query		= "SELECT setting,value FROM #__jomres_pluginsettings WHERE prid = ".$tmpBookingHandler->tmpbooking['property_uid']." AND plugin = '".$plugin."'";
			$settingsList = doSelectSql( $query );
			if ( count ($settingsList) > 0)
			{
				foreach ( $settingsList as $set )
				{
					$settingArray[ $set->setting ] = trim($set->value);
				}
			}

			if(!isset($settingArray[ 'api_token' ])){
				$settingArray[ 'api_token' ] = '';
			}

			if(!isset($settingArray[ 'test_api_token' ])){
				$settingArray[ 'test_api_token' ] = '';
			}

			if ( trim($settingArray[ 'test_api_token' ]) == '' && trim($settingArray[ 'api_token' ]) == '')  {
				return false;
			}

			$this->filepath		= get_showtime('ePointFilepath');
			$this->gatewayname	= jr_gettext('EXAMPLE_GATEWAY_TITLE',"EXAMPLE_GATEWAY_TITLE",false,false);
		}
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
		return array('filepath'=>$this->filepath,'gatewayname'=>$this->gatewayname);
		}
	}
