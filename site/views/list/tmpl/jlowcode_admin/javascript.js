/**
 * List helper
 *
 * @copyright: Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license  : GNU/GPL http                         :                              //www.gnu.org/copyleft/gpl.html
 */

requirejs(['fab/fabrik', 'fab/bootstrap_tree'], function (Fabrik, BootstrapTree) {
	jQuery(document).ready(function () {
		var tree = jQuery('.summary')[0];

		hideHeadings();
		setFiltersTutorialTemplate();
		orderingTreeTutorial(tree);
		setToogleFilters();
		
		Fabrik.addEvent('fabrik.list.update', function (list) {
			hideHeadings();

			return list;
		});
	});

	Fabrik.addEvent('fabrik.list.loaded', function (list) {
		hideHeadings();

		var dataRow = list.list.getElementsByClassName('fabrik_row');
		Array.from(dataRow).each(function (row) {
			var btnAction = row.getElementsByClassName('fabrik_action');
			if (btnAction[0]) {
				btnAction[0].addEventListener("mouseover", function () {
					btnAction[0].addClass('open');
				});
			}
		});
	});

	Fabrik.addEvent('fabrik.list.submit.ajax.complete', function (list) {
		jQuery('#nav-pagination').val(Math.ceil((parseInt(list.options.limitStart)+1)/parseInt(list.options.limitLength)));
	})

	Fabrik.addEvent('fabrik.list.submit.ajax.complete', function (t, j) {
		if(j.filters.value === undefined) {
			jQuery('.clearFilters').addClass('fabrikHide');
			jQuery('.fabrik-list .fabrikButtonsContainer .fabrik_filter').removeClass('p-clean-filters');
			return;
		}

		qtnFilters = Object.keys(j.filters.value).length;
		jQuery('.toggleFilters .num-flag').html(qtnFilters);
		jQuery('.toggleFilters .num-flag').removeClass('fabrikHide');
		jQuery('.clearFilters').removeClass('fabrikHide');
		jQuery('.fabrik-list .fabrikButtonsContainer .fabrik_filter').addClass('p-clean-filters');
	});

	jQuery('.clearFilters').on('click', function() {
		jQuery('.toggleFilters .num-flag').addClass('fabrikHide');
	});
});

window.addEvent('fabrik.loaded', function () {
	setEventsNavigation();

	// Description container
	var toogleBtn = jQuery('.intro-container .fa');
	var textContent = jQuery('.text-intro-content');

	if(toogleBtn.length > 0 && textContent.length > 0) {
		height = textContent[0].scrollHeight;
		maxHeight = parseInt(textContent.css('max-height'));
		if(height < maxHeight) {
			toogleBtn.css('display', 'none');
		}

		toogleBtn.on('click', function() {
			if (textContent.css('max-height') !== 'none') {
				textContent.css('max-height', 'none');
				jQuery(this).removeClass('fa-angle-down');
				jQuery(this).addClass('fa-angle-up');
			} else {
				textContent.css('max-height', '8.5em');
				jQuery(this).removeClass('fa-angle-up');
				jQuery(this).addClass('fa-angle-down');
			}
		})
	}

	// Search icon start
	// Search icon on filters
	const searchIcon = '<i class="fa-solid fa-magnifying-glass"></i>';
	let inputs = jQuery("input.fabrik_filter[type='text']");
	for (let value of inputs) {
		jQuery(value).parent().css({ "position": "relative" });
		jQuery(value).after('<div style="position: absolute; left: 17px; top: 10px;" class="bi-search">' + searchIcon + '</div>');
	}
	jQuery('.bi-search').on('click', function (event) {
		Fabrik.fireEvent('fabrik.list.dofilter', [this]);
	});

	// Begin - Search icon on general search
	var searchButton = jQuery('.search-all');
	searchButton.on('click', function () {
		Fabrik.fireEvent('fabrik.list.dofilter', [this]);
	});
	// End - Search icon on general search

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
})


function handleRadioClick(element) {
	showSpinner();

	switch (element.id) {
		case 'list-view':
			sessionStorage.setItem("modo", "list");			
			break;

		case 'grid-view':
			sessionStorage.setItem("modo", "grid");		
			break;

		case 'tree-view':
			sessionStorage.setItem("modo", "tree");			
			break;

		case 'tutorial-view':
			sessionStorage.setItem("modo", "tutorial");			
			break;
	}

	enviarDadosParaServidor();
};

