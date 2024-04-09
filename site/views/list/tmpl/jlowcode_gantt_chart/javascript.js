/**
 * List helper
 *
 * @copyright: Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license  : GNU/GPL http                         :                              //www.gnu.org/copyleft/gpl.html
 */

requirejs(['fab/fabrik', 'fab/bootstrap_tree'], function (Fabrik, BootstrapTree) {

	jQuery(document).ready(function () {
		// console.log('debug: bootstrap tree init called');
		BootstrapTree.init(Fabrik.liveSite);
		Fabrik.addEvent('fabrik.list.update', function (list) {
			// console.log('debug: event triggered update tree');
			BootstrapTree.init(Fabrik.liveSite);
			// console.log(list);
			let lists = document.querySelectorAll('.fabrik___rowlink')

			for(let i = 0; i <= lists.length - 1; i++){
			if(!lists[i].hasClass('btn')){
				lists[i].href = lists[i].href.replace('form', 'details');
			}
		}
			return list;
		});

		let list = document.querySelectorAll('.fabrik___rowlink')

		for(let i = 0; i <= list.length - 1; i++){
			if(!list[i].hasClass('btn')){
				list[i].href = list[i].href.replace('form', 'details');
			}
		}
	});

	Fabrik.addEvent('fabrik.list.loaded', function (list) {
		var dataRow = list.list.getElementsByClassName('fabrik_row');
		
		Array.from(dataRow).each(function (row) {
			var btnAction = row.getElementsByClassName('fabrik_action');
			if (btnAction[0]) {
				btnAction[0].addEventListener("mouseover", function () {
					btnAction[0].addClass('open');
				});
			}
		})
	});
});

window.addEvent('fabrik.loaded', function () {

	// Search icon start
	// Search icon on filters
	const searchIcon = ' <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/><path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/></svg>';
	let inputs = jQuery("input.fabrik_filter[type='text']");
	for (let value of inputs) {
		jQuery(value).parent().css({ "position": "relative" });
		jQuery(value).after('<div style="position: absolute; right: 15px; top: 20px;" class="search-icon-button">' + searchIcon + '</div>');
	}
	jQuery('.search-icon-button').on('click', function (event) {
		Fabrik.fireEvent('fabrik.list.dofilter', [this]);
	});
	//  Search icon on general search
	var searchBox = jQuery('.fabrik_filter.search-query');
	var searchButton = jQuery('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/><path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/></svg>');
	searchBox.parent().css({
		'position': 'relative'
	});
	searchBox.parent().append(searchButton);
	searchButton.css({
		'position': 'absolute',
		'top': '10px',
		'right': '10px',
		'cursor': 'pointer',
		'font-size': '18px',
		'z-index': '10',
	});
	searchButton.on('click', function () {
		Fabrik.fireEvent('fabrik.list.dofilter', [this]);
	});
	// Search icon  END

	var dataRow = $('.fabrik_groupdata');

	Array.from(dataRow).each(function (row) {
		var btnAction = row.getElementsByClassName('fabrik_action');

		Array.from(btnAction).each(function (btn) {
			if (btn) {
				btn.addEventListener("mouseover", function () {
					btn.addClass('open');
				});
			}
		})
	})

	jQuery('tbody.fabrik_groupdata').each(function() {
		var name_list = this.classList[1];
		jQuery(this.parentNode).find(".fabrik_ordercell").each(function(index) {
			var i = 0;
			var linhas = jQuery(this).closest('table').find('.' + name_list + ' .fabrik_row');

			while (i < (linhas.length)) {
				linhas[i].children[index].setAttribute('data-content', this.outerText);
				i++;
			}
		});
	});
})