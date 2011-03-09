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

require_once(FE_DIR.'/lib/controller.php');

class FRONTEND_DISPATCHER {
	
	public function getControllerInstanceByUserRequest(){
		
		$booError = false;
		
		$arrUserRequest = $this->getAssocArrayByUserRequest($this->getUserRequest());
		$strController = $arrUserRequest['controller'];
		
		$strControllerFileName = $this->getControllerFileName($strController);
		$strControllerClassName = $this->getControllerClassName($strController);
		
		if (!array_key_exists($arrUserRequest['controller'],$this->getAvailableControllers())) $booError=true; // check if controller is known?
		
		if (false == $booError){
			require_once(FE_DIR.'/controller/'.$strControllerFileName); // include required controller file
			$objController = new $strControllerClassName();
			$objController->setUserRequest($arrUserRequest);
			return $objController;
		}
		
		throw new Exception('invalid user request / unknown controller');
		
	}
	
	public function getUserRequest(){
		
		$arrPHPSelf = explode('/',$_SERVER['PHP_SELF']);
		$arrRequest = explode('/',$_SERVER['REQUEST_URI']);

		$offset = 0;
		foreach ((array)$arrPHPSelf as $key => $strValue){
			if ($strValue != 'index.php') $offset++;
			else break;
		}

		$arrUserRequest = array();
		for ($i=$offset; $i<count($arrRequest);$i++){
			if (false == empty($arrRequest[$i])) $arrUserRequest[] = $arrRequest[$i];
		}

		$arrUserRequestResult = array();

		// startscreen (request by domain only e.g. http://www.example.de/)
		if (count($arrUserRequest) == 0){
			$arrUserRequestResult[0] = 'index';
			$arrUserRequestResult[1] = 'index';
		}

		// hiding "index" in root level
		if (count($arrUserRequest) == 1){
			// check if given request is a known controller and set action to index
			if (array_key_exists($arrUserRequest[0], $this->getAvailableControllers())){
				$arrUserRequestResult[0] = $arrUserRequest[0]; // known controller
				$arrUserRequestResult[1] = 'index'; // index action
			} else {
				$arrUserRequestResult[0] = 'index'; // index controller
				$arrUserRequestResult[1] = $arrUserRequest[0]; // given action
			}
		}

		// simple request with given action and controller
		if (count($arrUserRequest) > 1 ) $arrUserRequestResult = $arrUserRequest;
		
		return $arrUserRequestResult;
		
	}	

	private function getAssocArrayByUserRequest($arrUserRequest){
		
		// create array from additional parameters
		$arrAssoc = array();
		$arrParams = array();
		for ($i=2;$i<count($arrUserRequest);$i++) $arrParams[] = $arrUserRequest[$i];

		// create assoc array
		$arrAssoc['controller'] = $arrUserRequest[0];
		$arrAssoc['action'] = $arrUserRequest[1];
		$arrAssoc['params'] = $arrParams;
		
		return $arrAssoc;
		
	}

	private function getControllerFileName($strControllerName){
		return $strControllerName.'.php';
	}
	
	private function getControllerClassName($strControllerName){
		return 'FRONTEND_CONTROLLER_'.strtoupper($strControllerName);
	}
	
	private function getAvailableControllers(){
		$strDirectory = FE_DIR.'/controller';
		$arrControllerDir = scandir($strDirectory);
		foreach ($arrControllerDir as $key => $strDirItem){
			if (is_file($strDirectory.'/'.$strDirItem)){
				$strController = basename($strDirItem,'.php');
				$arrAvailableControllers[$strController] = $strController;
				unset($strController);
			}
		}
		return $arrAvailableControllers;
	}
}