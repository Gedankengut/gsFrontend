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

class GSALES_DATA_REFUND extends GSALES_DATA{

	public function __construct(){
		parent::__construct();
	}
	
	public function getRefundById($intId, $intCurrentUserId, $booSilentMode=false){
		$arrResult = $this->objSoapClient->getRefund($this->strAPIKey, $intId);
		if ($arrResult['status']->code != 0 && $booSilentMode) return false;
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message,$arrResult['status']->code);
		$objRefund = new GSALES2_OBJECT_REFUND($arrResult['result']);
		if ($objRefund->getCustomerId() != $intCurrentUserId) return false;
		return $objRefund;
	}
	
	public function getRefundsByCustomerId($intCustomerId){
		$arrFilter[] = array('field'=>'customers_id', 'operator'=>'is', 'value'=>$intCustomerId);
		$arrSort = array('field'=>'created', 'direction'=>'desc');
		$arrResult = $this->objSoapClient->getRefunds($this->strAPIKey, $arrFilter, $arrSort, 999, 0);
		if ($arrResult['status']->code != 0) throw new Exception($arrResult['status']->message,$arrResult['status']->code);
		$objRefunds = array();
		foreach ((array)$arrResult['result'] as $key => $refund) $objRefunds[] = new GSALES2_OBJECT_REFUND($refund);
		return $objRefunds;
	}
	
	public function getRefundPDFFile($intId, $intCurrentUserId){
		
		$arrRefund = $this->getRefundById($intId, $intCurrentUserId, true);
		if (false == $arrRefund) return false;
		
		$arrResult = $this->objSoapClient->getRefundPDF($this->strAPIKey, $intId);
		if (false == is_string($arrResult['result']->content)) throw new Exception($arrResult['status']->message,$arrResult['status']->code);
		
		$arrFileNameParts = explode('/',$arrResult['result']->name);
		
		$arrReturn['filename'] = $arrFileNameParts[count($arrFileNameParts)-1];
		$arrReturn['content'] = base64_decode($arrResult['result']->content);
		return $arrReturn;
		
	}
	
}