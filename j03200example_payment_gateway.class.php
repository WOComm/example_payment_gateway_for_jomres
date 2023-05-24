<?php
/**
 * Connect plugin
 *
 * @author Vince Wooll <sales@jomres.net>
 *
 * @version Jomres 9.22.0
 *
 * @copyright	2005-2020 Vince Wooll
 *
 * This file is NOT open source and you are not allowed to distribute it, for any reason.
 **/

// ################################################################
defined( '_JOMRES_INITCHECK' ) or die( 'Direct Access to this file is not allowed.' );
// ################################################################

class j03200bitpay
{
	function __construct($componentArgs)
	{
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch) {
			$this->template_touchable=false; return;
			}

		// The purpose here is to log successful payment in the payment gateway against the Jomres invoice so that the invoice shows the successful payment

		// Besides the plugin name, it's highly unlikely that you will need to edit this script much. The only exception is the line where it says $tmpBookingHandler->tmpbooking['example_gateway_invoice_id'] because your gateway will have its own transaction/invoice id that you will want to record

		$plugin="example_payment_gateway";

		$tmpBookingHandler =jomres_getSingleton('jomres_temp_booking_handler');

		$new_booking_user_id = get_showtime("new_booking_user_id");
		$thisJRUser = jomres_singleton_abstract::getInstance('jr_user');
		$thisJRUser->init_user($new_booking_user_id);  // The user could have just been created in the 03020 script therefore the user's object may not have been setup (singleton) therefore we'll force a reload here

		// Let's get the Jomres invoice
		jr_import('jrportal_invoice');
		$invoice = new jrportal_invoice();
		$invoice->id = (int) get_showtime('inserted_booking_invoice_id');
		$invoice->getInvoice();

		// We'll add a new line item which is a negative figure, to allow the invoice class to correctly calculate the required balance
		$line_items = array('tax_code_id' => 0,
			'name' => jr_gettext('_JOMRES_MR_AUDIT_ENTEREDDEPOSIT', '_JOMRES_MR_AUDIT_ENTEREDDEPOSIT', false, false),
			'description' => $plugin,
			'init_price' => 0 - $componentArgs["tempBookingDataList"][0]->deposit_required,
			'init_qty' => 1,
			'init_discount' => 0,
			'is_payment' => 1,
			'payment_method' => $plugin,
			'transaction_id' => $tmpBookingHandler->tmpbooking['example_gateway_invoice_id']
		);
		$invoice->add_line_item($line_items);

		// Save the updated invoice
		$invoice->commitUpdateInvoice();


	}

	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}
