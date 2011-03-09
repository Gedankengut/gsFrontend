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

class FRONTEND_CONTROLLER_REFUND extends FRONTEND_CONTROLLER {

	public function __construct(){
		parent::__construct();
	}
	
	public function indexAction(){
		$objDataInvoice = new GSALES_DATA_REFUND();
		$arrayOfObjRefund = $objDataInvoice->getRefundsByCustomerId($this->objUserAuth->getCustomerId()); // read customer refunds
		$this->objSmarty->assignByRef('refunds', $arrayOfObjRefund);		
	}
	
	public function pdfAction(){
		
		$this->setSmartyOutput(false);
		
		$arrUserRequest = $this->getUserRequest();
		if (false == isset($arrUserRequest['params']['0']) || false == is_numeric($arrUserRequest['params']['0'])){
			$this->redirectTo('refund'); // check for refund id to get pdf for
			return;
		}
		
		$objDataRefund = new GSALES_DATA_REFUND();
		$arrPDF = $objDataRefund->getRefundPDFFile($arrUserRequest['params']['0'], $this->objUserAuth->getCustomerId());
		
		if (false == $arrPDF){
			$this->redirectTo('refund');
			return;
		}
		
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$arrPDF['filename'].'"');
		echo $arrPDF['content'];
	}	
	
}