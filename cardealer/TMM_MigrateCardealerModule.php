<?php

class TMM_MigrateCardealerModule extends TMM_MigrateHelper {

	public static function import_carproducers() {

		$data = file_get_contents(TMM_MIGRATE_PATH . 'cardealer/carproducers.dat');
		$data = unserialize($data);

		if (!empty($data) AND is_array($data)) {
			foreach ($data as $maker_obj) {
				$args = array(
					'name' => $maker_obj->name,
					//'slug' => $maker_obj->slug,
					'parent' => 0
				);
				$term = @wp_insert_term($maker_obj->name, 'carproducer', $args);
				if(is_array($term) && isset($term['term_id'])){
					$parent_id = $term['term_id'];
				}else{
					$parent_id = get_term_by('name', $maker_obj->name, 'carproducer');
					$parent_id = $parent_id->term_id;
				}

				if (!empty($maker_obj->childs) AND isset($maker_obj->childs)) {
					foreach ($maker_obj->childs as $model_obj) {
						$args = array(
							'name' => $model_obj->name,
							//'slug' => $model_obj->slug,
							'parent' => $parent_id
						);
						@wp_insert_term($model_obj->name, 'carproducer', $args);
					}
				}
			}
		}

		delete_option("carproducer_children");
		_get_term_hierarchy("carproducer");

		exit;
	}

	/* return array with terms objects of 'carproducer' taxonomy */
	public static function get_carproducers($only_makes = false){
		$args = array(
			'parent' => '0',
			'hide_empty' => 0,
		);
		$terms = get_terms('carproducer', $args);
		if(!$only_makes){
			foreach($terms as $term){
				$args['parent'] = $term->term_id;
				$childs = get_terms('carproducer', $args);
				if(is_array($childs) && count($childs)){
					$term->childs = $childs;
				}
			}
		}
		file_put_contents(TMM_MIGRATE_PATH . 'cardealer/temp.dat', serialize($terms));
		return $terms;
	}

	/* Import CarDealer locations */
	public function import_carlocations(){

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
		$files_count = count($_POST['locations']);
		$uploaded_files_count = 0;
		$result = array(
			'success' => false,
			'errors' => array(),
		);
		$locations = array();

		if (!empty($_POST['locations'])) {
			$locations = $_POST['locations'];
		}

		if (is_array($locations)) {

			foreach ($locations as $file_id => $file_url) {

				if(strpos($file_url, '.zip') !== false){

					$basename = basename($file_url);
					$file_name = $targetFolder . $basename;

					$path = wp_upload_dir();
					$temp = explode('wp-content/uploads', $file_url);
					$file_path = $path['basedir'] . $temp[1];

					$is_copied = copy( $file_path, $file_name);

					if (!$is_copied) {
						$result['errors'][] = $basename;
					}

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
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
						WP_Filesystem();
						$zipfile = unzip_file($file_name, $targetFolder . 'temp');
					}

					if($zipfile){
						$this->process_location_files($targetFolder . 'temp');
						$uploaded_files_count++;
					}

					@unlink($file_name);

				}

			}

		}

		$this->delete_dir($targetFolder . 'temp');

		if($files_count === $uploaded_files_count){
			$result['success'] = 1;
		}

		ob_clean();
		echo json_encode($result);
		wp_die();
	}

	protected function create_locations_upload_folder() {
		$path = wp_upload_dir();
		$path = $path['basedir'];

		if (!file_exists($path)) {
			mkdir($path, 0775, 1);
		}
		$path = $path . '/locations';

		if (!file_exists($path)) {
			mkdir($path, 0775, 1);
		}

		return $path . '/';
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
			$country_id = (int) $this->insert_location_item($args);
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
						$country_id = (int) $this->insert_location_item($args);
					}

					if($country_id){
						if(isset($data['state_name'])){
							$state_id = 0;
							$args = array(
								'parent_id' => $country_id,
								'name' => $data['state_name']
							);
							$state_id = (int) $this->insert_location_item($args);

							if($state_id && isset($data['cities']) && is_array($data['cities'])){

								global $wpdb;
								$existed_cities = $wpdb->get_results("SELECT `slug` FROM tmm_cars_locations WHERE parent_id = {$state_id}", ARRAY_A);
								$slugs = array();
								$new_cities = array();

								foreach ($existed_cities as $k => $v) {
									if (isset($v['slug'])) {
										$slugs[$v['slug']] = 1;
									}
								}

								foreach($data['cities'] as $city){
									$city_slug = sanitize_key($city['city_name']);

									if ( !isset($slugs[$city_slug]) ) {
										$new_cities[] = $city['city_name'];
									}

								}

								$this->insert_locations_pack($state_id, $new_cities);

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

	public function insert_locations_pack($parent_id, $data) {
		if (empty($data)) {
			return;
		}

		global $wpdb;
		$query = "INSERT INTO `tmm_cars_locations` (`id`, `parent_id`, `name`, `slug`) VALUES ";

		foreach ($data as $k => $v) {
			$query .= $wpdb->prepare("(NULL, '%d', '%s', '%s'),", $parent_id, $v, sanitize_key($v));
		}

		$query = rtrim($query, ',') . ';';

		$wpdb->query($query);
	}

}
