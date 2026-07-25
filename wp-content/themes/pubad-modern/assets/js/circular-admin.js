(function () {
	'use strict';

	document.querySelectorAll('[data-pdf-select]').forEach(function (button) {
		button.addEventListener('click', function () {
			var field = button.closest('.pubad-pdf-field');
			var input = field.querySelector('[data-pdf-input]');
			var preview = field.querySelector('[data-pdf-preview]');
			var frame = wp.media({
				title: pubadCircularAdmin.selectPdf,
				button: { text: pubadCircularAdmin.usePdf },
				library: { type: 'application/pdf' },
				multiple: false
			});

			frame.on('select', function () {
				var file = frame.state().get('selection').first().toJSON();
				if (file.mime !== 'application/pdf') {
					window.alert(pubadCircularAdmin.pdfOnly);
					return;
				}

				input.value = file.id;
				preview.innerHTML = '<a href="' + file.url + '" target="_blank" rel="noopener noreferrer">' + file.filename + '</a>';
			});

			frame.open();
		});
	});

	document.querySelectorAll('[data-pdf-clear]').forEach(function (button) {
		button.addEventListener('click', function () {
			var field = button.closest('.pubad-pdf-field');
			field.querySelector('[data-pdf-input]').value = '';
			field.querySelector('[data-pdf-preview]').innerHTML = '<em>No PDF selected.</em>';
		});
	});
}());
