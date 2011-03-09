<?php

function smarty_function_felink($params, $template)
{
	global $objController;
	
	$strController = $params['controller'];
	$strAction = $params['action'];
	
	if ($strController == '') $strController='index';
	if ($strAction == '') $strAction='index';
	
	$strOwnPath =  $objController->detectOwnPath();
	return $objController->strFrontendPath.$strController.'/'.$strAction;
}
