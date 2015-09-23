(function($) {

	$(function () {

		/* Import CarDealer locations */
		var files_input = $('#upload_locations'),
			files_to_send = {},
			support_form_data = window.FormData ? true : false;//IE8,9 don't support FormData object

		$('.button_browse_zip').on('click', function (e) {
			e.preventDefault();
			files_input.trigger('click');
		});

		files_input.on('change', function () {
			var files = $(this)[0].files,
				files_list_html = '',
				d = new Date();

			$.each(files, function (key, value) {
				if (value.name.match(/.*.zip/)) {
					key = '_' + d.getTime() + key;
					files_to_send[key] = value;

					files_list_html += '<li>';
					files_list_html += '<span>' + value.name + '</span>';
					if (support_form_data) {
						files_list_html += '<a data-id="' + key + '" class="remove js_remove_upload_zip" title="Delete" href="#"></a>';
					}
					files_list_html += '</li>';
				}
			});

			if (files_list_html !== '') {
				if (support_form_data) {
					$('.upload_zip_list').append(files_list_html);
				} else {
					$('.upload_zip_list').html(files_list_html);
				}
			}
		});

		$('.upload_zip_list').on('click', '.js_remove_upload_zip', function (e) {
			e.preventDefault();
			var id = $(this).data('id');
			$(this).parent().remove();
			delete files_to_send[id];
		});

		$('.button_upload_zip').on('click', function (e) {
			e.preventDefault();

			var is_files = false, i;

			for (i in files_to_send) {
				is_files = true;
				break;
			}

			if (is_files) {
				var url = tmm_migrate_cardealer_l10n.import_location_url;

				if (support_form_data) {
					var form = new FormData(),
						xhr = new XMLHttpRequest(),
						loading = '<div id="fountainTextG">\
										<div id="fountainTextG_1" class="fountainTextG">L</div>\
										<div id="fountainTextG_2" class="fountainTextG">o</div>\
										<div id="fountainTextG_3" class="fountainTextG">a</div>\
										<div id="fountainTextG_4" class="fountainTextG">d</div>\
										<div id="fountainTextG_5" class="fountainTextG">i</div>\
										<div id="fountainTextG_6" class="fountainTextG">n</div>\
										<div id="fountainTextG_7" class="fountainTextG">g</div>\
										<div id="fountainTextG_8" class="fountainTextG">.</div>\
										<div id="fountainTextG_9" class="fountainTextG">.</div>\
										<div id="fountainTextG_10" class="fountainTextG">.</div>\
									</div>';

					show_static_info_popup( loading );

					$.each(files_to_send, function (key, file) {
						form.append('locations_zip[]', file);
					});

					xhr.open("post", url, true);
					xhr.onreadystatechange = function () {
						hide_static_info_popup(100);
						if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText == '1') {
							files_to_send = {};
							$('.upload_zip_list').empty();
							show_info_popup( tmm_migrate_cardealer_l10n.import_location_done );
						} else {
							show_info_popup( tmm_migrate_cardealer_l10n.import_location_fail );
						}
					};
					xhr.send(form);
				} else {
					post_to_iframe(url, 'locationsForm', 'locationsIframe');
					$('.upload_zip_list').empty();
					files_input.val('');
				}
			}
		});

		function post_to_iframe(url, form_id, target_name) {
			var iframe = $('#' + target_name),
				form = $('#' + form_id);

			if (!iframe.length) {
				iframe = $('<iframe name="' + target_name + '" id="' + target_name + '" src="javascript:void(0)" style="display:none">');
				$('body').append(iframe);
			}
			if (!form.length) {
				form = $('<form id="' + form_id + '" method="POST" action="' + url + '" target="' + target_name + '" enctype="multipart/form-data" style="display:none"></form>');
				form.append(files_input);
				$('body').append(form);
			}
			form.trigger('submit');
		}

		$('#button_import_carproducers').on('click', function () {
			var $this = $(this);

			if ($this.attr('data-active') != 'true') {
				if (confirm(tmm_migrate_cardealer_l10n.import_carproducers_caution)) {
					var process_div = $('#tmm_db_migrate_process_imp'),
						process_html = '<li><div id="squaresWaveG"><div id="squaresWaveG_1" class="squaresWaveG"></div><div id="squaresWaveG_2" class="squaresWaveG"></div><div id="squaresWaveG_3" class="squaresWaveG"></div><div id="squaresWaveG_4" class="squaresWaveG"></div><div id="squaresWaveG_5" class="squaresWaveG"></div><div id="squaresWaveG_6" class="squaresWaveG"></div><div id="squaresWaveG_7" class="squaresWaveG"></div><div id="squaresWaveG_8" class="squaresWaveG"></div></div></li>';

					process_div.empty().append(process_html);

					var data = {
						action: "tmm_migrate_import_carproducers"
					};
					$.post(ajaxurl, data, function (response) {
						process_div.empty();
						alert(tmm_migrate_cardealer_l10n.import_carproducers_done);
					});
				}
				$this.attr('data-active', true);
			} else {
				alert(tmm_migrate_cardealer_l10n.import_carproducers_alert);
			}
			return false;
		});

	});

})(jQuery);


