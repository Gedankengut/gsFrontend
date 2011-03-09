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

class GSALES_DATA_CUSTOMER extends GSALES_DATA{

	public function __construct(){
		parent::__construct();
	}
	
	public function getCustomerById($intId, $booSilentMode=false){
		$arrResult = $this->objSoapClient->getCustomer($this->strAPIKey, $intId);
		if ($arrResult['status']->code != 0 && $booSilentMode) return false;
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message,$arrResult['status']->code);
		return new GSALES2_OBJECT_CUSTOMER($arrResult['result']);
	}
	
	public function saveUpdatedFrontendPassword($intCustomerId, $strPassword){
		$strPasswordMD5 = md5($strPassword);
		$arrResult = $this->objSoapClient->changeCustomerFrontendPassword($this->strAPIKey, $intCustomerId, $strPasswordMD5);
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message,$arrResult['status']->code);
		return $arrResult['result'];
	}
	
	public function passwordLostStep1($strCustomerNoOrEmail){
		$arrResult = $this->objSoapClient->customerFrontendPasswordLost($this->strAPIKey, $strCustomerNoOrEmail);
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message, $arrResult['status']->code);
		return $arrResult['result'];
	}
	
	public function updateCustomerProposal($intId, $arrData){
		foreach ((array)$arrData as $key => $value) $arrData[$key] = utf8_encode($value);
		$arrResult = $this->objSoapClient->updateCustomerProposal($this->strAPIKey, $intId, $arrData);
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message,$arrResult['status']->code);
		return new GSALES2_OBJECT_CUSTOMER($arrResult['result']);
	}

}