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

require_once(FE_DIR.'/lib/PHPMailer_v5.1/class.phpmailer.php');

class FRONTEND_MAILER extends PHPMailer{

	public function __construct(){
		
		$this->IsSMTP();
		if (MAIL_TYPE == 'phpmail') $this->IsMail();
		if (MAIL_TYPE == 'qmail') $this->IsQmail();
		if (MAIL_TYPE == 'sendmail') $this->IsSendmail();
		
		$this->IsHTML(false);
		$this->SMTPAuth   = MAIL_AUTH;
		$this->CharSet    = 'UTF-8';
		$this->Host       = MAIL_HOSTNAME;
		
		if (MAIL_AUTH){
			$this->Username   = MAIL_USERNAME;
			$this->Password   = MAIL_PASSWORD;
		}
		
	}
	
	public function useTemplateForSubjectAndBody($strTemplateFile, $arrSmartyVars){
		
		// init smarty
		$objSmarty = new Smarty();
		$objSmarty->compile_check = true;
		$objSmarty->force_compile = true;
		$objSmarty->template_dir = FE_DIR.'/templates/emails/';
		$objSmarty->compile_dir = FE_DIR.'/templates_c/';
		
		$objSmarty->assign('data', $arrSmartyVars);
		$arrParts = explode('[MAIL-NEXT-PART]',$objSmarty->fetch($strTemplateFile));

		$this->Subject = trim($arrParts[0]);
		$this->Body = trim($arrParts[1]);
	}
	
}