(function($) {

	$(function () {

		/* Import CarDealer locations */
		var files_to_send = {};

		$('.button_browse_zip').on('click', function (e) {
			e.preventDefault();

			var frame = wp.media({
				title: wp.media.view.l10n.uploadFilesTitle,
				multiple: true,
				library: { type: 'application' }
			});

			frame.on( 'select', function() {
				var selection = frame.state().get('selection');
					files_list_html = '';

				selection.each(function(attachment) {
					var url = attachment.attributes.url;

					if (attachment.attributes.filename.match(/.*.zip/) && !files_to_send[attachment.attributes.id]) {
						files_to_send[attachment.attributes.id] = url;

						files_list_html += '<li>';
						files_list_html += '<span>' + attachment.attributes.filename + '</span>';
						files_list_html += '<a data-id="' + attachment.attributes.id + '" class="remove js_remove_upload_zip" title="Delete" href="#"></a>';
						files_list_html += '</li>';
					}
				});

				if (files_list_html !== '') {
					$('.upload_zip_list').append(files_list_html);
				}

			});

			frame.open();

		});

		$('.upload_zip_list').on('click', '.js_remove_upload_zip', function (e) {
			e.preventDefault();
			var id = $(this).data('id');
			$(this).parent().remove();
			delete files_to_send[id];
		});

		$('.button_upload_zip').on('click', function (e) {
			e.preventDefault();

			var is_files = false,
				i;

			for (i in files_to_send) {
				is_files = true;
				break;
			}

			if (is_files) {

				var loading = '<div id="fountainTextG">\
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

				var data = {
					action: "tmm_migrate_import_locations",
					locations: files_to_send
				};

				jQuery.post(ajaxurl, data, function (response) {
					hide_static_info_popup(100);

					if (response.success && response.success == 1) {
						files_to_send = {};
						$('.upload_zip_list').empty();
						show_info_popup( tmm_migrate_cardealer_l10n.import_location_done );
					} else {
						show_info_popup( tmm_migrate_cardealer_l10n.import_location_fail );
					}
				}, 'json');

			}

		});

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


