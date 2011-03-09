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

class GSALES2_OBJECT_PAYMENT extends GSALES2_OBJECT {
	
	protected $payment_provider=false;
	protected $transaction_id=false;
	protected $invoice_id=false;
	protected $amount=false;
	
	public function __construct(){
	}

	// setters
	
	public function setPaymentProvider($strProvider){
		$this->payment_provider=$strProvider;
	}
	
	public function setTransactionId($strTransactionId){
		$this->transaction_id = $strTransactionId;
	}
	
	public function setInvoiceId($strInvoiceId){
		$this->invoice_id = $strInvoiceId;
	}
	
	public function setAmount($floatAmount){
		$this->amount = $floatAmount;
	}
	
	// getters
	
	public function getPaymentProvider(){
		return $this->payment_provider;
	}
	
	public function getTransactionId(){
		return $this->transaction_id;
	}
	
	public function getInvoiceId(){
		return $this->invoice_id;
	}
	
	public function getAmount(){
		return $this->amount;
	}	
	
	
	// methods
	
	public function checkPaidAmount(){
		if (false == $this->getInvoiceId()) return false;
		if (false == $this->getAmount()) return false;
		$objDataInvoice = new GSALES_DATA_INVOICE();
		$objInvoice = $objDataInvoice->getInvoiceByIdWithoutUserCheck($this->getInvoiceId(), true);
		if ($objInvoice->getOpenAmount() == $this->getAmount()) return true;
		if (number_format($this->getAmount(),2) == number_format($objInvoice->getOpenAmount(),2)) return true; // to compare calculated floats (currency) :(
		return false;
	}
	
	public function checkPaidAmountAndSetInvoiceAsPaid(){
		if ($this->checkPaidAmount()){
			$objDataInvoice = new GSALES_DATA_INVOICE();
			$arrResult = $objDataInvoice->setInvoiceStatePaid($this->getInvoiceId());
			if (false == $arrResult) return false;
			return true;
		}
		return false;
	}

}