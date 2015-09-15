<?php
/*
  Plugin Name: ThemeMakers DB Migrate
  Plugin URI: http://webtemplatemasters.com
  Description: ThemeMakers WordPress DataBase Migration
  Author: ThemeMakers
  Version: 1.1.0
  Author URI: http://themeforest.net/user/ThemeMakers
 */

define('TMM_MIGRATE_PLUGIN_TEXTDOMAIN', 'tmm_db_migrate');

class TMM_MigratePlugin {

	private $export = null;
	private $import = null;

	public function get_application_path() {
		return plugin_dir_path(__FILE__);
	}

	public static function get_application_uri() {
		return plugin_dir_url(__FILE__);
	}

	public function init() {
		if(intval(ini_get('memory_limit')) < 256){
			@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', '256M' ) );
		}
		if(intval(ini_get('max_execution_time')) < 180){
			@ini_set( 'max_execution_time', apply_filters( 'max_execution_time', '180' ) );
		}
		load_plugin_textdomain(TMM_MIGRATE_PLUGIN_TEXTDOMAIN, false, $this->get_application_path() . 'languages');
		add_action('admin_notices', array($this, 'admin_notices'));

		include_once $this->get_application_path() . 'classes/TMM_MigrateHelper.php';
		include_once $this->get_application_path() . 'classes/TMM_MigrateExport.php';
		include_once $this->get_application_path() . 'classes/TMM_MigrateImport.php';

		$this->export = new TMM_MigrateExport();
		$this->import = new TMM_MigrateImport();

		//***export actions
		add_action('wp_ajax_tmm_prepare_export_data', array($this->export, 'prepare_export_data'));
		add_action('wp_ajax_tmm_process_export_data', array($this->export, 'process_table'));
		add_action('wp_ajax_tmm_zip_export_data', array($this->export, 'zip_export_data'));
		//***import actions
		add_action('wp_ajax_tmm_import_data', array($this->import, 'import_data'));
	}

	public function admin_notices() {
		$notices = "";
		if (!is_writable($this->export->get_upload_dir())) {
			$notices.=sprintf(__('<div class="error"><p>To make plugin ThemeMakers DB Migrate work correctly you need to set the permissions 775 for <b>%s</b> folder or create this one. Follow <a href="http://webtemplatemasters.com/tutorials/permissions/" target="_blank">the link</a> to read the instructions how to do it properly.</p></div>', TMM_MIGRATE_PLUGIN_TEXTDOMAIN), $this->export->get_upload_dir());
		}
		echo $notices;
	}

	public static function draw_options_page() {
		wp_enqueue_script('tmm_db_migrate_exp', self::get_application_uri() . 'js/export.js', array('jquery'));
		wp_enqueue_script('tmm_db_migrate_imp', self::get_application_uri() . 'js/import.js', array('jquery'));
		?>
		<script type="text/javascript">
			var tmm_db_migrate_link = "<?php echo self::get_application_uri() ?>";
			var tmm_db_migrate_lang1 = "<?php _e('Prepare finished. Count of tables:', TMM_MIGRATE_PLUGIN_TEXTDOMAIN); ?>";
			var tmm_db_migrate_lang2 = "<?php _e('Process table:', TMM_MIGRATE_PLUGIN_TEXTDOMAIN); ?>";
			var tmm_db_migrate_lang3 = "<?php _e('Process Finishing ...', TMM_MIGRATE_PLUGIN_TEXTDOMAIN); ?>";
			var tmm_db_migrate_lang4 = "<?php _e('Download data zip', TMM_MIGRATE_PLUGIN_TEXTDOMAIN); ?>";
			var tmm_db_migrate_lang5 = "<?php _e('Import started. Please wait ...', TMM_MIGRATE_PLUGIN_TEXTDOMAIN); ?>";
			var tmm_db_migrate_lang6 = "<?php _e('Import finished. Count of tables:', TMM_MIGRATE_PLUGIN_TEXTDOMAIN); ?>";
			var tmm_db_migrate_lang7 = "<?php _e('Are you sure? All content will be rewritten by the demo content if you confirm!', 'tmm_db_migrate'); ?>";
		</script>
		<?php
	}

	public function draw_html($view, $data = array()) {
		@extract($data);
		ob_start();
		include($this->get_application_path() . '/views/' . $view . '.php');
		return ob_get_clean();
	}

}


if (is_admin()) {
	add_action('init', array(new TMM_MigratePlugin(), 'init'), 999);
}
add_action('admin_enqueue_scripts', array('TMM_MigratePlugin', 'draw_options_page'));
