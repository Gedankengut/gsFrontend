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
<h1>Rechnung {$invoice->getInvoiceNo()} bezahlen</h1>

<table class="half">
	<tr>
		<td><strong>Datum:</strong></td>
		<td> {$invoice->getCreated()|date_format:"%d.%m.%Y"}</td>
	</tr>
	<tr class="altRow">
		<td><strong>Zahlungsziel:</strong></td>
		<td>{$invoice->getPayable()|date_format:"%d.%m.%Y"}</td>
	</tr>
	<tr>
		<td><strong>Nummer:</strong></td>
		<td>{$invoice->getInvoiceNo()}</td>
	</tr>
	{if $invoice->getDunningFee()}
	<tr class="altRow">
		<td><strong>Rechnungsbetrag:</strong></td>
		<td>{$invoice->getFormatedRoundedAmountWithSymbol()}</td>
	</tr>
	<tr>
		<td><strong>zzgl. Mahngebühren:</strong></td>
		<td>{$invoice->getFormatedDunningFeeWithSymbol()}</td>
	</tr>
	{/if}
</table>

<h2>offener Gesamtbetrag: {$invoice->getFormatedRoundOpenAmountWithSymbol()}</h2>

{if $payment_paypal || $payment_sofort}
	<p><br /><strong>Bitte wählen Sie eine Zahlungsart:</strong></p>
	<p>
		{if $payment_paypal}<a href="{felink controller="invoice" action="paypaypal"}/{$invoice->getId()}"><img src="{$fepath}public/img/paymentPayPal.gif" alt="PayPal" /></a>{/if}
		{if $payment_paypal && $payment_sofort}&nbsp;&nbsp;{/if}
		{if $payment_sofort}<a href="{felink controller="invoice" action="paysofort"}/{$invoice->getId()}"><img src="{$fepath}public/img/paymentSofort.gif" alt="sofortueberweisung.de" /></a>{/if}
	</p>
	{if $payment_sofort}
		<p>
			<em>Bitte prüfen Sie vor der Bezahlung per sofortueberweisung.de ob Ihre Bank unterstützt wird.</em><br />
			<a href="https://www.payment-network.com/sue_de/kaeuferbereich/bankensuche" target="_blank">Jetzt überprüfen</a>
		</p>
	{/if}
{else}
	<p>Momentan steht leider keine Online-Zahlungsmöglichkeit zur Verfügung.<br />Sie können die Rechnung jedoch mittels Überweisung bezahlen.</p>
{/if}

<p><br /><br /><a href="{felink controller="invoice" action="index"}" class="button">zurück zur Übersicht</a></p>