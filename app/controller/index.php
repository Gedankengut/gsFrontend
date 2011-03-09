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

class FRONTEND_CONTROLLER_INDEX extends FRONTEND_CONTROLLER {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function indexAction(){

		$objForm = new HTMLFORM($this,'login');
		$objForm->setAction('index/index');
		$objForm->addField('E-Mail/Kundennummer',  'input', 'username',  '',  true, '', '', '');
		$objForm->addField('Passwort',  'password', 'password',  '',  true, '', '', '');
		$objForm->addSubmitButton('Login');
		$objForm->output();		

		$objData = new GSALES_DATA();	
		
		if ($objForm->isSubmittedAndValid()){
			
			$intResult  = $this->objUserAuth->checkCredentials($_POST['username'],$_POST['password'],true);
			
			if ($intResult < 0){
				
				switch ($intResult) {
				    case -1:
				    	if (false == strstr('@',$strUsername)) $this->setMessage('Die E-Mail Adresse ist nicht eindeutig, bitte über die Kundennummer einloggen.', 'error');	
				    	else $this->setMessage('Die Kundennummer ist nicht eindeutig, bitte über die E-Mail Adresse einloggen.', 'error');	
				        break;
				    case -2:
				        $this->setMessage('Kudenkonto konnte nicht gefunden werden.', 'error');
				        break;
				    case -3:
				        $this->setMessage('Ein Login mit diesem Kundenkonto ist derzeit nicht möglich.', 'error');
				        break;
				    case -4:
				        $this->setMessage('Das Passwort ist nicht korrekt.', 'error');
				        break;
				    default:
				}
							
				return false;
				
			} else {
				
				$this->setMessage('Login erfolgreich');
				$this->redirectTo('mydata', 'index');
				
			}
			
		}
		
	}
	
	public function passwordAction(){
		
		$objForm = new HTMLFORM($this,'password');
		$objForm->setAction('index/password');
		$objForm->addField('E-Mail/Kundennummer',  'input', 'username',  '',  true, '', '', '');
		$objForm->addSubmitButton('E-Mail zusenden');
		$objForm->output();

		if ($objForm->isSubmittedAndValid()){
			
			$objCustomerData = new GSALES_DATA_CUSTOMER();
			$intResult = $objCustomerData->passwordLostStep1($_POST['username']);
			
			if ($intResult < 0){
				
				switch ($intResult) {
				    case -1:
				    	if (false == strstr('@',$strUsername)) $this->setMessage('Die E-Mail Adresse ist nicht eindeutig, bitte anhand Ihrer Kundennummer probieren.', 'error');	
				    	else $this->setMessage('Die Kundennummer ist nicht eindeutig, bitte anhand Ihrer E-Mail Adresse probieren.', 'error');	
				        break;
				    case -2:
				        $this->setMessage('Kudenkonto konnte nicht gefunden werden.', 'error');
				        break;
				    case -3:
				        $this->setMessage('Die Passwort-Vergessen Funktion steht momentan aufgrund eines Fehlers nicht zur Verfügung.', 'error');
				        break;
				    default:
				}			
				
				return false;
				
			} else {
				
				// email versenden
				$objCustomer = $objCustomerData->getCustomerById($intResult);
				
				$arrData['id'] = $objCustomer->getId();
				$arrData['email'] = $objCustomer->getEmail();
				$arrData['token'] = $objCustomer->getFrontendPasswordLost();
				$arrData['url'] = FRONTEND_URL;
				
				$objMailer = new FRONTEND_MAILER();
				$objMailer->FromName =  MAIL_FROM_NAME;
				$objMailer->From = MAIL_FROM;
				$objMailer->useTemplateForSubjectAndBody('passwordlost.tpl',$arrData);
				$objMailer->AddAddress($objCustomer->getEmail(), trim($objCustomer->getFirstname().' '.$objCustomer->getLastname()));
				$objMailer->Send();
				
				$this->setMessage('Sie erhalten in Kürze eine E-Mail. Bitte befolgen Sie die Anweisungen um ein neues Passwort festzulegen.');
				$this->redirectTo('index', 'index');
			}
			
		}
		
	}
	
	public function newpasswordAction(){

		$intCustomerId = false;
		$strToken = false;
		
		$arrUserRequest = $this->getUserRequest();
		
		// check link or hidden post vars
		
		if (is_array($arrUserRequest['params'])){
			if (count($arrUserRequest['params']) == 2){
				$intCustomerId = $arrUserRequest['params'][0];
				$strToken = $arrUserRequest['params'][1];
			}
		}
		
		if (isset($_POST['cid'])) $intCustomerId = $_POST['cid'];
		if (isset($_POST['token'])) $strToken = $_POST['token'];
		
		if (false == $intCustomerId || false == $strToken){
			$this->setMessage('Ungültiger Link', 'error');
			$this->redirectTo('index','index');
			return;
		}
		
		// check if customer exists
		$objDataCustomer = new GSALES_DATA_CUSTOMER();
		$objCustomer = $objDataCustomer->getCustomerById($intCustomerId, true);
		
		if (false == $objCustomer){
			$this->setMessage('Ungültiger Link', 'error');
			$this->redirectTo('index','index');
			return;
		}

		// check if token is correct
		if ($objCustomer->getFrontendPasswordLost() != $strToken){
			$this->setMessage('Ungültiger Link', 'error');
			$this->redirectTo('index','index');
			return;
		}

		$objForm = new HTMLFORM($this,'newpassword');
		$objForm->setAction('index/newpassword');
		$objForm->setConfirmField('password1', 'password2');
		$objForm->setConfirmField('password1', 'password2');
		$objForm->addField('Neues Passwort',  'password', 'password1',  '',  true, 'password', '', '');
		$objForm->addField('Neues Passwort bestätigen',  'password', 'password2',  '',  true, '', '', '');
		$objForm->addField('cid','hidden','cid',$intCustomerId);
		$objForm->addField('token','hidden','token',$strToken);
		$objForm->addSubmitButton('Neues Passwort speichern');
		$objForm->output();	

		if ($objForm->isSubmittedAndValid()){
			
			$booCheck = $objDataCustomer->saveUpdatedFrontendPassword($intCustomerId, $_POST['password1']);
			
			if ($booCheck){
				$this->setMessage('Passwort wurde erfolgreich geändert');
				$this->redirectTo('index','index');
			} else {
				$this->setMessage('Passwort konnte aufgrund eines Programmfehlers nicht geändert werden', 'error');
			}
			
		}
		
	}
	
	public function paypalipnAction(){
		if (isset($_POST)){
			$this->setSmartyOutput(false);
			require_once(FE_DIR.'/lib/payment/paypal.php');
			$objPayPal = new PAYPAL(); 
			$objPayment == $objPayPal->checkAndvalidateIPN();
			if (false == $objPayment) return false;
		} else {
			$this->redirectTo('index','index');
		}
	}
	
	public function sofortpnAction(){
		if (isset($_POST)){
			$this->setSmartyOutput(false);
			require_once(FE_DIR.'/lib/payment/sofortueberweisung.php');
			$objSofort = new SOFORTUEBERWEISUNG();
			$objPayment = $objSofort->checkAndvalidatePN();
			if (false == $objPayment) return false;
		} else {
			$this->redirectTo('index','index');
		}		
	}

}