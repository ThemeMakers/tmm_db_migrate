<?php
/*
 * Plugin Name: ThemeMakers DB Migrate
 * Plugin URI: http://webtemplatemasters.com
 * Description: ThemeMakers WordPress DataBase Migration
 * Author: ThemeMakers
 * Version: 1.1.0
 * Author URI: http://themeforest.net/user/ThemeMakers
 * Text Domain: tmm_db_migrate
 */

define('TMM_MIGRATE_TEXTDOMAIN', 'tmm_db_migrate');
define('TMM_MIGRATE_PATH', plugin_dir_path(__FILE__));
define('TMM_MIGRATE_URL', plugin_dir_url(__FILE__));

include_once TMM_MIGRATE_PATH . 'classes/TMM_MigrateHelper.php';
include_once TMM_MIGRATE_PATH . 'classes/TMM_MigrateExport.php';
include_once TMM_MIGRATE_PATH . 'classes/TMM_MigrateImport.php';

add_action( 'plugins_loaded', 'tmm_migrate_load_textdomain' );
/**
 * Load plugin textdomain.
 */
function tmm_migrate_load_textdomain() {
	load_plugin_textdomain( TMM_MIGRATE_TEXTDOMAIN, false, TMM_MIGRATE_PATH . 'languages' );
}

add_action( 'admin_enqueue_scripts', 'tmm_migrate_admin_enqueue_scripts' );
/**
 * Enqueue admin scripts.
 */
function tmm_migrate_admin_enqueue_scripts() {
	wp_enqueue_script('tmm_db_migrate', TMM_MIGRATE_URL . 'js/import_export.js', array('jquery'));
	?>
	<script type="text/javascript">
		var tmm_db_migrate_link = "<?php echo TMM_MIGRATE_URL; ?>";
		var tmm_db_migrate_lang1 = "<?php _e('Prepare finished. Count of tables:', TMM_MIGRATE_TEXTDOMAIN); ?>";
		var tmm_db_migrate_lang2 = "<?php _e('Process table:', TMM_MIGRATE_TEXTDOMAIN); ?>";
		var tmm_db_migrate_lang3 = "<?php _e('Process Finishing ...', TMM_MIGRATE_TEXTDOMAIN); ?>";
		var tmm_db_migrate_lang4 = "<?php _e('Download data zip', TMM_MIGRATE_TEXTDOMAIN); ?>";
		var tmm_db_migrate_lang5 = "<?php _e('Import started. Please wait ...', TMM_MIGRATE_TEXTDOMAIN); ?>";
		var tmm_db_migrate_lang6 = "<?php _e('Import finished. Count of tables:', TMM_MIGRATE_TEXTDOMAIN); ?>";
		var tmm_db_migrate_lang7 = "<?php _e('Are you sure? All content will be rewritten by the demo content if you confirm!', TMM_MIGRATE_TEXTDOMAIN); ?>";
	</script>
<?php
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
		add_action('wp_ajax_tmm_import_data', array($import, 'import_data'));
	}
}

/* TODO: remove this class */
class TMM_MigratePlugin {}
