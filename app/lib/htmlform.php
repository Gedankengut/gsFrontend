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

class HTMLFORM{
	
	// Objects
	var $objRefController;
	
	// Strings
	var $strFormID='default';
	var $strSubmitLabel='Submit';
	var $strAction = '';
	var $strValidatorMessage ='';

	var $strEditId = '';
	var $strEditFieldPrimKey = 'id';
	
	// Arrays
	var $arrForm;
	var $arrMatchTwoFields;
	
	// Boolean
	var $booValidatorRunned = false;
	var $booValidatorResult = false;
	var $booSubmit = true;
	
	public function __construct($objRefController, $strFormID='default'){
		$this->objRefController = $objRefController;
		if ($strFormID != '') $this->setFormID($strFormID);
	}

	// setters and getters
	
	public function setFormID($strFormID){
		$this->strFormID = $strFormID;
	}
	
	private function getFormID(){
		return $this->strFormID;
	}
	
	public function setConfirmField($strName1, $strName2){
		$this->arrMatchTwoFields[] = array('f1' => $strName1, 'f2' => $strName2); 
	}	
	
	public function setEditId($strEditId){
		$this->strEditId = $strEditId;
	}
	
	public function setSubmitButtonLabel($strLabel){
		$this->strSubmitLabel = $strLabel;
	}
	
	private function getSubmitButtonLabel(){
		return $this->strSubmitLabel;
	}
	
	public function setSubmit($booSubmit){
		$this->booSubmit = $booSubmit;
	}
	
	private function getSubmit(){
		return $this->booSubmit;
	}
	
	public function setAction($strAction, $booPrependOwnPath=true){
		if ($booPrependOwnPath) $this->strAction = $this->objRefController->detectOwnPath();
		$this->strAction .= $strAction;
	}
	
	private function getAction(){
		return $this->strAction;
	}
	
	private function setValidatorMessage($strMessage){
		$this->strValidatorMessage = $strMessage;
	}
	
	private function getValidatorMessage(){
		$strRet = $this->strValidatorMessage;
		$this->setValidatorMessage('');
		return $strRet;
	}
	
	
	// public section
	
	public function addField($strLabel, $strType, $strName='', $strDefValue='', $booRequired=false, $strValidator='', $arrOptions=array(), $strCustomHTML='', $booReadonly=false){

		if ($this->isFormSubmitted() && $strName != '' && isset($_POST[$strName])) $strDefValue = $_POST[$strName];
		
		if (is_array($strDefValue)){
			foreach ((array)$strDefValue as $intKey => $strVal){
				$strDefValue[$intKey] = htmlspecialchars($strVal);
			}
		} else {
			$strDefValue = htmlspecialchars($strDefValue); // by outputting user input in form fields protect our html by escaping special chars	
		}
		
		
		$strClassReadonly = '';
		$strReadonlyString = '';
		
    	if ($booReadonly){
    		$strClassReadonly = 'readonly';
    		$strReadonlyString = ' readonly="readonly" ';
    	}
		
		switch ($strType) {
		    case 'input':
		        $strHTMLCode = '<input id="'.$this->getFormID().'-'.$strName.'" type="text" class="text '.$strClassReadonly.'" name="'.$strName.'" value="'.$strDefValue.'" '.$strReadonlyString.' '.$strCustomHTML.' />';
		        break;
		    case 'password':
		        $strHTMLCode = '<input id="'.$this->getFormID().'-'.$strName.'" type="password" class="text '.$strClassReadonly.'" name="'.$strName.'" value="'.$strDefValue.'" '.$strReadonlyString.' '.$strCustomHTML.' />';
		        break;
		    case 'dropdown':
		        $strHTMLCode = '<select id="'.$this->getFormID().'-'.$strName.'" name="'.$strName.'" '.$strCustomHTML.'>'.$this->getDropDownOptionElements($arrOptions, $strDefValue).'</select>';
		        break;
		    case 'textarea':
		        $strHTMLCode = '<textarea id="'.$this->getFormID().'-'.$strName.'" type="text" class="text '.$strClassReadonly.'" name="'.$strName.'" '.$strReadonlyString.' '.$strCustomHTML.'>'.$strDefValue.'</textarea>';
		        break;
		    case 'hidden':
		        $strHTMLCode = '<input type="hidden" id="'.$this->getFormID().'-'.$strName.'" type="text" name="'.$strName.'" value="'.$strDefValue.'" />';
		        break;		        
		    default:
		    	throw new Exception($strType .' is unknown form field type');
		}
		
		$this->addFieldToArrForm($strName, $strLabel, $strType, $strHTMLCode, $booRequired, $strValidator);
		
	}
	
