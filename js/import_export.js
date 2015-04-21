var TMM_DB_MIGRATE = function() {

	var self = {
		tables: [],

		init: function () {
			jQuery('#button_prepare_import_data').click(function () {
				self.import( jQuery(this) );
				return false;
			});

			jQuery('#button_prepare_export_data').click(function() {
				self.export();
				return false;
			});
		},

		import: function ($this) {
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
					//window.location = location.protocol + "//" + location.hostname;
				});
				$this.attr('data-active', true);
			}
		},

		export: function ($this) {
			jQuery('#tmm_db_migrate_process').empty();
			var data = {
				action: "tmm_prepare_export_data"
			};
			jQuery.post(ajaxurl, data, function(tables) {
				self.tables = jQuery.parseJSON(tables);
				self.add_process_txt(tmm_db_migrate_lang1 + ' ' + self.tables.length);
				self.process_table(self.tables[0], 0);
			});
		},

		process_table: function(table, index) {
			if (index < self.tables.length) {
				self.add_process_txt(tmm_db_migrate_lang2 + ' ' + table + ' ...');
				var data = {
					action: "tmm_process_export_data",
					table: table
				};
				jQuery.post(ajaxurl, data, function(row_count) {
					jQuery('#tmm_db_migrate_process').find('li:last-child').append('(' + (row_count ? row_count : 0) + ' rows processed)');
					self.process_table(self.tables[index + 1], index + 1);
				});
			} else {
				self.add_process_txt(tmm_db_migrate_lang3);
				self.zip_tables();
			}
		},

		zip_tables: function() {
			var data = {
				action: "tmm_zip_export_data"
			};
			jQuery.post(ajaxurl, data, function(zip_link) {
				self.add_process_txt('<a href="' + zip_link + '">' + tmm_db_migrate_lang4 + '</a>');
			});
		},

		add_process_txt: function (txt) {
			jQuery('#tmm_db_migrate_process').append('<li>').find('li:last-child').html(txt);
		}

	};
	return self;
};

jQuery(document).ready(function () {
	tmm_db_import = new TMM_DB_MIGRATE();
	tmm_db_import.init();
});