function carregarModoEscolhido() {
	var modoExibicao = sessionStorage.getItem("modo");

	switch (modoExibicao) {
		case 'list':
			document.getElementById("list-view").checked = true;		
			break;

		case 'grid':
			document.getElementById("grid-view").checked = true;	
			break;

		case 'tree':
			document.getElementById("tree-view").checked = true;		
			break;

		case 'tutorial':
			document.getElementById("tutorial-view").checked = true;		
			break;
	}
}

function enviarDadosParaServidor() {
	const modoExibicao = sessionStorage.getItem("modo"); // Obtém o valor do sessionStorage
	var data = {};
	data['modo'] = modoExibicao;

	// Envia o valor via POST para o servidor
	var url = window.location.origin + "/index.php?option=com_fabrik&view=list&listid=" + jQuery('[name=listid]').val();
	jQuery.ajax({
		url: url,
		method: 'post',
		data: data,
	}).done(function (r) {
		window.location.reload();
	});
}

// Carrega o modo escolhido quando a página é carregada
document.addEventListener("DOMContentLoaded", carregarModoEscolhido);

function carregarFilhos(paiId, elementoPai) {
	showSpinner();
	fetch(window.location.origin + "/index.php?option=com_fabrik&view=list&listid=" + jQuery('[name=listid]').val(), {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: new URLSearchParams({ action: 'getFilhos', id: paiId }) // Envia a ação e o ID do pai
	})
		.then(response => response.json())
		.then(data => {

			if (data.length === 0) {
				if (!elementoPai.querySelector('.no-children')) {
					const mensagem = document.createElement('div');
					mensagem.classList.add('no-children');
					mensagem.textContent = 'Não há filhos para este item.';
					elementoPai.appendChild(mensagem);
				}
				hideSpinner();
				return;
			}

			// Verifica se já carregou os filhos
			if (elementoPai.classList.contains('open')) {
				elementoPai.classList.remove('open');
				elementoPai.querySelectorAll('.tree-item').forEach(el => el.remove());
				hideSpinner();
				return;
			}

			elementoPai.classList.add('open');
			var childrenNode = document.createElement('div');
			childrenNode.classList.add('children-node');
			childrenNode.setAttribute('data-parent', elementoPai.getAttribute('data-id'));

			// Itera sobre os filhos e os adiciona ao DOM
			data.forEach(filho => {
				const itemFilho = document.createElement('div');
				itemFilho.classList.add('tree-item');
				itemFilho.setAttribute('data-id', filho.id);

				// Adiciona os elementos ao item filho			
				const action = document.createElement('span');
				action.innerHTML = filho.actions;

				itemFilho.appendChild(action);
				childrenNode.appendChild(itemFilho);
			});
			elementoPai.appendChild(childrenNode);

			setFiltersTutorialTemplate();
			if(jQuery(elementoPai).closest('#summary-tutorial').length > 0) {
				orderingTreeTutorial(childrenNode);
			}
			hideSpinner();
		})
		.catch(error => {
			console.error('Erro ao carregar filhos:', error);
			hideSpinner();
		});
}

function showSpinner() {
	document.getElementById("loadingModal").style.display = 'flex';
}

function hideSpinner() {
	document.getElementById('loadingModal').style.display = 'none';
}

function onReportAbuse(listRowIds) {
	showSpinner()
	var options = {};
	options.user = {};
	options.user.approve_for_own_records = window.workflowInstance.options.user.approve_for_own_records;
	options.workflow_owner_element = window.workflowInstance.options.workflow_owner_element;
	options.allow_review_request = window.workflowInstance.options.allow_review_request;

	jQuery.ajax({
		'url': '',
		'method': 'get',
		'data': {
			'options': options,
			'listRowIds': listRowIds.attributes[5].value,
			'option': 'com_fabrik',
			'task': 'plugin.pluginAjax',
			'plugin': 'workflow',
			'method': 'onReportAbuse',
			'g': 'form',
		},
		success: function (data) {
			alert("Sucesso!");
			hideSpinner();
			location.reload();
		},
		error: function (err) {
			alert("Erro ao reportar abuso.");
			hideSpinner();
		}
	});
}

function hideHeadings() {
	jQuery('.fabrikList .fabrik___heading th').each(function (i, column) {
		classes = jQuery(column).attr('class').split(' ');
		if(classes.indexOf('fabrik_actions') < 0) {
			jQuery(column).css('visibility', 'hidden');
		}
	});
}

function setFiltersTutorialTemplate() {
	var nodesTree = jQuery('.tree-text');

	nodesTree.each(function() {
		var nodeTree = jQuery(this);

		nodeTree.on('click', function() {
			var nodeTree = jQuery(this);
			var treeItem = nodeTree.closest('.tree-item');
			var id = treeItem.data('id');
			var url = jQuery('form').prop('action');

			renderTutorial(url, id);
		});
	});
}

