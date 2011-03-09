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

class FRONTEND_DATABASE{
	
	// resources
	protected $resCon;
	
	public function __construct(){
		// connect to databse
		$this->resCon = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		if (!$this->resCon) throw new Exception('Database connection failed');
		mysql_select_db(DB_DBNAME,$this->resCon);
		
		// set utf8
		mysql_query("SET NAMES utf8", $this->resCon);
		mysql_query('SET CHARACTER SET utf8', $this->resCon);
	}
	
	public function sqlSelect($strQuery, $strDBFieldToKey=''){
		$arrRes = mysql_query($strQuery, $this->resCon);
		if (!$arrRes) throw new Exception('Database Select Query failed:'.$strQuery.' mysql error:'.mysql_error($this->resCon), mysql_errno($this->resCon));
		
		$arrData = array();
		while ($arrRow = mysql_fetch_assoc($arrRes)) $arrData[] = $arrRow;
		unset($arrRow);
		
		if ($strDBFieldToKey != '') $arrData = $this->setDBFieldToArrayKey($arrData, $strDBFieldToKey);
		return $arrData;
	}
	
	public function sqlInsert($strQuery){
		$booCheck = mysql_query($strQuery, $this->resCon);
		if (!$booCheck) throw new Exception('Database Insert Query failed:'.$strQuery.' mysql error:'.mysql_error($this->resCon), mysql_errno($this->resCon));
		$intLastInsertID = mysql_insert_id($this->resCon);
		return $intLastInsertID;
	}
	
	public function sqlUpdate($strQuery){
		$booCheck = mysql_query($strQuery, $this->resCon);
		if (!$booCheck) throw new Exception('Database Update Query failed:'.$strQuery.' mysql error:'.mysql_error($this->resCon), mysql_errno($this->resCon));
		return true;
	}
	
	public function sqlDelete($strQuery){
		$booCheck = mysql_query($strQuery, $this->resCon);
		if (!$booCheck) throw new Exception('Database Delete Query failed:'.$strQuery.' mysql error:'.mysql_error($this->resCon), mysql_errno($this->resCon));
		return true;
	}
	
	private function setDBFieldToArrayKey($arrData, $strField){
		$arrTransformed = array();
		foreach ((array)$arrData as $intKey => $arrValue) $arrayTransformed[$arrValue[$strField]] = $arrValue;
		return $arrTransformed;
	}
	
}