<?php
/*
 * Plugin Name: ThemeMakers DB Migrate
 * Plugin URI: http://webtemplatemasters.com
 * Description: ThemeMakers WordPress DataBase Migration
 * Author: ThemeMakers
 * Version: 2.0.5
 * Author URI: http://themeforest.net/user/ThemeMakers
 * Text Domain: tmm_db_migrate
 */

define('TMM_MIGRATE_TEXTDOMAIN', 'tmm_db_migrate');
define('TMM_MIGRATE_PATH', plugin_dir_path(__FILE__));
define('TMM_MIGRATE_URL', plugin_dir_url(__FILE__));
define('TMM_MIGRATE_UPLOAD_ATTACHMENTS_PACK', true);
define('TMM_MIGRATE_UPLOAD_ATTACHMENT_BY_HTTP', false);

include_once TMM_MIGRATE_PATH . 'classes/TMM_MigrateHelper.php';
include_once TMM_MIGRATE_PATH . 'classes/TMM_MigrateExport.php';
include_once TMM_MIGRATE_PATH . 'classes/TMM_MigrateImport.php';

add_action( 'plugins_loaded', 'tmm_migrate_load_textdomain' );
/**
 * Load plugin textdomain.
 */
function tmm_migrate_load_textdomain() {
	load_plugin_textdomain( 'tmm_db_migrate', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action( 'admin_enqueue_scripts', 'tmm_migrate_admin_enqueue_scripts' );
/**
 * Enqueue admin scripts.
 */
function tmm_migrate_admin_enqueue_scripts() {

	$tmm_lang = array(
		'prepare_finished' => esc_html__('Prepare finished. Count of tables:', 'tmm_db_migrate'),
		'process_table' => esc_html__('Process table:', 'tmm_db_migrate'),
		'process_finished' => esc_html__('Process Finishing ...', 'tmm_db_migrate'),
		'download_zip' => esc_html__('Download data zip', 'tmm_db_migrate'),
		'import_started' => esc_html__('Import started. Please wait ...', 'tmm_db_migrate'),
		'import_finished' => esc_html__('Content imported!', 'tmm_db_migrate'),
		'import_caution' => esc_html__('Are you sure? Please make sure you backed up your website database before proceed installing demo. All your current content will be overwritten by the demo content if you confirm!', 'tmm_db_migrate'),
	);

	wp_enqueue_script('tmm_db_migrate', TMM_MIGRATE_URL . 'js/import_export.js', array('jquery'), false, true);
	wp_localize_script('tmm_db_migrate', 'tmm_migrate_l10n', $tmm_lang);
}

add_action( 'admin_init', 'tmm_migrate_init', 999 );
/**
 * Init main functionality.
 */
function tmm_migrate_init() {
	if ( current_user_can('manage_options') ) {
		/* try to increase performance settings */
		if(intval(ini_get('memory_limit')) < 256){
			@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', '256M' ) );
		}
		if(intval(ini_get('max_execution_time')) < 180){
			@ini_set( 'max_execution_time', apply_filters( 'max_execution_time', '180' ) );
		}

		$export = new TMM_MigrateExport();
		$import = new TMM_MigrateImport();

		/* export actions */
		add_action('wp_ajax_tmm_prepare_export_data', array($export, 'prepare_export_data'));
		add_action('wp_ajax_tmm_process_export_data', array($export, 'process_table'));
		add_action('wp_ajax_tmm_zip_export_data', array($export, 'zip_export_data'));
		/* import actions */
		add_action('wp_ajax_tmm_migrate_import_content', array($import, 'import_content'));
		add_action('wp_ajax_tmm_migrate_import_attachment', array($import, 'upload_attachment'));
	}
}

add_action( 'tmm_add_theme_options_tab', 'tmm_migrate_add_settings_tab', 999 );
/**
 * Add Settings tab.
 */
function tmm_migrate_add_settings_tab() {
	if ( current_user_can('manage_options') ) {
		if (class_exists('TMM_OptionsHelper')) {

			$content = array();
			$tmpl_path = TMM_MIGRATE_PATH . '/views/theme_options_tab.php';

			$content[ 'tmm_db_migrate' ] = array(
				'title' => '',
				'type' => 'custom',
				'custom_html' => TMM::draw_free_page($tmpl_path),
				'show_title' => false
			);

			$sections = array(
				'name' => esc_html__("Import / Export", 'tmm_db_migrate'),
				'css_class' => 'shortcut-plugins',
				'show_general_page' => true,
				'content' => $content,
				'child_sections' => array(),
				'menu_icon' => 'dashicons-admin-tools'
			);

			TMM_OptionsHelper::$sections[ 'tmm_db_migrate' ] = $sections;

		}
	}
}