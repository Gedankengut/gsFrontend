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

class FRONTEND_USERAUTH{

	var $objController;
	var $strMessage;

	public function __construct($objController){
		$this->objController = $objController;
	}
	
	public function isAuthorized(){
		if ($this->objController->objSession->get('auth') == true) return true;
		return false;
	}
	
	private function setMessage($strMessage){
		$this->strMessage = $strMessage;
	}
	
	public function getMessage(){
		return $this->strMessage;
	}
	
	public function getCustomerId(){
		if (false == $this->isAuthorized()) return false;
		$arrUserdetails = $this->objController->objSession->get('userdetails');
		return $arrUserdetails->getId();
	}
	
	public function getCustomerDetails(){
		if (false == $this->isAuthorized()) return false;
		return $this->objController->objSession->get('userdetails');
	}
	
	public function checkCredentials($strUsername, $strPassword, $booDoLogin=false){

		$objDataCustomer = new GSALES_DATA_CUSTOMER();
		$intResult = $objDataCustomer->customerLogin($strUsername, $strPassword);

		if ($intResult > 0 && $booDoLogin){
			$arrUserData = $objDataCustomer->getCustomerById($intResult);
			if ($arrUserData){
				$this->login($arrUserData);
			}
		}
		
		return $intResult;
		
	}

	public function login($arrUserData){
		$this->objController->objSession->store('auth',true);
		$this->objController->objSession->store('userdetails',$arrUserData);
		return true;
	}

	public function logout(){
		$this->objController->objSession->destroy();
	}			
}