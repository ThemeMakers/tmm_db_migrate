<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<h2 class="section-title"><?php _e('Demo Data Installation', 'tmm_db_migrate'); ?></h2>

<?php
TMM_OptionsHelper::draw_theme_option(array(
	'title' => __('Import Attachments', 'tmm_db_migrate'),
	'type' => 'checkbox',
	'name' => 'tmm_migrate_upload_attachments',
	'default_value' => 1,
	'value' => 1,
	'css_class' => '',
	'description' => __('Download and import file attachments (images, videos, audios)', 'tmm_db_migrate')
));
?>

<?php
TMM_OptionsHelper::draw_theme_option(array(
	'title' => __('Backup DB', 'tmm_db_migrate'),
	'type' => 'checkbox',
	'name' => 'tmm_migrate_backup',
	'default_value' => 1,
	'value' => 1,
	'css_class' => '',
	'description' => __('Backup your database content before importing. Placed in ', 'tmm_db_migrate') . "'/uploads/tmm_backup/'"
));
?>

<?php
global $tmm_demo_list;
$def_demo = array_keys($tmm_demo_list);
$demo_list = array();

foreach ($tmm_demo_list as $id => $attr) {
	$demo_list[$id] = $attr['title'];
}

TMM_OptionsHelper::draw_theme_option(array(
	'title' => __('Choose Demo', 'tmm_db_migrate'),
	'type' => 'select',
	'name' => 'tmm_migrate_demo',
	'css_class' => 'tmm_migrate_demo',
	'default_value' => $def_demo[0],
	'values' => $demo_list,
	'description' => ''
));
?>

<img id="tmm_demo_preview" style="display:block;max-width: 100%" src="<?php echo TMM_MIGRATE_URL . 'demo_data/' . $def_demo[0] . '/screenshot.png' ?>" alt="<?php echo $demo_list[$def_demo[0]] ?>" />

<br><br>

<div class="option">

	<div class="controls alternative">

		<a href="#" class="button button-primary button-large" id="button_prepare_import_data"><?php _e('Install Demo Data', 'tmm_db_migrate'); ?></a>

	</div>

	<div class="explain alternative">

		<?php
		$count_posts = wp_count_posts();
		$count_pages = wp_count_posts('page');
		$published_posts = $count_posts->publish;
		$published_pages = $count_pages->publish;

		if ($published_posts > 3  || $published_pages > 3) { ?>

			<h3 class="red"><?php _e('Important Notice:', 'tmm_db_migrate'); ?></h3>
			<p class="red big"><?php _e('We just defined there are some posts/pages already there on your website, therefore it is not a clean WordPress Installation!', 'tmm_db_migrate'); ?></p>
			<p class="big"><?php _e('Please note, that your current database(all your content) will be overwritten after clicking "Demo Data Install" button and there is no way to revert it back, so we would kindly ask you making a database backup before installing demo content.', 'tmm_db_migrate'); ?></p>

		<?php } else { ?>

			<h3 class="green"><?php _e('Everything is fine.', 'tmm_db_migrate'); ?><br/><?php _e('You are ready to go...', 'tmm_db_migrate'); ?></h3>

		<?php } ?>

	</div>

</div>

<ul id="tmm_db_migrate_process_imp"></ul>

<hr>
<br><br>

<h2 class="section-title"><?php _e('Export Data', 'tmm_db_migrate'); ?></h2>

<div class="option">

	<div class="controls alternative">

		<a href="#" class="button button-primary button-large" id="button_prepare_export_data"><?php _e('Export Data', 'tmm_db_migrate'); ?></a>

		<ul id="tmm_db_migrate_process"></ul>

	</div>

	<div class="explain alternative">

		<p><?php _e('In Case you need to transfer your website to another domain the easiest way to export all the data is here.', 'tmm_db_migrate'); ?></p>
		<p><?php _e('Video guide on how to do that properly is coming soon...', 'tmm_db_migrate'); ?></p>

	</div>

</div>