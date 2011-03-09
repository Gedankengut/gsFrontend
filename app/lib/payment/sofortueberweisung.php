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

class SOFORTUEBERWEISUNG
{

	// class settings
	var $strLogfile;
	var $arrForm;
	var $booLogEvents=false;
	
	// sofortu settings
	var $strProjectPass;
	var $intUserId;
	var $intProjectId;
	var $strCurrency = 'EUR';
	
	// payment settings
	var $intInvoiceId;
	var $floatAmount;
	var $strReason1;
	var $strReason2;
	
	function __construct(){

		if (true == PAYMENT_LOG){
			$this->enableLogging();
			$this->setLogFile(PAYMENT_LOGFILE);
		}
		
		$this->intUserId = SOFORTU_USERID;
		$this->intProjectId = SOFORTU_PROJECTID;
		$this->strProjectPass = SOFORTU_PROJECTPASS;
		
	}
	
	function setInvoiceId($intInvoiceId){
		$this->intInvoiceId = $intInvoiceId;
	}
	
	function setAmount($floatAmount){
		$this->floatAmount = $floatAmount;
	}
	
	function setReason1($strReason1){
		$this->strReason1 = $strReason1;
	}
	
	function setReason2($strReason2){
		$this->strReason2 = $strReason2;
	}
	
	function setLogFile($strAbsolutPathToFile){
		$this->strLogfile = $strAbsolutPathToFile;
	}
	
	function enableLogging(){
		$this->booLogEvents = true;
	}
	
	function startProcess(){
		?> 
		<html>
			<head><title>sofortüberweisung wird eingeleitet ...</title></head>
			<body onLoad="document.sofort_form.submit();">
			<?php
			$strHash =  $this->getInputHash($this->intUserId, $this->intProjectId, $this->strReason1, $this->strReason2, $this->floatAmount, $this->intInvoiceId);
			echo '<form method="post" name="sofort_form" action="https://www.sofortueberweisung.de/payment/start">';
				echo '<input type="hidden" name="user_id" value="'.$this->intUserId.'" />';
				echo '<input type="hidden" name="project_id" value="'.$this->intProjectId.'" />';
				echo '<input type="hidden" name="reason_1" value="'.$this->strReason1.'" />';
				echo '<input type="hidden" name="reason_2" value="'.$this->strReason2.'" />';
				echo '<input name="currency_id" type="hidden" value="'.$this->strCurrency.'"/>';
				echo '<input type="hidden" name="amount" value="'.$this->floatAmount.'" />';
				echo '<input type="hidden" name="user_variable_0" value="'.$this->intInvoiceId.'" />';
				echo '<input name="hash" type="hidden" value="'.$strHash.'"/>';
			echo '</form>';
			?>
			<center><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="333333">sofortüberweisung wird eingeleitet ...</font></center>
			</body>   
		</html>			
		<?php
	}

	function getInputHash($intUserId, $intProjectId, $strReason1, $strReason2, $strAmount, $strUserVar0 ){
		$strProjectpass=$this->strProjectPass;
		$strToHash = "$intUserId|$intProjectId|||||$strAmount|EUR|$strReason1|$strReason2|$strUserVar0||||||$strProjectpass";
		return sha1($strToHash);		
	}

	function checkAndvalidatePN(){
		
		$debug = $this->booLogEvents;
		
		if ($debug) $f = fopen($this->strLogfile,'a+');
		if ($debug) fwrite($f,'SOFORTU PAYMENT NOTIFICATION'."\n");
		if ($debug) fwrite($f,'POST'.print_r($_POST,true));
		if ($debug) fwrite($f,'GET'.print_r($_GET,true));
		
		// hash berechnen
		$data = array(
		'transaction' => $_POST[transaction],
		'user_id' => $_POST[user_id],
		'project_id' => $_POST[project_id],
		'sender_holder' => $_POST[sender_holder],
		'sender_account_number' => $_POST[sender_account_number],
		'sender_bank_code' => $_POST[sender_bank_code],
		'sender_bank_name' => $_POST[sender_bank_name],
		'sender_bank_bic' => $_POST[sender_bank_bic],
		'sender_iban' => $_POST[sender_iban],
		'sender_country_id' => $_POST[sender_country_id],
		'recipient_holder' => $_POST[recipient_holder],
		'recipient_account_number' => $_POST[recipient_account_number],
		'recipient_bank_code' => $_POST[recipient_bank_code],
		'recipient_bank_name' => $_POST[recipient_bank_name],
		'recipient_bank_bic' => $_POST[recipient_bank_bic],
		'recipient_iban' => $_POST[recipient_iban],
		'recipient_country_id' => $_POST[recipient_country_id],
		'international_transaction' => $_POST[international_transaction],
		'amount' => $_POST[amount],
		'currency_id' => $_POST[currency_id],
		'reason_1' => $_POST[reason_1],
		'reason_2' => $_POST[reason_2],
		'security_criteria' => $_POST[security_criteria],
		'user_variable_0' => $_POST[user_variable_0],
		'user_variable_1' => $_POST[user_variable_1],
		'user_variable_2' => $_POST[user_variable_2],
		'user_variable_3' => $_POST[user_variable_3],
		'user_variable_4' => $_POST[user_variable_4],
		'user_variable_5' => $_POST[user_variable_5],
		'created' => $_POST[created],
		'project_password' => $this->strProjectPass
		);
	
		$data_implode = implode('|', $data);
		$hash = sha1($data_implode);
		if ($debug) fwrite($f,'DATA IMPLODE:'.$data_implode);
		if ($debug) fwrite($f,'HASH:'.$hash);
		if ($hash == $_POST['hash']){
			if ($debug) fwrite($f,'HASH OKAY'."\n");
			
				// process payment
				$objPayment = new GSALES2_OBJECT_PAYMENT();
					$objPayment->setPaymentProvider('sofortu');
					$objPayment->setAmount($_POST['amount']);
					$objPayment->setInvoiceId($_POST['user_variable_0']);
					$objPayment->setTransactionId($_POST['transaction']);
				if ($debug) fwrite($f,'Payment Object: '.print_r($objPayment,true)."\n");
				
				// set invoice to paid
				return $objPayment->checkPaidAmountAndSetInvoiceAsPaid();
			
			return true;
		} else {
			if ($debug) fwrite($f,'HASH NOT OKAY'."\n");
			return false;
		}
		if ($debug) fclose($f);
	}	

}
