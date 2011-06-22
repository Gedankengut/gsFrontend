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

class GSALES2_OBJECT_INVOICE extends GSALES2_OBJECT {
	
	protected $id;
	protected $created;
	protected $invoiceno;
	protected $payable;
	protected $partialpayment;
	protected $deliverydate;
	protected $customers_id;
	protected $customerno;
	protected $customer_zip;
	protected $customer_city;
	protected $customer_country;
	protected $status_id;
	protected $status_date;
	protected $rounded_amount;
	protected $curr_id;
	protected $curr_symbol;
	protected $curr_rate;
	protected $rounded_curr_amount;
	
	protected $pos;
	protected $summ;
	protected $dunning;
	protected $dunning_fee;

	public function __construct($arrAPIResult=''){
		if (is_object($arrAPIResult)){
			foreach ($this as $key => $value) $this->$key = $arrAPIResult->base->$key; // base
			foreach ((array)$arrAPIResult->pos as $key => $arrValuePos) $this->pos[$arrValuePos->id] = new GSALES2_OBJECT_INVOICE_POS($arrValuePos); // pos
			$this->summ = new GSALES2_OBJECT_INVOICE_SUMM($arrAPIResult->summ); // summ
			
			if ($arrAPIResult->dunning){
				foreach ((array) $arrAPIResult->dunning as $key => $arrValueDunning){
					$objDunning = new GSALES2_OBJECT_INVOICE_DUNNING($arrValueDunning); // dunning information
					$this->dunning[$objDunning->getId()] = $objDunning;
					if ($objDunning->getFee() > $this->dunning_fee) $this->dunning_fee = $objDunning->getFee();
				}
			}
		}
	}
	
	// getters
	
	public function getId(){
		return $this->id;
	}
	
	public function getCreated(){
		return $this->created;
	}	
	
	public function getInvoiceNo(){
		return $this->invoiceno;
	}	
	
	public function getPayable(){
		return $this->payable;
	}

	public function getPartialPayment(){
		return $this->partialpayment;
	}

	public function getDeliveryDate(){
		return $this->deliverydate;
	}	
	
	public function getCustomerId(){
		return $this->customers_id;
	}
	
	public function getCustomerNo(){
		return $this->customerno;
	}
	
	public function getCustomerZIP(){
		return $this->customerno;
	}
	
	public function getCustomerCity(){
		return $this->customerno;
	}
	
	public function getCustomerCountry(){
		return $this->customerno;
	}
	
	public function getStatusId(){
		return $this->status_id;
	}
	
	public function getStatusDate(){
		return $this->status_date;
	}

	public function getRoundedAmount(){
		return $this->rounded_amount;
	}
	
	public function getCurrencyId(){
		return $this->curr_id;
	}
	
	public function getCurrencySymbol(){
		return $this->curr_symbol;
	}
	
	public function getCurrencyRate(){
		return $this->curr_rate;
	}
	
	public function getRoundedCurrencyAmount(){
		return $this->rounded_curr_amount;
	}
	
	public function getPositions(){
		return $this->pos;
	}
	
	public function getSumm(){
		return $this->summ;
	}
	
	public function getDunning(){
		return $this->dunning;
	}
	
	public function getDunningFee(){
		return $this->dunning_fee;
	}
	
	public function getFormatedDunningFeeWithSymbol(){
		if ($this->isDefaultCurrency()){
			return number_format($this->getDunningFee(),2,',','.') .'&nbsp;'. $this->getCurrencySymbol();	
		} else {
			return number_format($this->getDunningFee()*$this->getCurrencyRate(),2,',','.') .'&nbsp;'. $this->getCurrencySymbol();	
		}
	}	
	
	public function isOpenAndDunned(){
		if ($this->getStatusId() == 0 && $this->getDunning()) return true;
		return false;
	}
	
	public function getStatusIdAsText(){
		$arrStatus[0] = 'offen';
		$arrStatus[1] = 'bezahlt';
		$arrStatus[2] = 'storniert';
		return $arrStatus[$this->getStatusId()];
	}
	
