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
	var $strPaypalPostback;
	var $intPaypalPostbackPort;
	
	// frontend stuff
	var $strSuccessPage;
	var $strCancelPage;
	var $strIPNPage;
	
	function __construct(){

		$booSandbox = PAYPAL_SANDBOX;
		
		if ($booSandbox == true){
			$this->strPaypalAccount = PAYPAL_SANDBOX_ACCOUNT;
			$this->strPaypalUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			$this->strPaypalPostback = 'ssl://www.sandbox.paypal.com';
		} else {
			$this->strPaypalAccount = PAYPAL_ACCOUNT;
			$this->strPaypalUrl = 'https://www.paypal.com/cgi-bin/webscr';
			$this->strPaypalPostback = 'ssl://www.paypal.com';
		}
		
		$this->intPaypalPostbackPort = '443';
		
		$this->strSuccessPage = PAYPAL_SUCCESS;
		$this->strCancelPage = PAYPAL_CANCEL;
		$this->strIPNPage = PAYPAL_IPN;

		// set default form fields
		$this->add('business',$this->strPaypalAccount);
		$this->add('no_note','1'); // display comment
		$this->add('no_shipping','1'); // display shipping address
		$this->add('edit_quantity',''); // -> 'no'
		$this->add('tax','');		
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
				<center><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="333333">PayPal Bezahlung wird eingeleitet ...</font></center>
				</form>
			</body>   
		</html>			
		<?php
	}

	function checkAndvalidateIPN(){
		
		$debug = true;
	
		if ($debug) $f = fopen($this->strLogfile,'a+');
		
		if ($debug) fwrite($f,'PAYPAL IPN'."\n");
		if ($debug) fwrite($f,'POST: '.print_r($_POST,true)."\n");
	
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
	
		// post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ($this->strPaypalPostback, $this->intPaypalPostbackPort, $errno, $errstr, 30);
		
		$pstatus = $_POST['payment_status'];
			
		if (!$fp) {
			if ($debug) fwrite($f,'paypal connection fsockopen failed'."\n");
			return false;
		} else {
			fputs ($fp, $header . $req);
			// read the body data 
			$res = '';
			$headerdone = false;
			while (!feof($fp)) {
				$line = fgets ($fp, 1024);
				if (strcmp($line, "\r\n") == 0) {
					$headerdone = true; // read the header
				} else if ($headerdone) {
					$res .= $line; // header has been read. now read the contents
				}
			}
			fclose ($fp);	
			// parse the data
			$lines = explode("\n", $res);
			if (strcmp ($lines[0], "VERIFIED") == 0) {
				if ($debug) fwrite($f,'paypal request verified'."\n");
					if ($pstatus == 'Completed'){
						if ($debug) fwrite($f,'paypal payment completed'."\n");
						
						// process payment
						$objPayment = new GSALES2_OBJECT_PAYMENT();
							$objPayment->setPaymentProvider('paypal');
							$objPayment->setAmount($_POST['mc_gross']);
							$objPayment->setInvoiceId($_POST['custom']);
							$objPayment->setTransactionId($_POST['txn_id']);
						if ($debug) fwrite($f,'Payment Object: '.print_r($objPayment,true)."\n");
						
						// set invoice to paid
						return $objPayment->checkPaidAmountAndSetInvoiceAsPaid();
					}
			} else {
				return false;
			}
		}
		
	}

}
