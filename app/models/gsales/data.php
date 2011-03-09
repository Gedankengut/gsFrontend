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

class GSALES_DATA {

	protected $strAPIKey;
	protected $strAPIUrl;
	
	protected $objSoapClient;
	
	public function __construct($booUseConfig=true, $strAPIKey='', $strAPIUrl=''){
		
		if ($booUseConfig){
			$this->setAPIUrl(GSALES2_API_URL);
			$this->setAPIKey(GSALES2_API_KEY);
		} else {
			if ($strAPIKey == '') throw new Exception('api key is missing');
			if ($strAPIUrl == '') throw new Exception('api url is missing');
			$this->setAPIKey($strAPIKey);
			$this->setAPIUrl($strAPIUrl);
		}

		// init soap client
		ini_set("soap.wsdl_cache_enabled", "0");
		$this->objSoapClient = new soapclient($this->strAPIUrl);
	}
	
	public function setAPIKey($strAPIKey){
		$this->strAPIKey = $strAPIKey;
		return true;
	}
	
	public function setAPIUrl($strAPIUrl){
		$this->strAPIUrl = $strAPIUrl;
		return true;
	}
	
	public function customerLogin($strUsername, $strPassword){
		$strPasswordHash = md5($strPassword);
		$arrResult = $this->objSoapClient->doCustomerFrontendLogin($this->strAPIKey, $strUsername, $strPasswordHash);
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message, $arrResult['status']->code);
		return $arrResult['result'];
	}
	
}