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
<form method="post" action="{$form.$fid.action}" id="{$fid}" name="{$fid}" class="inputform">
	<table>
	{foreach $form.$fid.fields as $k=>$f}
		{if $f.type != "submitButton" && $f.type != "linkButton" && $f.type != "hidden"}
			<tr {if $f@iteration%2 == 0}class="altRow"{/if}>
				<td>
					{if $f.label}
						<label for="{$f.elementid}"{if $f.error} class="errorlabel"{/if}>{$f.label}{if $f.required}&nbsp;*{/if}</label>
					{else}
						&nbsp;
					{/if}
				</td>
				<td>
					{$f.html}
					{if $f.error && $f.errormsg != ""}<span class="errormessage">{$f.errormsg}</span>{/if}
				</td>
			</tr>
		{/if}
	{/foreach}
	{if $f@show}
	<tr {if $f@total%2 == 0}class="altRow"{/if}>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="submitted" value="{$fid}" />
			{foreach key=k item=f from=$form.$fid.fields name=form}
				{if $f.type == "submitButton" || $f.type == "linkButton" || $f.type == "hidden"}{$f.html}{/if}
			{/foreach}	
		</td>
	</tr>
	{/if}
	</table>
</form>