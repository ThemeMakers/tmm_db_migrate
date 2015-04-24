<?php

class TMM_MigrateImport extends TMM_MigrateHelper {

	public function __construct() {}
	
	/* handle objects before push content to eval function */
	private static function set_state($array) {
		$obj = new stdClass();
		foreach($array as $k => $v){
			$obj->$k = $v;
		}
		return serialize($obj);
	}

	/* calling by ajax */
	public function import_data() {
		$counter = 0;
		$this->extract_zip();
		chdir($this->get_upload_dir());
		$dat_files = glob("*.dat");

		if(is_array($dat_files)){
			$counter = count($dat_files);

			foreach ($dat_files as $filename) {
				$table = basename($filename, '.dat');
				try {
					$this->process_table($table);
					if(file_exists($this->get_upload_dir() . $table . '.dsc')){
						unlink($this->get_upload_dir() . $table . '.dsc');
					}
					if(file_exists($this->get_upload_dir() . $table . '.dat')){
						unlink($this->get_upload_dir() . $table . '.dat');
					}
				} catch (Exception $e) {}
			}
			if(file_exists($this->get_upload_dir() . 'wpdb.prfx')){
				unlink($this->get_upload_dir() . 'wpdb.prfx');
			}
		}

		wp_die($counter);
	}

	public function process_table($table) {
		global $wpdb;
		$table_dsc = unserialize(file_get_contents($this->get_upload_dir() . $table . '.dsc'));
		$old_wpdb_prefix = file_get_contents($this->get_upload_dir() . 'wpdb.prfx');
		$new_table_name = preg_replace('[^' . $old_wpdb_prefix . ']', $wpdb->prefix, $table);

		$wpdb->query('DROP TABLE IF EXISTS `' . $new_table_name . '`;');

		$table_sql = "CREATE TABLE `" . $new_table_name . "` (";
		if (!empty($table_dsc)) {
			$PRIMARY_KEY = "";
			$UNIQUE_KEY = "";
			$KEY = array();
			foreach ($table_dsc as $col) {
				$table_sql.="`" . $col->Field . "` " . $col->Type;

				if ($col->Null == 'NO') {
					$table_sql.=" NOT NULL";
				}

				if (!empty($col->Default)) {
					$table_sql.=" DEFAULT '" . $col->Default . "'";
				}

				if ($col->Extra == 'auto_increment') {
					$table_sql.=" AUTO_INCREMENT";
				}

				if ($col->Key == 'PRI') {
					$set_pk = true;
					if (($col->Field == 'term_taxonomy_id' OR $col->Field == 'object_id') AND $new_table_name == ($wpdb->prefix . 'term_relationships')) {
						//prevent little bug in db
						$set_pk = false;
					}

					if ($set_pk) {
						$PRIMARY_KEY = $col->Field;
					}
				}

				if ($col->Key == 'UNI') {
					$UNIQUE_KEY = $col->Field;
				}

				if ($col->Key == 'MUL') {
					$KEY[] = $col->Field;
				}

				$table_sql.=',';
			}

			if (!empty($PRIMARY_KEY)) {
				$table_sql.="PRIMARY KEY (`" . $PRIMARY_KEY . "`),";
			}

			if (!empty($UNIQUE_KEY)) {
				if ($table == $old_wpdb_prefix . 'term_taxonomy') {
					$table_sql.="`term_id_taxonomy` (`term_id`,`taxonomy`)";
				} else {
					$table_sql.="UNIQUE KEY `" . $UNIQUE_KEY . "` (`$UNIQUE_KEY`),";
				}
			}

			if (!empty($KEY)) {
				foreach ($KEY as $k) {
					$table_sql.="KEY `" . $k . "` (`" . $k . "`),";
				}
			}
		}
		$table_sql.=");";
		$table_sql = str_replace(",);", ");", $table_sql);
		$wpdb->query($table_sql);

		//*** DATA INSERTING
		$content = str_replace('__tmm_old_home_url__', home_url(), file_get_contents($this->get_upload_dir() . $table . '.dat'));
		$content = str_replace('__tmm_wpdb_prefix__', $wpdb->prefix, $content);
		$content = str_replace('stdClass::__set_state', 'self::set_state', $content);

		eval('$table_data=' . $content . ';');
		
		if (!empty($table_data) AND is_array($table_data)) {
			foreach ($table_data as $row) {
				$data_string = "";
				if (!empty($row)) {
					$is_first_iter = true;
					$data = array();
					foreach ($row as $key => $value) {
						if (is_array($value) OR is_object($value)) {
							$data[$key] = serialize($value);
						} else {
							$data[$key] = $value;
						}
					}
				}
				$wpdb->insert($new_table_name, $data);
			}
		}
	}
	
}
