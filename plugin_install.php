<?php
/**
* Jomres CMS Agnostic Plugin
* @author Woollyinwales IT <sales@jomres.net>
* @version Jomres 9 
* @package Jomres
* @copyright	2005-2015 Woollyinwales IT
* Jomres (tm) PHP files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project.
**/

if (!defined('JOMRES_INSTALLER')) exit;

// This script is optional. It is run by the Jomres plugin manager when it installs plugins and allows the plugin to create new tables on installation. If you don't need it, just delete it from your own gateway.
$query = "CREATE TABLE IF NOT EXISTS `#__jomres_example_payment_gateway_table_blahblahblah` (
	`id` int(10) NOT NULL auto_increment,
	PRIMARY KEY  (id)
)";
doInsertSql($query,"");


