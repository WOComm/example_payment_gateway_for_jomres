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

class j00510example_payment_gateway {
	function __construct()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return 
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=false; return;
			}
		// This should be called at the beginning of the minicomponent constructor. It gives the path to this script. If it's included later in the script it's possible that other scripts will have changed this value, set it right at the beginning.
		$ePointFilepath=get_showtime('ePointFilepath');
		// This gives the relative path to the plugin's directory, which you could use for including javascript for example.
		$eLiveSite = get_showtime('eLiveSite');

		$plugin="example_payment_gateway";

		// Use this to find the property that the manager is currently editing/viewing
		$defaultProperty=getDefaultProperty();

		// Set up some default values so that PHP doesn't throw it's teddy out of it's pram
		$settingArray=array();
		$settingArray['active']="0";
		$settingArray['api_token']="";
		$settingArray['test_mode']="1";
		$settingArray['test_api_token']="";

		// Used for bulding Yes/No button sets
		$yesno = array();
		$yesno[] = jomresHTML::makeOption( '0', jr_gettext('_JOMRES_COM_MR_NO','_JOMRES_COM_MR_NO',FALSE) );
		$yesno[] = jomresHTML::makeOption( '1', jr_gettext('_JOMRES_COM_MR_YES','_JOMRES_COM_MR_YES',FALSE) );


		// Find any existing settings
		$query="SELECT setting,value FROM #__jomres_pluginsettings WHERE prid = '".(int)$defaultProperty."' AND plugin = '$plugin' ";
		$settingsList=doSelectSql($query);
		foreach ($settingsList as $set) {
			$settingArray[$set->setting]=$set->value;
		}

		// The values saved to the $output array are then handed off to the templating system to build the form. The array's key are captitalised to make it easy to see visually which items are intended to be used in a template, but they don't need to be capitalised here. They do, however, have to be capitalised in the template file (eg edit_gateway.html) itself.

		$output = [];

		// Setup a logo image
		$output['LOGO'] = $eLiveSite."j00510".$plugin.".png";

		$output['JR_GATEWAY_CONFIG_ACTIVE']	= jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_ACTIVE'.$plugin,"Active");

		// Populate the form fields
		$output['ACTIVE']					= jomresHTML::selectList( $yesno, 'active', 'class="inputbox form-control" size="1"', 'value', 'text', $settingArray['active'] );
		$output['API_TOKEN']				= $settingArray['api_token'];
		$output['TEST_API_TOKEN']			= $settingArray['test_api_token'];
		$output['TEST_MODE']				= jomresHTML::selectList( $yesno, 'test_mode', 'class="inputbox form-control" size="1"', 'value', 'text', $settingArray['test_mode']);

		// Taken from the language file. The third argument of jr_gettext determines whether or not a manager can edit the string. Given that these are instructions on use, we don't want the manager to edit them so this arguement should always be set to False.
		$output['EXAMPLE_GATEWAY_TITLE']				= jr_gettext('EXAMPLE_GATEWAY_TITLE','EXAMPLE_GATEWAY_TITLE',false);
		$output['EXAMPLE_GATEWAY_MARKETING']			= jr_gettext('EXAMPLE_GATEWAY_MARKETING','EXAMPLE_GATEWAY_MARKETING',false);
		$output['EXAMPLE_GATEWAY_API_TOKEN_TITLE']		= jr_gettext('EXAMPLE_GATEWAY_API_TOKEN_TITLE','EXAMPLE_GATEWAY_API_TOKEN_TITLE',false);
		$output['EXAMPLE_GATEWAY_TEST_API_TOKEN_TITLE']	= jr_gettext('EXAMPLE_GATEWAY_TEST_API_TOKEN_TITLE','EXAMPLE_GATEWAY_TEST_API_TOKEN_TITLE',false);
		$output['EXAMPLE_GATEWAY_API_TOKEN_DESC']		= jr_gettext('EXAMPLE_GATEWAY_API_TOKEN_DESC','EXAMPLE_GATEWAY_API_TOKEN_DESC',false);
		$output['EXAMPLE_GATEWAY_API_TEST_MODE']		= jr_gettext('EXAMPLE_GATEWAY_API_TEST_MODE','EXAMPLE_GATEWAY_API_TEST_MODE',false);
		$output['EXAMPLE_GATEWAY_API_TEST_MODE_DESC']	= jr_gettext('EXAMPLE_GATEWAY_API_TEST_MODE_DESC','EXAMPLE_GATEWAY_API_TEST_MODE_DESC',false);

		$output['COMMON_SUBMIT']						= jr_gettext('COMMON_SUBMIT','COMMON_SUBMIT');
		$output['GATEWAY']								= $plugin;

		// Call the edit_gateway.html template file to output the form for configuring the gateway settings.
		$pageoutput[]=$output;
		$tmpl = new patTemplate();
		$tmpl->setRoot( $ePointFilepath.'templates'.JRDS.find_plugin_template_directory() );
		$tmpl->readTemplatesFromInput( 'edit_gateway.html' );
		$tmpl->addRows( 'edit_gateway', $pageoutput );
		$tmpl->displayParsedTemplate();
	}

	/**
	#
	 * Must be included in every mini-component
	#
	 * Returns any settings that the mini-component wants to send back to the calling script. In addition to being returned to the calling script they are put into an array in the mcHandler object as eg. $mcHandler->miniComponentData[$ePoint][$eName]
	#
	 */
	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}
