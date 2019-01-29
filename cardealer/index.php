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
		'import_carproducers_caution' => esc_html__('Are you sure?', 'tmm_db_migrate'),
		'import_carproducers_done' => esc_html__('Carproducers imported!', 'tmm_db_migrate'),
		'import_carproducers_alert' => esc_html__('Carproducers already imported!', 'tmm_db_migrate'),
		'loading' => esc_html__('Loading ...', 'tmm_db_migrate'),
		'import_location_done' => esc_html__('Your location list was successfully loaded into server\'s database', 'tmm_db_migrate'),
		'import_location_fail' => esc_html__('Something wrong. Please try again!', 'tmm_db_migrate'),
	);

	wp_enqueue_script('tmm_db_migrate_cardealer', TMM_MIGRATE_URL . 'cardealer/cardealer.js', array('jquery', 'tmm_db_migrate'), false, true);
	wp_localize_script('tmm_db_migrate_cardealer', 'tmm_migrate_cardealer_l10n', $tmm_lang);
}

add_action( 'admin_enqueue_scripts', 'tmm_migrate_cardealer_admin_enqueue_scripts' );

add_action( 'wp_ajax_tmm_migrate_import_carproducers', array('TMM_MigrateCardealerModule', 'import_carproducers') );
add_action( 'wp_ajax_tmm_migrate_import_locations', array(new TMM_MigrateCardealerModule(), 'import_carlocations') );
