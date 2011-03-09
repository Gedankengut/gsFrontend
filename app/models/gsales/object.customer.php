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

class GSALES2_OBJECT_CUSTOMER extends GSALES2_OBJECT {
	
	protected $id;
	protected $created;
	protected $customerno;
	protected $company;
	protected $title;
	protected $firstname;
	protected $lastname;
	protected $address;
	protected $zip;
	protected $city;
	protected $country;
	protected $taxnumber;
	protected $phone;
	protected $cellular;
	protected $fax;
	protected $email;
	protected $homepage;
	protected $bank_account_no;
	protected $bank_code;
	protected $bank_name;
	protected $bank_account_owner;
	protected $bank_iban;
	protected $bank_bic;
	protected $dtaus;
	protected $frontend_passwordlost;
	protected $proposed_changes;
	
	public function __construct($arrAPIResult=''){
		if (is_object($arrAPIResult)){
			foreach ($this as $key => $value) $this->$key = $arrAPIResult->$key;
		}
	}
	
	public function setFromArray($arrData){
		foreach ((array)$arrData as $key => $value){
			$this->$key = $value;
		}
	}
	
	// getters
	
	public function getId(){
		return $this->id;
	}
	
	public function getCreated(){
		return $this->created;
	}	
	
	public function getCustomerNo(){
		return $this->customerno;
	}	
	
	public function getCompany(){
		return $this->company;
	}	
	
	public function getTitle(){
		return $this->title;
	}	
	
	public function getFirstname(){
		return $this->firstname;
	}
	
	public function getLastname(){
		return $this->lastname;
	}
	
	public function getAddress(){
		return $this->address;
	}
	
	public function getZIP(){
		return $this->zip;
	}
	
	public function getCity(){
		return $this->city;
	}
	
	public function getCountry(){
		return $this->country;
	}
	
	public function getTaxNumber(){
		return $this->taxnumber;
	}
	
	public function getPhone(){
		return $this->phone;
	}
	
	public function getCellular(){
		return $this->cellular;
	}
	
	public function getFax(){
		return $this->fax;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function getHomepage(){
		return $this->homepage;
	}
	
	public function getBankAccountNo(){
		return $this->bank_account_no;
	}
	
	public function getBankCode(){
		return $this->bank_code;
	}
	
	public function getBankAccountOwner(){
		return $this->bank_account_owner;
	}
	
	public function geBankIBAN(){
		return $this->bank_iban;
	}
	
	public function getBankBIC(){
		return $this->bank_bic;
	}
	
	public function getDTAUS(){
		return $this->dtaus;
	}
	
	public function getFrontendPasswordLost(){
		return $this->frontend_passwordlost;
	}
	
	public function getProposedChanges(){
		if ($this->proposed_changes != '') return unserialize(base64_decode($this->proposed_changes));
		return false;
	}

	public function overriteProposalWithCurrentValues(){
		if ($this->proposed_changes != ''){
			$tmpArray = unserialize(base64_decode($this->proposed_changes));
			foreach ((array)$tmpArray as $key => $value) $this->$key = $value;
		}
		return false;
		
	}
	
}