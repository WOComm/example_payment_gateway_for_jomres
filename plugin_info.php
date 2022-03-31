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
defined( '_JOMRES_INITCHECK' ) or die( '' );
// ################################################################

class plugin_info_example_payment_gateway
	{
	function __construct()
		{
		$this->data=array(
			"name"=>"example_payment_gateway",
			"category"=>"Payment handling",
			"marketing"=>"An example payment gateway plugin",
			"version"=>"1.0",
			"description"=> "An example plugin to show how to build a payment gateway for Jomres",
			"author"=>"",
			"authoremail"=>"",
            "lastupdate"=>"2022/03/30",
            "min_jomres_ver"=>"10.2.2",
			"manual_link"=>'',
			'change_log'=>'',
			'highlight'=>'',
			'image'=>'',
			'demo_url'=>''
			);
		}
	}
