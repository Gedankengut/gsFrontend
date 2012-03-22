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

class FRONTEND_CONTROLLER_INVOICE extends FRONTEND_CONTROLLER {

	public function __construct(){
		parent::__construct();
	}
	
	public function indexAction(){
		
		// rechnungen des kunden auslesen
		$objDataInvoice = new GSALES_DATA_INVOICE();
		$arrayOfObjInvoice = $objDataInvoice->getInvoicesByCustomerId($this->objUserAuth->getCustomerId());
		$this->objSmarty->assignByRef('invoices', $arrayOfObjInvoice);
		$this->objSmarty->assign('payment_paypal', PAYPAL_ENABLE);
		$this->objSmarty->assign('payment_sofort', SOFORTU_ENABLE);
		
	}
	
	public function pdfAction(){
		
		$this->setSmartyOutput(false);
		$arrUserRequest = $this->getUserRequest();
		
		if (false == isset($arrUserRequest['params']['0']) || false == is_numeric($arrUserRequest['params']['0'])){
			$this->redirectTo('invoice'); // check for invoice id to get pdf for
			return;
		}
		
		$objDataInvoice = new GSALES_DATA_INVOICE();
		$arrPDF = $objDataInvoice->getInvoicePDFFile($arrUserRequest['params']['0'], $this->objUserAuth->getCustomerId());
		
		if (false == $arrPDF){
			$this->redirectTo('invoice');
			return;
		}
		
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$arrPDF['filename'].'"');
		echo $arrPDF['content'];		
		
	}
	
	public function payAction(){
		
		$arrUserRequest = $this->getUserRequest();
		
		// no invoice id given
		if (false == isset($arrUserRequest['params']['0']) || false == is_numeric($arrUserRequest['params']['0'])){
			$this->redirectTo('invoice');
			return;
		}
		
		$objDataInvoice = new GSALES_DATA_INVOICE();
		$objInvoice = $objDataInvoice->getInvoiceById($arrUserRequest['params']['0'], $this->objUserAuth->getCustomerId(), true);
		
		// invoice does not exist (or belongs to another customer)
		if (false == $objInvoice){
			$this->redirectTo('invoice');
			return;
		}
		
		// invoice is already payed
		if ($objInvoice->getStatusId() != 0){
			$this->setMessage('Die von Ihnen gewählte Rechnung ist nicht offen');
			$this->redirectTo('invoice');
			return;
		}
		
		$this->objSmarty->assignByRef('invoice', $objInvoice);
		$this->objSmarty->assign('payment_paypal', PAYPAL_ENABLE);
		$this->objSmarty->assign('payment_sofort', SOFORTU_ENABLE);
		
	}
	
	public function paypaypalAction(){

		$this->setSmartyOutput(false);
		$arrUserRequest = $this->getUserRequest();

		// no invoice id given
		if (false == isset($arrUserRequest['params']['0']) || false == is_numeric($arrUserRequest['params']['0'])){
			$this->redirectTo('invoice');
			return;
		}		
		
		// paypal is disabled
		if (false == PAYPAL_ENABLE){
			$this->setMessage('Die Bezahlung über PayPal ist momentan nicht möglich', 'error');
			$this->redirectTo('invoice','pay',$arrUserRequest['params']['0']);
			return;
		}
		
		$objDataInvoice = new GSALES_DATA_INVOICE();
		$objInvoice = $objDataInvoice->getInvoiceById($arrUserRequest['params']['0'], $this->objUserAuth->getCustomerId(), true);
		
		// invoice does not exist (or belongs to another customer)
		if (false == $objInvoice){
			$this->redirectTo('invoice');
			return;
		}
		
		// read customer details
		$objDataCustomer = new GSALES_DATA_CUSTOMER();
		$objCustomer = $objDataCustomer->getCustomerById($this->objUserAuth->getCustomerId());
		
		// do paypal
		require_once(FE_DIR.'/lib/payment/paypal.php');
		$objPayPal = new PAYPAL(); 

		// invoice data
		$objPayPal->add('item_name','Rechnung '.$objInvoice->getInvoiceNo());
		$objPayPal->add('amount',$objInvoice->getOpenAmount());
		$objPayPal->add('custom',$objInvoice->getId()); // custom field -> invoice id
		
		// customer data
		$objPayPal->add('first_name',$objCustomer->getFirstname());
		$objPayPal->add('last_name',$objCustomer->getLastname());
		$objPayPal->add('address1',$objCustomer->getAddress());
		$objPayPal->add('city',$objCustomer->getCity());
		$objPayPal->add('zip',$objCustomer->getZIP());
		$objPayPal->add('email',$objCustomer->getEmail());
		
		$objPayPal->startProcess();		
		
	}
	
	public function paysofortAction(){
		
		$this->setSmartyOutput(false);
		$arrUserRequest = $this->getUserRequest();
		
		// no invoice id given
		if (false == isset($arrUserRequest['params']['0']) || false == is_numeric($arrUserRequest['params']['0'])){
			$this->redirectTo('invoice');
			return;
		}
		
		// sofortu is disabled
		if (false == SOFORTU_ENABLE){
			$this->setMessage('Die Bezahlung über sofortüberweisung.de ist momentan nicht möglich', 'error');
			$this->redirectTo('invoice','pay',$arrUserRequest['params']['0']);
			return;
		}

		$objDataInvoice = new GSALES_DATA_INVOICE();
		$objInvoice = $objDataInvoice->getInvoiceById($arrUserRequest['params']['0'], $this->objUserAuth->getCustomerId(), true);
		
		// invoice does not exist (or belongs to another customer)
		if (false == $objInvoice){
			$this->redirectTo('invoice');
			return;
		}			
		
		// do sofortüberweisung
		require_once(FE_DIR.'/lib/payment/sofortueberweisung.php');
		$objSofort = new SOFORTUEBERWEISUNG();
		$objSofort->setAmount($objInvoice->getOpenAmount());
		$objSofort->setInvoiceId($objInvoice->getId());
		$objSofort->setReason1('SU RNR '.$objInvoice->getInvoiceNo());
		$objSofort->setReason2('VOM '.date("d.m.Y",strtotime($objInvoice->getCreated())));
		
		$objSofort->startProcess();
		
	}
	
	public function paysuccessAction(){
		// displays only according template
	}

	public function payfailureAction(){
		// displays only according template
	}
	
}