	public function addLinkButton($strLabel, $strLink){
    	$strHTMLCode = '<button type="button" class="actionbutton" title="'.$strLabel.'" onclick="javascript:window.location.href = \''.$strLink.'\'"><span>'.$strLabel.'</span></button>';
    	$this->addFieldToArrForm('linkButton','','linkButton',$strHTMLCode);
	}
	
	public function addSubmitButton($strLabel){
		$strHTMLCode = '<input type="submit" name="submitButton" value="'.$strLabel.'" class="actionbutton submit" />';
		$strName = 'submitButton';
		$this->addFieldToArrForm('submitButton','', 'submitButton',$strHTMLCode);
	}
	
	public function output(){
		
		$booShowForm = true;
		
		if ($this->isFormSubmitted()){
			if ($this->validateForm() == false){
				$booShowForm = false;
			}
		}

		if ($booShowForm) $this->arrForm[$this->getFormID()]['show'] = 1;

		$this->arrForm[$this->getFormID()]['formID'] = $this->getFormID();
		$this->arrForm[$this->getFormID()]['submitLabel'] = $this->getSubmitButtonLabel();
		$this->arrForm[$this->getFormID()]['action'] = $this->getAction();
		$this->arrForm[$this->getFormID()]['submit'] = $this->getSubmit();
		
		$this->objRefController->objSmarty->append('form',$this->arrForm,true);
		
	}
	
	public function isSubmittedAndValid(){
		if (!$this->isFormSubmitted()) return false;
		if (!$this->validateForm()) return false;
		return true;
	}
	
	// private section
	
	private function addFieldToArrForm($strName, $strLabel='', $strType='', $strHTMLCode='', $strRequired='', $strValidator=''){
		$this->arrForm[$this->getFormID()]['fields'][$strName]['label'] = $strLabel;
		$this->arrForm[$this->getFormID()]['fields'][$strName]['type'] = $strType;
		$this->arrForm[$this->getFormID()]['fields'][$strName]['html'] = $strHTMLCode;
		$this->arrForm[$this->getFormID()]['fields'][$strName]['required'] = $strRequired;
		$this->arrForm[$this->getFormID()]['fields'][$strName]['validator'] = $strValidator;		
		$this->arrForm[$this->getFormID()]['fields'][$strName]['elementid'] = $this->getFormID().'-'.$strName;		
	}
	
	private function isFormSubmitted(){
		if (!isset($_POST['submitted'])) return false;
		if ($_POST['submitted'] == $this->getFormID()) return true;
	}
	
	private function getDropDownOptionElements($arrOptions, $strSelected='', $booFirstEmpty=true, $strFirstLabel=''){
		$strReturn ='';
		$strTmpSelected='';
		
		if ($booFirstEmpty){
			
			if (is_array($strSelected)){
				if ($strSelected[0] == '' && count($strSelected) > 1) unset($strSelected[0]);
				
				if (isset($strSelected[0])){
					if ($strSelected[0] == '') $strTmpSelected = 'selected';
				}
				
			} else {
				if ($strSelected == '') $strTmpSelected = 'selected';	
			}
			
			$strReturn .='<option value="" '.$strTmpSelected.'>'.$strFirstLabel.'</option>';
			$strTmpSelected='';
		}
		
		foreach ((array)$arrOptions as $strKey => $strValue){
			if ($strKey == $strSelected) $strTmpSelected = 'selected';
			if (is_array($strSelected)){
				foreach ((array)$strSelected as $intKey => $strSelID){
					if ($strKey == $strSelID) $strTmpSelected = 'selected';
				}
			}
			$strReturn .= '<option value="'.$strKey.'" '.$strTmpSelected.'>'.$strValue.'</option>';
			$strTmpSelected='';
		}
		return $strReturn;
	}	

