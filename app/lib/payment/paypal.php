<?php

/*
 * gsFrontend
 * Copyright (C) 2011 Gedankengut GbR Häuser & Sirin <support@gsales.de>
 * 
 * This file is part of gsFrontend.
 * 
 * gsFrontend is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * gsFrontend is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with gsFrontend. If not, see <http://www.gnu.org/licenses/>.
 */

class PAYPAL
{

	// class settings
	var $strLogfile;
	var $arrForm;
	var $booLogEvents=false;
	
	// paypal settings
	var $strPaypalAccount;
	var $strPaypalUrl;

	// frontend stuff
	var $strSuccessPage;
	var $strCancelPage;
	var $strIPNPage;

	function __construct(){

		$booSandbox = PAYPAL_SANDBOX;

		if ($booSandbox == true){
			$this->strPaypalAccount = PAYPAL_SANDBOX_ACCOUNT;
			$this->strPaypalUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		} else {
			$this->strPaypalAccount = PAYPAL_ACCOUNT;
			$this->strPaypalUrl = 'https://www.paypal.com/cgi-bin/webscr';
		}

		$this->strSuccessPage = PAYPAL_SUCCESS;
		$this->strCancelPage = PAYPAL_CANCEL;
		$this->strIPNPage = PAYPAL_IPN;

		// set default form fields
		$this->add('business',$this->strPaypalAccount);
		$this->add('no_note','1'); // display comment
		$this->add('no_shipping','1'); // display shipping address
		$this->add('edit_quantity',''); // -> 'no'
		$this->add('tax','0');
		$this->add('return',$this->strSuccessPage);
		$this->add('cancel_return',$this->strCancelPage);
		$this->add('notify_url',$this->strIPNPage);
		$this->add('rm',2); // return method post
		$this->add('currency_code',PAYPAL_CURRENCY);
		$this->add('lc','DE');
		$this->add('cmd','_xclick');

		if (true == PAYMENT_LOG){
			$this->enableLogging();
			$this->setLogFile(PAYMENT_LOGFILE);
		}

	}

	function setLogFile($strAbsolutPathToFile){
		$this->strLogfile = $strAbsolutPathToFile;
	}

	function enableLogging(){
		$this->booLogEvents = true;
	}

	function add($strKey,$strValue){
		$this->arrForm[$strKey]=$strValue;
	}

	function startProcess(){
		?>
		<html>
			<head><title>PayPal Bezahlung wird eingeleitet ...</title></head>
			<body onLoad="document.paypal_form.submit();">
				<form method="post" name="paypal_form" action="<?php echo $this->strPaypalUrl; ?>">
				<?php foreach ((array)$this->arrForm as $key => $value) echo '<input type="hidden" name="'.$key.'" value="'.$value.'">'; ?>
					<p style="text-align: center; font-family: Verdana, Arial, Helvetica, sans-serif; color: #333333">PayPal Bezahlung wird eingeleitet ...</p>
				</form>
			</body>
		</html>
		<?php
	}

	function checkAndvalidateIPN(){

		if ($this->booLogEvents){
			ini_set('log_errors', true);
			ini_set('error_log', $this->strLogfile);
		}

		include('PHP-PayPal-IPN/ipnlistener.php');

		$listener = new IpnListener();
		$listener->use_sandbox = PAYPAL_SANDBOX;
		$listener->use_ssl = true;

		$listener->use_curl = false;
		if (function_exists('curl_init')) $listener->use_curl = true;

		try {
			$listener->requirePostMethod();
			$verified = $listener->processIpn();
		} catch (Exception $e) {
			error_log($e->getMessage());
			exit(0);
		}

		if ($this->booLogEvents) error_log($listener->getTextReport());

		if ($verified) {

			if ($_POST['payment_status'] != 'Completed'){
				if ($this->booLogEvents) error_log('FAIL - payment_status is not Completed');
				return false;
			}

			if ($_POST['receiver_email'] != $this->strPaypalAccount){
				if ($this->booLogEvents) error_log('FAIL - receiver_email is: '.$_POST['receiver_email'].' expected: '.$this->strPaypalAccount);
				return false;
			}

			if ($_POST['mc_currency'] != PAYPAL_CURRENCY){
				if ($this->booLogEvents) error_log('FAIL - currency is: '.$_POST['mc_currency'].' expected: '.PAYPAL_CURRENCY);
				return false;
			}

			// process payment
			$objPayment = new GSALES2_OBJECT_PAYMENT();
			$objPayment->setPaymentProvider('paypal');
			$objPayment->setAmount($_POST['mc_gross']);
			$objPayment->setInvoiceId($_POST['custom']);
			$objPayment->setTransactionId($_POST['txn_id']);
			if ($this->booLogEvents) error_log('Payment object:'.print_r($objPayment,true));

			// set invoice to paid
			return $objPayment->checkPaidAmountAndSetInvoiceAsPaid();

		} else {
			if ($this->booLogEvents) error_log('!!! Invalid IPN !!! ');
		}

	}

}
