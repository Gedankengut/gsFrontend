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

require_once(FE_DIR.'/lib/htmlform.php');
require_once(FE_DIR.'/lib/mailer.php');

// gsales2 data + objects
require_once(FE_DIR.'/models/gsales/data.php');
require_once(FE_DIR.'/models/gsales/data.invoice.php');
require_once(FE_DIR.'/models/gsales/data.refund.php');
require_once(FE_DIR.'/models/gsales/data.customer.php');
require_once(FE_DIR.'/models/gsales/data.contract.php');
require_once(FE_DIR.'/models/gsales/data.document.php');

require_once(FE_DIR.'/models/gsales/object.php');
require_once(FE_DIR.'/models/gsales/object.invoice.php');
require_once(FE_DIR.'/models/gsales/object.refund.php');
require_once(FE_DIR.'/models/gsales/object.customer.php');
require_once(FE_DIR.'/models/gsales/object.contract.php');
require_once(FE_DIR.'/models/gsales/object.document.php');
require_once(FE_DIR.'/models/gsales/object.payment.php');

class FRONTEND_CONTROLLER {

	public $objSmarty;
	public $objSession;	
	public $objDatabase;
	public $objUserAuth;
	
	public $arrUserRequest;
	public $strFrontendPath;
	
	public $booSmartyOutput=true;
	public $strPageTitle;
	
	public $arrMessages = array();
	
	public function __construct(){
		
		if (false == defined('GSALES2_API_URL')) throw new Exception('GSALES2_API_URL not set in config.php');
		if (false == defined('GSALES2_API_KEY')) throw new Exception('GSALES2_API_KEY not set in config.php');
		
		$this->strFrontendPath = $this->detectOwnPath();
		
		// init session
		require_once(FE_DIR.'/lib/session.php');
		$this->objSession = new FRONTEND_SESSION();		
		$this->arrMessages = $this->objSession->getAndRemove('messages');
		
		// init view: smarty template system
		require_once(FE_DIR.'/lib/Smarty-3.0.5/libs/Smarty.class.php');
		$this->objSmarty = new Smarty();
		$this->objSmarty->compile_check = true;
		$this->objSmarty->force_compile=true;
		$this->objSmarty->debugging = false;
		$this->objSmarty->template_dir = FE_DIR.'/templates/';
		$this->objSmarty->compile_dir = FE_DIR.'/templates_c/';
		
		/*
		// init db
		require_once(FE_DIR.'/lib/database.php');
		$this->objDatabase = new FRONTEND_DATABASE();
		*/
		
		// init user auth
		require_once(FE_DIR.'/lib/userauth.php');
		$this->objUserAuth = new FRONTEND_USERAUTH($this);
		
	}

	public function renderOutput(){
		$this->callControllerMethod();
	}	
	
	public function getUserRequest(){
		return $this->arrUserRequest;
	}
	
	public function setSmartyOutput($booSmartyOutput){
		$this->booSmartyOutput = $booSmartyOutput;
	}
	
	public function ao($arrayArray){
		echo '<pre>';
			print_r($arrayArray);
		echo '</pre>';
	}

	public function detectOwnPath(){
		$strScriptPath = $_SERVER['SCRIPT_NAME'];
		$strScriptPath = substr($strScriptPath,0,strlen($strScriptPath)-strlen('index.php'));
		return $strScriptPath;
	}
	
	public function setUserRequest($arrUserRequest){
		$this->arrUserRequest = $arrUserRequest;
	}
	
	public function setPageTitle($strValue){
		$this->strPageTitle = $strValue;
	}
	
	public function redirectTo($strController, $strAction='', $strParams=''){
		$this->objSession->store('messages', $this->arrMessages);
		$strExtend='';
		if ($strParams != '') $strExtend = '/'.$strParams;
		header('location: '.$this->strFrontendPath.$strController.'/'.$strAction.$strExtend);
		die();
	}	
	
	
	private function getTemplateFileName(){
		$arrUserRequest = $this->getUserRequest();
		$strTemplateFile = $arrUserRequest['controller'].'/'.$arrUserRequest['action'].'.tpl';
		$strTemplateFile = strtolower($strTemplateFile);
		return $strTemplateFile;
	}
	
	private function callControllerMethod(){
		
		$strController = $this->arrUserRequest['controller'];
		$strActionName = $this->arrUserRequest['action'];
		
		// if user is not authenticated -> redirect to index controller
		if (false == $this->objUserAuth->isAuthorized() && $strController != 'index'){
			$this->redirectTo('index','index');
		}
		
		// if user is authenticated and tries to access "index" than redirect to "mydata"
		if ($this->objUserAuth->isAuthorized() && $strController == 'index'){
			$this->redirectTo('mydata','index');
		}

		// assign template file according to requested "action"
		if ($this->booSmartyOutput) $this->objSmarty->assign('template',$this->getTemplateFileName());
		
		// assign frontend path to smarty
		$this->objSmarty->assign('fepath',$this->detectOwnPath());
		$this->objSmarty->assign('controller',$strController);
		$this->objSmarty->assign('isuser',$this->objUserAuth->isAuthorized());
		
		$strMethodToCall = $strActionName.'Action';
		
		if (method_exists($this, $strMethodToCall)){
			
			call_user_func(array($this, $strMethodToCall));
			
			if ($this->booSmartyOutput){
				if ($this->objUserAuth->isAuthorized()) $this->objSmarty->assign('userdetails',$this->objSession->get('userdetails'));
				$this->objSmarty->assign('messages',$this->arrMessages);
				$this->objSmarty->display('index.tpl');
			}
			
		} else {
			if ($this->booSmartyOutput) $this->objSmarty->display('404.tpl');
		}
		
	}	
	
	protected function setMessage($strMessage, $strType='success'){
		$this->arrMessages[] = array('type'=>$strType, 'message'=>$strMessage);
	}
	
}