<?php

class TMM_ImpExp_Import extends TMM_ImpExp_DB {

	private $saved_options = array();
	private $demo_user_id = 1;

	public function __construct() {}
	
	/* handle objects before push content to eval function */
	private static function set_state($array) {
		$obj = new stdClass();
		foreach($array as $k => $v){
			$obj->$k = $v;
		}
		return serialize($obj);
	}

	//ajax
	public function import_data() {
		/* save general options */
		$this->saved_options = array(
			'blogname' => get_option('blogname'),
			'blogdescription' => get_option('blogdescription'),
			'admin_email' => get_option('admin_email'),
			'auth_key' => get_option('auth_key'),
			'auth_salt' => get_option('auth_salt'),
			'ftp_credentials' => get_option('ftp_credentials'),
			'db_version' => get_option('db_version'),
			'initial_db_version' => get_option('initial_db_version'),
		);

		$counter = 0;
		$this->extract_zip();
		chdir($this->get_upload_dir());
		$dat_files = glob("*.dat");

		if(is_array($dat_files)){
			$counter = count($dat_files);

			foreach ($dat_files as $filename) {
				$table = basename($filename, '.dat');
				try {
					if (@strrpos($table, '_users', -6) === false && @strrpos($table, '_usermeta', -9) === false) {
						$this->process_table($table);
					}
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
		//***
		$wpdb->query('DROP TABLE IF EXISTS `' . $new_table_name . '`;');
		//TABLE CREATING
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
			//***
			if (!empty($PRIMARY_KEY)) {
				$table_sql.="PRIMARY KEY (`" . $PRIMARY_KEY . "`),";
			}
			//***
			if (!empty($UNIQUE_KEY)) {
				if ($table == $old_wpdb_prefix . 'term_taxonomy') {
					$table_sql.="`term_id_taxonomy` (`term_id`,`taxonomy`)";
				} else {
					$table_sql.="UNIQUE KEY `" . $UNIQUE_KEY . "` (`$UNIQUE_KEY`),";
				}
			}
			//***
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

				if (!empty($row)) {
					$data = array();

					if (isset($row['option_name']) && isset($this->saved_options[ $row['option_name'] ])) {
						$row['option_value'] = $this->saved_options[ $row['option_name'] ];
					}

					if (isset($row['post_author'])) {
						$row['post_author'] = $this->demo_user_id;
					}

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
	
	/* Import CarDealer locations */
	public function import_carlocation(){
		global $wpdb;
		$wpdb->query("CREATE TABLE IF NOT EXISTS `tmm_cars_locations` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`parent_id` int(11) NOT NULL,
			`name` varchar(24) NOT NULL,
			`slug` varchar(24) NOT NULL,
			 PRIMARY KEY (`id`),
			 INDEX (`parent_id`),
			 INDEX (`slug`)
		)");
		$targetFolder = $this->create_locations_upload_folder();
		$files_count = count($_FILES['locations_zip']['tmp_name']);
		$uploaded_files_count = 0;
		$upload_success = false;
		
		for($i=0;$i<count($_FILES['locations_zip']['tmp_name']);$i++){
			if(strpos($_FILES['locations_zip']['type'][$i], 'zip')){
				$file_name = $targetFolder . $_FILES['locations_zip']['name'][$i];
				move_uploaded_file($_FILES['locations_zip']['tmp_name'][$i], $file_name);
				
				if(is_dir($targetFolder . 'temp')){
					$this->delete_dir($targetFolder . 'temp');
				}
				mkdir($targetFolder . 'temp', 0766);
				chmod($file_name, 0766);
				
				if(class_exists('ZipArchive')){
					$zip = new ZipArchive();
					if ($zip->open($file_name) === TRUE) {
						$zip->extractTo($targetFolder . 'temp');
						$zip->close();
						$zipfile = true;
					} else {
						echo 'failed';
						$zipfile = false;
					}
				}else{
					require_once(ABSPATH . 'wp-admin/includes/file.php');
					WP_Filesystem();
					$zipfile = unzip_file($file_name, $targetFolder . 'temp');
				}
				
				if($zipfile){
					$this->process_location_files($targetFolder . 'temp');
					$uploaded_files_count++;
				}
			}
		}
		if($files_count === $uploaded_files_count){
			$upload_success = 1;
		}
		
		echo $upload_success;
	}
	
	public function process_location_files($folder) {
		/* import country */
		$country_id = 0;
		$big_countries_list = array(
			'BR' => 'Brazil',
			'CN' => 'China',
			'FR' => 'France',
			'DE' => 'Germany',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'MX' => 'Mexico',
			'NP' => 'Nepal',
			'PK' => 'Pakistan',
			'PE' => 'Peru',
			'PL' => 'Poland',
			'RU' => 'Russia',
			'ES' => 'Spain',
			'TH' => 'Thailand',
			'TR' => 'Turkey',
			'US' => 'USA',
			'VN' => 'Vietnam',
		);
		if(file_exists($folder . '/country_name.dat')){
			$country_name = file_get_contents($folder . '/country_name.dat');
			$args = array(
				'parent_id' => 0,
				'name' => $country_name
			);
			$country_id = $this->insert_location_item($args);
		}
		/* import state */
		chdir($folder);
		$dat_files = glob("*.dat");

		if(is_array($dat_files)){
			foreach ($dat_files as $file) {
				$file_name = basename($file, '.dat');
				if($file_name !== 'country_name'){
					$content = file_get_contents($folder.'/'.$file_name.'.dat');
					$data = json_decode($content, true);
					$country_name = trim($data['country_code']);
					if(!$country_id && isset($big_countries_list[$country_name])){
						$args = array(
							'parent_id' => 0,
							'name' => $big_countries_list[$country_name]
						);
						$country_id = $this->insert_location_item($args);
					}
					if($country_id){
						if(isset($data['state_name'])){
							$state_id = 0;
							$args = array(
								'parent_id' => $country_id,
								'name' => $data['state_name']
							);
							$state_id = $this->insert_location_item($args);
							if($state_id && isset($data['cities']) && is_array($data['cities'])){
								foreach($data['cities'] as $city){
									$args = array(
										'parent_id' => $state_id,
										'name' => $city['city_name']
									);
									$this->insert_location_item($args);
								}
							}
						}
					}
				}
			}
		}
		
		return $country_id;
	}

	public function insert_location_item($data) {
		global $wpdb;
		$id = 0;
		$data['parent_id'] = (int) $data['parent_id'];
		$data['name'] = trim($data['name']);
		if($data['name'] !== ''){
			$id = (int) $wpdb->get_var("SELECT `id` FROM tmm_cars_locations WHERE parent_id = {$data['parent_id']} AND slug = '". sanitize_key($data['name']) ."'");
			if(!$id){
				$wpdb->insert('tmm_cars_locations', array('parent_id' => $data['parent_id'], 'name' => $data['name'], 'slug' => sanitize_key($data['name'])));
				$id = $wpdb->insert_id;
			}
		}
		return $id;
	}
	
}
