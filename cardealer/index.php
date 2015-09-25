<?php
/**
 * Module Name: Cardealer DB Migrate
 * Description: Extends default functionality.
 *		 	    Allows to install car makes and models pack and locations packs for CarDealer theme
 * Author: ThemeMakers
 * Author URI: http://themeforest.net/user/ThemeMakers
 */

if(isset($_FILES['locations_zip']) && is_uploaded_file($_FILES['locations_zip']['tmp_name'][0])){
	if(intval(ini_get('memory_limit')) < 256){
		@ini_set('memory_limit', '256M');
	}
	if(intval(ini_get('max_execution_time')) < 300){
		@ini_set('max_execution_time', '300');
	}
	$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
	$wp_load = $parse_uri[0] . 'wp-load.php';
	require_once($wp_load);
	include_once TMM_MIGRATE_PATH . 'classes/TMM_MigrateHelper.php';
	include_once TMM_MIGRATE_PATH . 'cardealer/TMM_MigrateCardealerModule.php';

	$import = new TMM_MigrateCardealerModule();
	$import->import_carlocation();
	die;
}

include_once TMM_MIGRATE_PATH . 'cardealer/TMM_MigrateCardealerModule.php';

function tmm_migrate_cardealer_admin_enqueue_scripts() {

	$tmm_lang = array(
		'import_carproducers_caution' => __('Are you sure?', TMM_MIGRATE_TEXTDOMAIN),
		'import_carproducers_done' => __('Carproducers imported!', TMM_MIGRATE_TEXTDOMAIN),
		'import_carproducers_alert' => __('Carproducers already imported!', TMM_MIGRATE_TEXTDOMAIN),
		'loading' => __('Loading ...', TMM_MIGRATE_TEXTDOMAIN),
		'import_location_done' => __('Your location list was successfully loaded into server\'s database', TMM_MIGRATE_TEXTDOMAIN),
		'import_location_fail' => __('Something wrong. Please try again!', TMM_MIGRATE_TEXTDOMAIN),
		'import_location_url' => TMM_MIGRATE_URL . 'cardealer/index.php',
	);

	wp_enqueue_script('tmm_db_migrate_cardealer', TMM_MIGRATE_URL . 'cardealer/cardealer.js', array('jquery', 'tmm_db_migrate'), false, true);
	wp_localize_script('tmm_db_migrate_cardealer', 'tmm_migrate_cardealer_l10n', $tmm_lang);
}

add_action( 'admin_enqueue_scripts', 'tmm_migrate_cardealer_admin_enqueue_scripts' );

add_action( 'wp_ajax_tmm_migrate_import_carproducers', array('TMM_MigrateCardealerModule', 'import_carproducers') );
