/**
 * List helper
 *
 * @copyright: Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license  : GNU/GPL http                         :                              //www.gnu.org/copyleft/gpl.html
 */

requirejs(['fab/fabrik', 'fab/bootstrap_tree'], function (Fabrik, BootstrapTree) {

	jQuery(document).ready(function () {

		Fabrik.addEvent('fabrik.list.update', function (list) {
			return list;
		});

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
	const searchIcon = ' <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/><path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/></svg>';
	let inputs = jQuery("input.fabrik_filter[type='text']");
	for (let value of inputs) {
		jQuery(value).parent().css({ "position": "relative" });
		jQuery(value).after('<div style="position: absolute; left: 17px; top: 11px;" class="bi-search">' + searchIcon + '</div>');
	}
	jQuery('.bi-search').on('click', function (event) {
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
		'top': '15px',
		'left': '30px',
		'cursor': 'pointer',
		'font-size': '18px',
		'z-index': '10',
		'color': '#A6A6A6'
	});
	searchButton.on('click', function () {
		Fabrik.fireEvent('fabrik.list.dofilter', [this]);
	});
	// Search icon  END

	// Clean filter
	var cleanFilterButton = jQuery('<button type="button" class="btn-close"></button>');
	searchBox.parent().css({
		'position': 'relative'
	});
	searchBox.parent().append(cleanFilterButton);
	cleanFilterButton.css({
		'position': 'absolute',
		'top': '18px',
		'right': '30px',
		'cursor': 'pointer',
		'font-size': '10px',
		'z-index': '10',
		'color': '#A6A6A6'
	});
	cleanFilterButton.on('click', function () {
		jQuery(this).parent().find('.fabrik_filter').val('');
		Fabrik.fireEvent('fabrik.list.dofilter', [this]);
	});
	// Clean filter - END

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
	if (element.id === "list-view") {
		sessionStorage.setItem("modo", "list");
		enviarDadosParaServidor();
	} else if (element.id === "grid-view") {
		sessionStorage.setItem("modo", "grid");
		enviarDadosParaServidor();
	} else if (element.id === "tree-view") {
		sessionStorage.setItem("modo", "tree");
		enviarDadosParaServidor();
	}
};

function carregarModoEscolhido() {
	var modoExibicao = sessionStorage.getItem("modo");
	if (modoExibicao == "list") {
		document.getElementById("list-view").checked = true;
	} else if (modoExibicao == "grid") {
		document.getElementById("grid-view").checked = true;
	} else if (modoExibicao == "tree") {
		document.getElementById("tree-view").checked = true;
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

			// Itera sobre os filhos e os adiciona ao DOM
			data.forEach(filho => {
				const itemFilho = document.createElement('div');
				itemFilho.classList.add('tree-item');

				// Adiciona os elementos ao item filho			
				const action = document.createElement('span');
				action.innerHTML = filho.actions;

				itemFilho.appendChild(action);
				elementoPai.appendChild(itemFilho);
			});
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


document.addEventListener("DOMContentLoaded", function () {
	// Certifique-se de que o Sortable está disponível
	if (typeof Sortable !== 'undefined') {
		const table = document.getElementById('list_'+jQuery('[name=listid]').val()+'_com_fabrik_'+jQuery('[name=listid]').val());
		if (table) {
			// Seleciona o thead para tornar as colunas ordenáveis
			const thead = table.querySelector('thead tr');
			// Aplica o SortableJS ao cabeçalho
			Sortable.create(thead, {
				animation: 150,
				onEnd: function (evt) {
					const oldIndex = evt.oldIndex;
					const newIndex = evt.newIndex;
					// Reordena as células no tbody
					table.querySelectorAll('tbody tr').forEach(function (row) {
						const cells = Array.from(row.children);
						const movedCell = cells.splice(oldIndex, 1)[0];
						cells.splice(newIndex, 0, movedCell);
						// Atualiza a ordem das células
						row.innerHTML = '';
						cells.forEach(function (cell) {
							row.appendChild(cell);
						});
					});
				}
			});
		}
	}
});