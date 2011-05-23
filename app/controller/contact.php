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

class FRONTEND_CONTROLLER_CONTACT extends FRONTEND_CONTROLLER {

	public function __construct(){
		parent::__construct();
	}
	
	public function indexAction(){
		
		$objForm = new HTMLFORM($this,'contact');
		$objForm->setAction('contact/index');
		$objForm->addField('Anliegen',  'input', 'subject',  '',  true, '', '', '');
		$objForm->addField('Nachricht',  'textarea', 'message',  '',  true, '', '', '');
		$objForm->addSubmitButton('Nachricht übermitteln');
		$objForm->output();

		if ($objForm->isSubmittedAndValid()){
			
			$objDataCustomer = new GSALES_DATA_CUSTOMER();
			$objCustomer = $objDataCustomer->getCustomerById($this->objUserAuth->getCustomerId());
			
			$objMailer = new FRONTEND_MAILER();
			$objMailer->FromName =  trim($objCustomer->getFirstname() . ' ' . $objCustomer->getLastname());
			$objMailer->From = $objCustomer->getEmail();
			$objMailer->AddReplyTo($objCustomer->getEmail(), trim($objCustomer->getFirstname() . ' ' . $objCustomer->getLastname()));
			$objMailer->Subject = 'Kundenfrontend "'.$_POST['subject'].'"';
			$objMailer->Body = $_POST['message'];
			$objMailer->AddAddress(MAIL_TO);
			$booCheck = $objMailer->Send();
			
			if ($booCheck){
				$this->setMessage('Nachricht wurde erfolgreich verschickt');
				$this->redirectTo('contact','index');
			} else {
				$this->setMessage($objMailer->ErrorInfo,'error');
			}
			
		}
		
	}
	
}