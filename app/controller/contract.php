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

class FRONTEND_CONTROLLER_CONTRACT extends FRONTEND_CONTROLLER {

	public function __construct(){
		parent::__construct();
	}
	
	public function indexAction(){
		
		$objDataInvoice = new GSALES_DATA_CONTRACT();
		$arrayOfObjContractPositions = $objDataInvoice->getContractPositionsByCustomerId($this->objUserAuth->getCustomerId());
		$this->objSmarty->assignByRef('contractpositions', $arrayOfObjContractPositions);
				
	}
	
}