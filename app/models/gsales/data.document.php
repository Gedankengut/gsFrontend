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

class GSALES_DATA_DOCUMENT extends GSALES_DATA{

	public function __construct(){
		parent::__construct();
	}
	
	public function getDocumentById($intId, $booSilentMode=false ){
		$arrResult = $this->objSoapClient->getCustomerDocument($this->strAPIKey, $intId);
		if ($arrResult['status']->code != 0 && $booSilentMode) return false;
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message,$arrResult['status']->code);
		return new GSALES2_OBJECT_DOCUMENT($arrResult['result']);
	}
	
	public function getDocumentsForCustomerId($intCustomerId){
		$arrFilter[] = array('field'=>'customers_id', 'operator'=>'is', 'value'=>$intCustomerId);
		$arrFilter[] = array('field'=>'public', 'operator'=>'is', 'value'=>'1');
		$arrSort = array('field'=>'created', 'direction'=>'desc');
		$arrResult = $this->objSoapClient->getCustomerDocuments($this->strAPIKey, $arrFilter, $arrSort, 999, 0);
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message,$arrResult['status']->code);
		$objDocuments = array();
		foreach ((array)$arrResult['result'] as $key => $doc) $objDocuments[] = new GSALES2_OBJECT_DOCUMENT($doc);
		return $objDocuments;
	}
	
	public function getDocumentFile($intId, $intCurrentUserId){
		$arrDocument = $this->getDocumentById($intId, true);
		
		if (false == $arrDocument) return false;
		
		if ($arrDocument->getCustomerId() != $intCurrentUserId) return false;
		if (false == $arrDocument->getPublic()) return false;

		$arrResult = $this->objSoapClient->getDocumentFile($this->strAPIKey, $intId);
		if (false == is_string($arrResult['result']->content)) throw new Exception($arrResult['status']->message, $arrResult['status']->code);
		
		$arrReturn['filename'] = $arrResult['result']->name;
		$arrReturn['content'] = base64_decode($arrResult['result']->content);
		return $arrReturn;		
	}
	
}