	private function validateForm(){
		
		if ($this->booValidatorRunned) return $this->booValidatorResult;
		
		$this->booValidatorRunned = true;
		$booError = false;
		$arrForm = $this->arrForm[$this->getFormID()];
		
		foreach ((array) $arrForm['fields'] as $strName => $arrField){
			if (isset($_POST[$strName]) && !is_array($_POST[$strName])){
				if (trim($_POST[$strName]) == ''){
					if ($arrField['required']){
						$this->arrForm[$this->getFormID()]['fields'][$strName]['error'] = true;
						$this->arrForm[$this->getFormID()]['fields'][$strName]['errormsg'] = 'Pflichtfeld';
						$booError = true;
					}
				} else {
					// input detected
					// use validator method to check
					if ($arrField['validator'] != ''){
						if (!$this->validateField($_POST[$strName],$arrField['validator'], $arrField['label'])){
							$this->arrForm[$this->getFormID()]['fields'][$strName]['error'] = true;
							$this->arrForm[$this->getFormID()]['fields'][$strName]['errormsg'] = $this->getValidatorMessage();
							$booError = true;
						}						
					}
				}
			}
		}
		
		foreach ((array) $this->arrMatchTwoFields as $intKey => $arrMatchFields){
			$strLabelField1 = $arrForm['fields'][$arrMatchFields['f1']]['label'];
			$strLabelField2 = $arrForm['fields'][$arrMatchFields['f2']]['label'];
			if ($_POST[$arrMatchFields['f1']] != $_POST[$arrMatchFields['f2']]){
				$this->arrForm[$this->getFormID()]['fields'][$arrMatchFields['f2']]['error'] = true;
				$this->arrForm[$this->getFormID()]['fields'][$arrMatchFields['f2']]['errormsg'] = 'Passwörter stimmen nicht überein';
			}
		}

		if ($booError){
			$this->booValidatorResult = false;
			return false;
		} else {
			$this->booValidatorResult = true;
			return true;
		}
		
	}
	
	private function validateField($strUserInput, $strValidate, $strLabel){
		$booError = false;
		
		switch ($strValidate) {
			
			/* E-Mail */
			
		    case 'email':
		    	if ($this->validateEMail($strUserInput) == false){
		    		$booError = true;
		    		$this->setValidatorMessage('E-Mail Adresse ungültig');
		    	}
		        break;
		        
			/* Password */	

		    case 'password':
		    	if ($this->validateStringPassword($strUserInput) == false){
		    		$booError = true;
		    		$this->setValidatorMessage('mindestens 6 Zeichen');
		    	}
		        break;	

		    /* Misc */
		    
		    case 'number':
		    	if (is_numeric($strUserInput) == false){
		    		$booError = true;
		    		$this->setValidatorMessage('Keine Zahl');
		    	}
		        break;
		            
		    default:
		    	throw new Exception($strValidate .' is unknown validator');
		}

		if ($booError) return false;
		return true;
		
	}
	
	
	// Helpers /  Form Validators
	
	public function date_de2en($string){
		//12.11.2006 -> 2006-11-12
		$teile = explode('.',$string);
		return $teile[2].'-'.$teile[1].'-'.$teile[0];
	}	
	
	private function validateEMail($strEMail){
		if (eregi("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,8}))$",$strEMail)) return true;
		return false;
	}
	
	private function validateStringPassword($strPassword){
		if (strlen($strPassword) > 5) return true;
		return false;
	}
	
}