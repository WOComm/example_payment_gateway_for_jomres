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


class j00005example_payment_gateway
	{
	function __construct($componentArgs)
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return 
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=false; return;
			}

		// This should be called at the beginning of the minicomponent constructor. It gives the path to this script. If it's included later in the script it's possible that other scripts will have changed this value so you should set it right at the beginning.
		$ePointFilepath=get_showtime('ePointFilepath');
		// This gives the relative path to the plugin's directory, which you could use for including javascript for example.
		$eLiveSite = get_showtime('eLiveSite');

		// If you want to include some javascript or css you would do the following. If not, then delete these lines.
		jomres_cmsspecific_addheaddata( "css", $eLiveSite.'/css/', "some-css-file.css" );
		jomres_cmsspecific_addheaddata( "javascript", $eLiveSite.'/javascript/', "some-javascript-file.js" );

		// Get the plugin's language file for the current language, if it exists, if not try to fall back to the English version of the language file.
		// This gateway example does not provide examples of other language files however you can see language files for Jomres Core here https://github.com/WoollyinWalesIT/jomres/tree/master/language so any new language files you add would need to be named in the same way, so a Spanish language file in the language subdirectory would need to be called es-ES.php
		if (file_exists($ePointFilepath.'language'.JRDS.get_showtime('lang').'.php'))
			require_once($ePointFilepath.'language'.JRDS.get_showtime('lang').'.php');
		else
			{
			if (file_exists($ePointFilepath.'language'.JRDS.'en-GB.php'))
				require_once($ePointFilepath.'language'.JRDS.'en-GB.php');
			}

		// If you want to autoload an SDK's files, do that here
		require_once($ePointFilepath.'vendor'.JRDS.'autoload.php');
		}

		
		
	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}
