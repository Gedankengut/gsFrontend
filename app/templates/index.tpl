{*
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
 *}
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="INDEX,FOLLOW" />
	<title>{$smarty.const.FRONTEND_TITLE}</title>
	<link rel="stylesheet" type="text/css" href="{$fepath}public/css/screen.css" />
	<script type="text/javascript" src="{$fepath}public/js/jquery-1.5.min.js"></script>
	<script type="text/javascript" src="{$fepath}public/js/jquery.sortable.js"></script>
	<script type="text/javascript" src="{$fepath}public/js/jquery.pagination.js"></script>
	<script type="text/javascript" src="{$fepath}public/js/jquery.metadata.js"></script>
	<script type="text/javascript" src="{$fepath}public/js/gsalesFrontend.js"></script>
</head>
<body>
	<div id="loading">Bitte warten…</div>
	<div class="pageWrap">
		<div class="pageHeader">
			{$smarty.const.FRONTEND_TITLE}
		</div>
		<div class="navigation">
			<ul class="clearfix">
				{if $isuser}
					<li><a href="{felink controller="mydata" action="index"}"{if $controller == 'mydata'} class="active"{/if}>Kundendaten</a></li>
					<li><a href="{felink controller="invoice" action="index"}"{if $controller == 'invoice'} class="active"{/if}>Rechnungen</a></li>
					<li><a href="{felink controller="refund" action="index"}"{if $controller == 'refund'} class="active"{/if}>Gutschriften</a></li>
					<li><a href="{felink controller="contract" action="index"}"{if $controller == 'contract'} class="active"{/if}>Positionen</a></li>
					<li><a href="{felink controller="document" action="index"}"{if $controller == 'document'} class="active"{/if}>Dokumente</a></li>
					<li><a href="{felink controller="contact" action="index"}"{if $controller == 'contact'} class="active"{/if}>Kontakt</a></li>
					<li><a href="{felink controller="logout" action="index"}" class="highlight">Logout</a></li>
				{else}
					<li><a href="{felink controller="index" action="index"}"{if $controller == 'index'} class="active"{/if}>Login</a></li>		
				{/if}
			</ul>
		</div>
		<div class="pageContent">
			{if $messages}
				{foreach $messages as $k=>$m}
					<div class="message {$m.type}">{$m.message}</div>
				{/foreach}
			{/if}
			{include file="$template"}
		</div>
		<div class="pageFooter clearfix">
			Diese Seite basiert auf dem <a href="http://www.gsales.de" target="_blank">g*Sales</a> Kundenfrontend. <a href="{$smarty.const.LINK_IMPRINT}" target="_blank" class="floatR">Impressum</a>
		</div>
	</div>
</body>
</html>