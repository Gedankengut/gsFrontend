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
<h1>Kundendaten</h1>

<table>
	<tr>
		<td><span class="label">Kunden-Nr.</span></td>
		<td>{$customer->getCustomerNo()}</td>
	</tr>
	<tr class="altRow">
		<td><span class="label">Passwort</span></td>
		<td><a href="{felink controller="mydata" action="password"}" class="button">Passwort ändern</a></td>
	</tr>
	<tr>
		<td><span class="label">Firma</span></td>
		<td>{$customer->getCompany()}</td>
	</tr>
	<tr class="altRow">
		<td><span class="label">Vor-, Nachname</span></td>
		<td>{$customer->getFirstname()} {$customer->getLastname()}</td>
	</tr>
	<tr>
		<td><span class="label">Anschrift</span></td>
		<td>{$customer->getAddress()}</td>
	</tr>
	<tr class="altRow">
		<td><span class="label">PLZ, Ort</span></td>
		<td>{$customer->getZIP()} {$customer->getCity()}</td>
	</tr>
	<tr>
		<td><span class="label">Land</span></td>
		<td>{$customer->getCountry()}</td>
	</tr>
	<tr class="altRow">
		<td><span class="label">Homepage</span></td>
		<td>{$customer->getHomepage()}</td>
	</tr>
</table>

<p><a href="{felink controller="mydata" action="edit"}" class="button">Daten bearbeiten</a></p>