/**
 * Render html to tutorial
 */
function renderTutorial(url, id) {
	var listRef = jQuery('input[name="listref"]').val();
	var itemId = jQuery('input[name="Itemid"]').val();
	var incFilters = jQuery('input[name="incfilters"]').val();
	var listId = jQuery('input[name="listid"]').val();

	jQuery.ajax({
		url: url,
		method: 'post',
		data: {
			'listRowIds': id,
			'option': 'com_fabrik',
			'task': 'list.filter',
			'tmpl': 'jlowcode_admin',
			'render': 'page_tutorial',
			'format': 'raw',
			'view': 'list',
			'listref': listRef,
			'itemid': itemId,
			'incfilters': incFilters,
			'listid': listId
		},
	}).done(function (r) {
		r = JSON.parse(r);

		jQuery('.ajax-filters').replaceWith(r['html']);
	});
}

/**
 * Make tree sortable to order
 */
function orderingTreeTutorial(tree) {
	//Make sortable
	if (typeof Sortable !== 'undefined' && tree !== undefined) {
		Sortable.create(tree, {
			animation: 150,
			filter: '.not-draggable',
			onEnd: function(e) {
				var oldIndex = e.oldIndex;
				var newIndex = e.newIndex;
				var row = jQuery(e.from).children('.tree-item')[e.newIndex-1];
				var id = jQuery(row).data('id');
				var value = (newIndex == 1 && !jQuery(this.el).hasClass('children-node')) || newIndex == 0 ? -1 : id;
				var refParentId = jQuery(e.item).closest('.children-node').data('parent');
				var rowId = jQuery(e.item).data('id');

				var itemId = jQuery('input[name="Itemid"]').val();
				var incFilters = jQuery('input[name="incfilters"]').val();
				var listId = jQuery('input[name="listid"]').val();

				refParentId = refParentId == rowId ? null : refParentId;
				if(oldIndex == newIndex) return;

				jQuery.ajax({
					url: '',
					method: 'post',
					data: {
						option: 'com_fabrik',
						format: 'raw',
						task: 'plugin.pluginAjax',
						g: 'element',
						plugin: 'ordering',
						method: 'makeOrdering',
						value: value,
						listId: listId,
						refParentId: refParentId,
						rowId: rowId,
						incfilters: incFilters,
						itemId: itemId
					}
				}).done(function(response) {
					renderTutorial(0, jQuery('form').prop('action'));
				});
			}
		})
	}
}

function setToogleFilters() {
	jQuery(".toggleFilters").on('click', function() {
		jQuery(".chosen-done").each(function(index, element) {
			jQuery(element).chosen("destroy");
		});

		Fabrik.buildChosen('select.advancedSelect', {
			"placeholder_text_multiple": Joomla.JText._('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS'),
			"placeholder_text_single": Joomla.JText._('JGLOBAL_SELECT_AN_OPTION'),
			"no_results_text": Joomla.JText._('JGLOBAL_SELECT_NO_RESULTS_MATCH')
		});
	});
}

/**
 * Functions to new pagination
 * 
 */
setInterval(() => {
	setEventsNavigation();
}, 1000);


function setEventsNavigation() {
	jQuery("#go-page").off('click').on("click", function (e) {
		e.preventDefault();
		e.stopPropagation();
		navigation();
	});

	jQuery("#nav-pagination").off('blur').on("blur", function (e) {
		e.preventDefault();
		e.stopPropagation();
		navigation();
	});

	jQuery("#nav-pagination").off('keypress').on('keypress', function(e) {
		if (e.which === 13) {
			e.preventDefault();
			e.stopPropagation();
			navigation();
		}
	});
}

function navigation() {
	let urlInput = jQuery('input[name="nav-pagination-url"]');
	let paginationInput = jQuery('input[name="nav-pagination"]');
	let limitInput = jQuery('input[name="nav-pagination-limit"]');
	let resultsPerPage = jQuery('input[name="nav-pagination-results-per-page"]');

	if (urlInput.length && paginationInput.length && limitInput.length && resultsPerPage) {
		let limit = limitInput.val();

		if (parseInt(paginationInput.val()) > parseInt(limit)) {
			paginationInput.val(limit);
		}

		let finalUrl = urlInput.val().slice(0, -2) + (parseInt(paginationInput.val()) - 1) * parseInt(resultsPerPage.val());
		history.pushState(null, '', window.location.pathname + '?' + finalUrl + '&resetfilters=0&clearordering=0&clearfilters=0');
		location.reload();
	}
}