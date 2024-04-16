/**
 * List helper
 *
 * @copyright: Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

requirejs(['fab/fabrik'], function () {
	var mq = window.matchMedia("(max-width: 700px)");
	if (mq.matches) {
		var url = window.location.href;
		if (url.match(/layout=/)) {
			url.replace("bootstrap", "div");
		} else {
			url += '?layout=div';
		}
		location.replace(url);
	}

	mq.addEventListener('change', function () {
		var url = window.location.href;
		if (url.match(/layout=/)) {
			url.replace("bootstrap", "div");
		} else {
			url += '?layout=div';
		}
		location.replace(url);
	});

	Fabrik.addEvent('fabrik.list.loaded', function (list) {
		var dataRow = list.list.getElementsByClassName('fabrik_row');

		Array.from(dataRow).each(function (row) {
			var btnAction = row.getElementsByClassName('fabrik_action');
			if (btnAction[0]) {
				btnAction[0].addEventListener("mouseover", function () {
					btnAction[0].addClass('open');
				});

				// btnAction[0].addEventListener("mouseout", function(){
				// 	btnAction[0].classList.remove('open');
				// });
			}

			// // ADICIONA TOOTIPS PARA CAMPOS VAZIOS
			// var fields = jQuery('.fabrik___rowlink.fabrik_edit');
			// Object.keys(fields).forEach(function(key) {
			// 	if (fields[key].textContent == '\n') {
			// 		fields[key].parentElement.setAttribute('data-bs-toggle',"tooltip")
		 	// 		fields[key].parentElement.setAttribute('data-bs-placement',"top")
		 	// 		fields[key].parentElement.setAttribute('title',"Completar ou corrigir esses dados")
			// 	};
			// });


		})
	});
});

window.addEvent('fabrik.loaded', function () {
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

	var mq = window.matchMedia("(max-width: 700px)");
	if (mq.matches) {
		var url = window.location.href;
		if (url.match(/layout=/)) {
			url.replace("bootstrap", "div");
		} else {
			url += '?layout=div';
		}
		location.replace(url);
	}

	mq.addEventListener('change', function () {
		var url = window.location.href;
		if (url.match(/layout=/)) {
			url.replace("bootstrap", "div");
		} else {
			url += '?layout=div';
		}
		location.replace(url);
	});

})



// function reportAbuse(listId, rowId) {
// 	const loadImg   = jQuery('<div style=" display: flex; position: fixed; background: rgba(0,0,0,0.5);width: 100%;top: 0;height: 100vh;margin: auto;"><img style="margin: auto;" src="https://mir-s3-cdn-cf.behance.net/project_modules/disp/35771931234507.564a1d2403b3a.gif"></div>');
	
// 	jQuery('body').append(loadImg);
// 	// jQuery.ajax({
// 	// 	'url': '',
// 	// 	'method': 'get',
// 	// 	'data': {
// 	// 		'listId': listId,
// 	// 		'rowId': rowId,
// 	// 		'option': 'com_fabrik',
// 	// 		'task': 'plugin.pluginAjax',
// 	// 		'plugin': 'workflow',
// 	// 		'method': 'onReportAbuse',
// 	// 		'g': 'form',
// 	// 	},
// 	// 	success: function (data) {
// 	// 		location.reload();
// 	// 	}
// 	// });
// }