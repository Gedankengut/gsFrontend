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

require_once('app/cfg.php');
require_once('app/lib/dispatcher.php');

try {
	
	$objDispatcher = new FRONTEND_DISPATCHER();
	$objController = $objDispatcher->getControllerInstanceByUserRequest();
	$objController->renderOutput();
	
} catch (Exception $e){
	
	// display exception template to user
	require_once(FE_DIR.'/lib/Smarty-3.0.5/libs/Smarty.class.php');
	$objSmarty = new Smarty();
	$objSmarty->compile_check = true;
	$objSmarty->force_compile=true;
	$objSmarty->debugging = false;
	$objSmarty->template_dir = FE_DIR.'/templates/';
	$objSmarty->compile_dir = FE_DIR.'/templates_c/';
	$objSmarty->assign('exception_message', $e->getMessage());
	$objSmarty->display('exception.tpl');
	
	// log exception frontend.log
	error_log($e->getMessage());
	
}