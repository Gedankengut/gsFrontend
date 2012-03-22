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

class FRONTEND_CONTROLLER_MYDATA extends FRONTEND_CONTROLLER {

	public function __construct(){
		parent::__construct();
	}
	
	public function indexAction(){
		
		$objDataCustomer = new GSALES_DATA_CUSTOMER();
		$objCustomer = $objDataCustomer->getCustomerById($this->objUserAuth->getCustomerId()); // read customer data
		$this->objSmarty->assignByRef('customer', $objCustomer); // assign to template
		
	}
	
	public function editAction(){
		
		$objDataCustomer = new GSALES_DATA_CUSTOMER();
		$objCustomer = $objDataCustomer->getCustomerById($this->objUserAuth->getCustomerId()); // read customer data
		$objCustomer->overriteProposalWithCurrentValues(); // load existent proposals
		
		$objForm = new HTMLFORM($this,'mydataedit'); // form for customer data edit
		$objForm->setAction('mydata/edit');
		$objForm->addField('Firma',  'input', 'company',  $objCustomer->getCompany(),  false, '', '', '');
		$objForm->addField('Vorname',  'input', 'firstname',  $objCustomer->getFirstname(),  true, '', '', '');
		$objForm->addField('Nachname',  'input', 'lastname',  $objCustomer->getLastname(),  true, '', '', '');
		$objForm->addField('Anschrift',  'input', 'address',  $objCustomer->getAddress(),  true, '', '', '');
		$objForm->addField('PLZ',  'input', 'zip',  $objCustomer->getZIP(),  true, '', '', '');
		$objForm->addField('Ort',  'input', 'city',  $objCustomer->getCity(),  true, '', '', '');
		$objForm->addField('Land',  'input', 'country',  $objCustomer->getCountry(),  false, '', '', '');
		$objForm->addField('Homepage',  'input', 'homepage',  $objCustomer->getHomepage(),  false, '', '', '');
		$objForm->addField('E-Mail',  'input', 'email',  $objCustomer->getEMail(),  true, '', '', '');
		$objForm->addField('Telefon',  'input', 'phone',  $objCustomer->getPhone(),  true, '', '', '');
		$objForm->addField('Fax',  'input', 'fax',  $objCustomer->getFax(),  false, '', '', '');
		$objForm->addField('Kontonummer',  'input', 'bank_account_no',  $objCustomer->getBankAccountNo(),  false, '', '', '');
		$objForm->addField('BLZ',  'input', 'bank_code',  $objCustomer->getBankCode(),  false, '', '', '');
		$objForm->addField('Inhaber',  'input', 'bank_account_owner',  $objCustomer->getBankAccountOwner(),  false, '', '', '');
		$objForm->addField('IBAN',  'input', 'bank_iban',  $objCustomer->geBankIBAN(),  false, '', '', '');
		$objForm->addField('BIC',  'input', 'bank_bic',  $objCustomer->getBankBIC(),  false, '', '', '');
		$objForm->addSubmitButton('Änderungen speichern'); // save button
		$objForm->output();				
		
		if ($objForm->isSubmittedAndValid()){
			
			$arrData = $_POST;
			unset($arrData['submitted']); // unset "garbage" of HTMLFORM Class
			unset($arrData['submitButton']); 
			$arrResult = $objDataCustomer->updateCustomerProposal($this->objUserAuth->getCustomerId(), $arrData);
			
			if (false != $arrResult->getProposedChanges()){
				$this->setMessage('Wir haben Ihre Änderungen erhalten und werden diese nach einer Überprüfung endgültig in unser System aufnehmen');	
				$this->redirectTo('mydata','index');
			}
			
		} else {
			
			if (false != $objCustomer->getProposedChanges()){
				$this->setMessage('Ihre zuvor übermittelten Änderungen wurden noch nicht endgültig ins unser System übernommen', 'error');
			}
			
		}
	}
	
	public function passwordAction(){
		
		$objForm = new HTMLFORM($this,'editpass');
		$objForm->setAction('mydata/password');
		$objForm->setConfirmField('password1', 'password2');
		$objForm->addField('Neues Passwort',  'password', 'password1',  '',  true, 'password', '', '');
		$objForm->addField('Neues Passwort bestätigen',  'password', 'password2',  '',  true, '', '', '');
		$objForm->addSubmitButton('Passwort ändern');
		$objForm->output();
		
		if ($objForm->isSubmittedAndValid()){
			
			$objDataCustomer = new GSALES_DATA_CUSTOMER();
			$booResult = $objDataCustomer->saveUpdatedFrontendPassword($this->objUserAuth->getCustomerId(), $_POST['password1']);
			
			if (false == $booResult){
				$this->setMessage('Das neue Passwort konnte nicht gespeichert werden', 'error');
				return false;
			}
			
			$this->setMessage('Das neue Passwort wurde erfolgreich gespeichert');
			$this->redirectTo('mydata','index');
			
		}
	}

}