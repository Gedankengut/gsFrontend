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

class FRONTEND_SESSION {
	
	protected $strSessionIdent;
	
	public function __construct($booSessionStart=true){
		if ($booSessionStart) session_start();
		
		if (defined('FRONTEND_SESSION_IDENT')) $this->strSessionIdent = FRONTEND_SESSION_IDENT;
		else $this->strSessionIdent = md5($_SERVER['SERVER_ADDR'].$_SERVER['SERVER_NAME']); // fallback
		
	}

	public function store($strKey, $value){
		$_SESSION[$this->strSessionIdent][$strKey] = $value;
		return true;
	}
	
	public function get($strKey){
		if (isset($_SESSION[$this->strSessionIdent][$strKey])) return $_SESSION[$this->strSessionIdent][$strKey];
		return false;
	}
	
	public function getAndRemove($strKey){
		if (isset($_SESSION[$this->strSessionIdent][$strKey])){
			$arrReturn = $_SESSION[$this->strSessionIdent][$strKey];
			$this->remove($strKey);
			return $arrReturn;
		}
		return false;
	}
	
	public function remove($strKey){
		unset ($_SESSION[$this->strSessionIdent][$strKey]);
		if (isset($_SESSION[$this->strSessionIdent][$strKey])) return false;
		return true;
	}
	
	public function getSession(){
		return $_SESSION[$this->strSessionIdent];
	}
	
	public function destroy(){
		session_destroy();
		return true;
	}
	
}