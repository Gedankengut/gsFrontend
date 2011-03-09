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

class GSALES2_OBJECT_CONTRACT extends GSALES2_OBJECT_INVOICE {
	
	protected $start;
	protected $interval;
	protected $stop;
	protected $duedate;	
	protected $billed_until;	

	// getters
	
	public function getStart(){
		return $this->start;
	}
	
	public function getInterval(){
		return $this->interval;
	}
	
	public function getStop(){
		return $this->stop;
	}
	
	public function getDueDate(){
		return $this->duedate;	
	}		
	
	public function getBilledUntil(){
		return $this->billed_until;	
	}		
	
}


class GSALES2_OBJECT_CONTRACT_POS extends GSALES2_OBJECT_INVOICE_POS{}
class GSALES2_OBJECT_CONTRACT_SUMM extends GSALES2_OBJECT_INVOICE_SUMM  {}

class GSALES2_OBJECT_CONTRACT_MULTIPOS extends GSALES2_OBJECT_CONTRACT_POS {
	
	protected $start;
	protected $interval;
	protected $stop;
	protected $duedate;
	protected $billed_until;
	protected $curr_symbol;
	
	public function __construct($objPos, $objContract){
		foreach ($objPos as $key => $value){
			$this->$key = $value;
		}
		$this->setContractBaseData($objContract);
	}
	
	private function setContractBaseData($arrBase){
		$this->start = $arrBase->getStart();
		$this->interval =  $arrBase->getInterval();
		$this->stop =  $arrBase->getStop();
		$this->duedate =  $arrBase->getDueDate();
		$this->billed_until =  $arrBase->getBilledUntil();
		$this->curr_symbol =  $arrBase->getCurrencySymbol();
	}
	
	public function getStart(){
		return $this->start;
	}
	
	public function getInterval(){
		return $this->interval;
	}
	
	public function getStop(){
		return $this->stop;
	}
	
	public function getDueDate(){
		return $this->duedate;	
	}

	public function getBilledUntil(){
		return $this->billed_until;	
	}

	public function getCurrencySymbol(){
		return $this->curr_symbol;
	}
	
	public function getFormatedRoundedPriceWithSymbol(){
		return number_format($this->getPrice(),2,',','.') .'&nbsp;'. $this->getCurrencySymbol();	
	}	
	
}
