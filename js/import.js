var TMM_DB_IMPORT = function() {
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
		},
		add_process_txt: function (txt) {
			jQuery('#tmm_db_migrate_process').append('<li>').find('li:last-child').html(txt);
		},
		getParameterByName: function(name) {
			name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				results = regex.exec(location.search);
			return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}
	};
	return self;
};

var tmm_db_import = null;
jQuery(document).ready(function () {
	tmm_db_import = new TMM_DB_IMPORT();
	tmm_db_import.init();
});