	public function isDefaultCurrency(){
		if ($this->getCurrencyId() == 0) return true;
		return false;
	}
	
	public function getFormatedRoundedAmountWithSymbol(){
		if ($this->isDefaultCurrency()){
			return number_format($this->getRoundedAmount(),2,',','.') .'&nbsp;'. $this->getCurrencySymbol();	
		} else {
			return number_format($this->getRoundedCurrencyAmount(),2,',','.') .'&nbsp;'. $this->getCurrencySymbol();	
		}
	}
	
	public function getOpenAmount(){
		if ($this->getStatusId() != 0) return false;
		$floatOpenAmount = $this->getRoundedAmount()-$this->getPartialPayment()+$this->getDunningFee();
		return $floatOpenAmount;
	}
	
	public function getFormatedRoundOpenAmountWithSymbol(){
		if (false == $this->getOpenAmount()) return '-';
		if ($this->isDefaultCurrency()){
			return number_format($this->getOpenAmount(),2,',','.') .'&nbsp;'. $this->getCurrencySymbol();	
		} else {
			return number_format($this->getOpenAmount()*$this->getCurrencyRate(),2,',','.') .'&nbsp;'. $this->getCurrencySymbol();	
		}
	}
	
}




class GSALES2_OBJECT_INVOICE_POS extends GSALES2_OBJECT {
	
	protected $id;
	protected $article_id;
	protected $quantity;
	protected $unit;
	protected $pos_txt;
	protected $vars_pos_txt;
	protected $price;
	protected $discount;
	protected $tax;
	
	public function __construct($arrPosition){
		foreach ($this as $key => $value) $this->$key = $arrPosition->$key;
	}
	
	// getters
	
	public function getId(){
		return $this->id;
	}
	
	public function getArticleId(){
		return $this->article_id;
	}
	
	public function getQuantity(){
		return $this->quantity;
	}
	
	public function getUnit(){
		return $this->unit;	
	}
	
	public function getPosText($booVarsReplaced=true){
		if ($booVarsReplaced)return $this->vars_pos_txt;
		return $this->pos_txt;
	}
	
	public function getPrice(){
		return $this->price;	
	}
	
	public function getDiscount(){
		return $this->discount;	
	}
	
	public function getTax(){
		return floatval($this->tax);
	}
	
	public function getFormatedTax(){
		return number_format($this->getTax(),1,',','.') .'%';
	}	
	
}




class GSALES2_OBJECT_INVOICE_SUMM extends GSALES2_OBJECT {
	
	protected $net;
	protected $discount;
	protected $tax;
	protected $gross;
	
	public function __construct($arrSumm){
		foreach ($this as $key => $value) $this->$key = $arrSumm->$key;
	}
	
	// getters
	
	public function getNet(){
		return $this->net;
	}
	
	public function getDiscount(){
		return $this->discount;
	}

	public function getTax(){
		return $this->tax;
	}

	public function getGross(){
		return $this->gross;
	}
	
}


class GSALES2_OBJECT_INVOICE_DUNNING extends GSALES2_OBJECT {
	
	protected $id;
	protected $invoices_id;
	protected $created;
	protected $action;
	protected $fee;
	
	public function __construct($arrSumm){
		foreach ($this as $key => $value) $this->$key = $arrSumm->$key;
	}
	
	// getters
	
	public function getId(){
		return $this->id;
	}
	
	public function getInvoicesId(){
		return $this->invoices_id;
	}

	public function getCreated(){
		return $this->created;
	}

	public function getAction(){
		return $this->action;
	}
	
	public function getFee(){
		return $this->fee;
	}
	
	public function getActionAsText(){
		$intId = $this->getAction();
		if ($intId == 0) return 'Infomail';
		if ($intId == 1) return '1. Mahnung';
		if ($intId == 2) return '2. Mahnung';
		if ($intId == 3) return '3. Mahnung';
		if ($intId == 99) return 'Übergabe an mediafinanz Inkasso';
		return 'unbekannt';
	}
	
}