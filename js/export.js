var TMM_DB_EXPORT = function() {
	var self = {
		tables: [],
		init: function() {
			jQuery('#button_prepare_export_data').click(function() {
                jQuery('#tmm_db_migrate_process').empty();
				var data = {
					action: "tmm_prepare_export_data"
				};
				jQuery.post(ajaxurl, data, function(tables) {
					self.tables = jQuery.parseJSON(tables);
					self.add_process_txt(tmm_db_migrate_lang1 + ' ' + self.tables.length);
					self.process_table(self.tables[0], 0);
				});

				return false;
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
		add_process_txt: function(txt) {
			jQuery('#tmm_db_migrate_process').append('<li>').find('li:last-child').html(txt);
		}
	};
	//***
	return self;
};
//***
var tmm_db_export = null;
jQuery(document).ready(function() {
	tmm_db_export = new TMM_DB_EXPORT();
	tmm_db_export.init();
});





