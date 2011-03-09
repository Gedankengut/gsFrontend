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
$(document).ready(function(){
	if($('#sortable').index() != -1){
		// sortable table config
		$.tablesorter.defaults.widthFixed = true;
		$.tablesorter.defaults.sortList = [[0,1]];
		$.tablesorter.defaults.widgets = ['zebra'];
		// parse german dates
		$.tablesorter.addParser({
			id: 'deDate',
			is: function(s){ return false; },
			format: function(s){ s = s.replace(/(\d{1,2})[\.](\d{1,2})[\.](\d{2,4})/, '$3/$2/$1'); if(s.indexOf()!=-1) s = '0000/00/00'; return $.tablesorter.formatFloat(new Date(s).getTime()); },
			type: 'numeric'
		});
		// parse strings starting with int
		$.tablesorter.addParser({
			id: 'int',
			is: function(s){ return false; },
			format: function(s){ s = s.split(' '); return $.tablesorter.formatFloat(s[0]); },
			type: 'numeric'
		});
		// parse custom currency
		$.tablesorter.addParser({
			id: 'deCurrency',
			is: function(s){ return false; },
			format: function(s){ s = s.replace('.','').split(' '); return $.tablesorter.formatFloat(s[0]); },
			type: 'numeric'
		});
		// sortable table with pagination
		$('#sortable').tablesorter().tablesorterPager({container: $('#pagination')});
		// assign sort events
		$('#sortable').bind('sortStart',function(){
			$('#loading').show();
		}).bind('sortEnd',function(){
			$('#loading').hide();
		});		
	}
});
// close popup on escape
$(document).keypress(function(e){
	if(e.keyCode == 27){
		$('.documentPositions').hide();
	}
});