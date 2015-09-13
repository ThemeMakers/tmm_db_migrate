var TMM_DB_IMPORT = function () {
	var self = {
		init: function () {
			jQuery('#button_prepare_import_data').click(function () {
				var $this = jQuery(this);

				if ($this.attr('data-active') != 'true') {
                    
                    if(!confirm(tmm_db_migrate_lang7)){
						return false;
					}

					jQuery('#tmm_db_migrate_process_imp').append('<li><div id="squaresWaveG"><div id="squaresWaveG_1" class="squaresWaveG"></div><div id="squaresWaveG_2" class="squaresWaveG"></div><div id="squaresWaveG_3" class="squaresWaveG"></div><div id="squaresWaveG_4" class="squaresWaveG"></div><div id="squaresWaveG_5" class="squaresWaveG"></div><div id="squaresWaveG_6" class="squaresWaveG"></div><div id="squaresWaveG_7" class="squaresWaveG"></div><div id="squaresWaveG_8" class="squaresWaveG"></div></div></li>');
					var data = {
						action: "tmm_import_data"
					};
					jQuery.post(ajaxurl, data, function (tables_count) {
						jQuery('#tmm_db_migrate_process_imp').empty();
						window.location = location.protocol + "//" + location.hostname;
					});
					$this.attr('data-active', true);
				}
				return false;
			});

			/* Import CarDealer locations */

			var files_input = jQuery('#upload_locations'),
				files_to_send = {},
				support_form_data = window.FormData ? true : false;//IE8,9 don't support FormData object

			jQuery('.button_browse_zip').on('click', function (e) {
				e.preventDefault();
				files_input.trigger('click');
			});

			files_input.on('change', function () {
				var files = jQuery(this)[0].files,
					files_list_html = '',
					d = new Date();

				jQuery.each(files, function (key, value) {
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
						jQuery('.upload_zip_list').append(files_list_html);
					} else {
						jQuery('.upload_zip_list').html(files_list_html);
					}
				}
			});

			jQuery('.upload_zip_list').on('click', '.js_remove_upload_zip', function (e) {
				e.preventDefault();
				var id = jQuery(this).data('id');
				jQuery(this).parent().remove();
				delete files_to_send[id];
			});

			jQuery('.button_upload_zip').on('click', function (e) {
				e.preventDefault();

				var is_files = false, i;
				for (i in files_to_send) {
					is_files = true;
					break;
				}

				if (is_files) {
					var url = tmm_db_migrate_link + 'index.php';

					if (support_form_data) {
						var form = new FormData(),
                            xhr = new XMLHttpRequest(),
                            loading = jQuery('.location_loader').find('#fountainTextG').html();
                            
                        show_static_info_popup(loading);

						jQuery.each(files_to_send, function (key, file) {
							form.append('locations_zip[]', file);
						});

						xhr.open("post", url, true);
						xhr.onreadystatechange = function () {
							hide_static_info_popup(100);
							if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText == '1') {
								files_to_send = {};
								jQuery('.upload_zip_list').empty();
								show_info_popup('Your location list was successfully loaded into server\'s database');
							} else {
								show_info_popup('Something wrong. Please try again!');
							}
						};
						xhr.send(form);
					} else {
						post_to_iframe(url, 'locationsForm', 'locationsIframe');
						jQuery('.upload_zip_list').empty();
						files_input.val('');
					}
				}
			});

			function post_to_iframe(url, form_id, target_name) {
				var iframe = jQuery('#' + target_name),
					form = jQuery('#' + form_id);

				if (!iframe.length) {
					iframe = jQuery('<iframe name="' + target_name + '" id="' + target_name + '" src="javascript:void(0)" style="display:none">');
					jQuery('body').append(iframe);
				}
				if (!form.length) {
					form = jQuery('<form id="' + form_id + '" method="POST" action="' + url + '" target="' + target_name + '" enctype="multipart/form-data" style="display:none"></form>');
					form.append(files_input);
					jQuery('body').append(form);
				}
				form.trigger('submit');
			}
		},
		add_process_txt: function (txt) {
			jQuery('#tmm_db_migrate_process').append('<li>').find('li:last-child').html(txt);
		},
		getParameterByName: function (name) {
			name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				results = regex.exec(location.search);
			return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}
	};
	//***
	return self;
};
//***
var tmm_db_import = null;
jQuery(document).ready(function () {
	tmm_db_import = new TMM_DB_IMPORT();
	tmm_db_import.init();
});


