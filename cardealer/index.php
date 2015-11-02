<?php
/**
 * Module Name: Cardealer DB Migrate
 * Description: Extends default functionality.
 *		 	    Allows to install car makes and models pack and locations packs for CarDealer theme
 * Author: ThemeMakers
 * Author URI: http://themeforest.net/user/ThemeMakers
 */

include_once TMM_MIGRATE_PATH . 'cardealer/TMM_MigrateCardealerModule.php';

function tmm_migrate_cardealer_admin_enqueue_scripts() {

	$tmm_lang = array(
		'import_carproducers_caution' => __('Are you sure?', TMM_MIGRATE_TEXTDOMAIN),
		'import_carproducers_done' => __('Carproducers imported!', TMM_MIGRATE_TEXTDOMAIN),
		'import_carproducers_alert' => __('Carproducers already imported!', TMM_MIGRATE_TEXTDOMAIN),
		'loading' => __('Loading ...', TMM_MIGRATE_TEXTDOMAIN),
		'import_location_done' => __('Your location list was successfully loaded into server\'s database', TMM_MIGRATE_TEXTDOMAIN),
		'import_location_fail' => __('Something wrong. Please try again!', TMM_MIGRATE_TEXTDOMAIN),
	);

	wp_enqueue_script('tmm_db_migrate_cardealer', TMM_MIGRATE_URL . 'cardealer/cardealer.js', array('jquery', 'tmm_db_migrate'), false, true);
	wp_localize_script('tmm_db_migrate_cardealer', 'tmm_migrate_cardealer_l10n', $tmm_lang);
}

add_action( 'admin_enqueue_scripts', 'tmm_migrate_cardealer_admin_enqueue_scripts' );

add_action( 'wp_ajax_tmm_migrate_import_carproducers', array('TMM_MigrateCardealerModule', 'import_carproducers') );
add_action( 'wp_ajax_tmm_migrate_import_locations', array(new TMM_MigrateCardealerModule(), 'import_carlocations') );
