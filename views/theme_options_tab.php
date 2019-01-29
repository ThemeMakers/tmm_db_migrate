<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<style type="text/css">
	.option  .explain.red, .red {color:red}
	.option  .explain.blue, .blue {color:blue}

	#fountainTextG{
		width:190px;
		margin:auto;
	}

	.fountainTextG{
		color:rgb(0,0,0);
		font-family:Arial;
		font-size:42px;
		text-decoration:none;
		font-weight:normal;
		font-style:normal;
		float:left;
		animation-name:bounce_fountainTextG;
		-o-animation-name:bounce_fountainTextG;
		-ms-animation-name:bounce_fountainTextG;
		-webkit-animation-name:bounce_fountainTextG;
		-moz-animation-name:bounce_fountainTextG;
		animation-duration:2.09s;
		-o-animation-duration:2.09s;
		-ms-animation-duration:2.09s;
		-webkit-animation-duration:2.09s;
		-moz-animation-duration:2.09s;
		animation-iteration-count:infinite;
		-o-animation-iteration-count:infinite;
		-ms-animation-iteration-count:infinite;
		-webkit-animation-iteration-count:infinite;
		-moz-animation-iteration-count:infinite;
		animation-direction:normal;
		-o-animation-direction:normal;
		-ms-animation-direction:normal;
		-webkit-animation-direction:normal;
		-moz-animation-direction:normal;
		transform:scale(.5);
		-o-transform:scale(.5);
		-ms-transform:scale(.5);
		-webkit-transform:scale(.5);
		-moz-transform:scale(.5);
	}#fountainTextG_1{
		 animation-delay:0.75s;
		 -o-animation-delay:0.75s;
		 -ms-animation-delay:0.75s;
		 -webkit-animation-delay:0.75s;
		 -moz-animation-delay:0.75s;
	 }
	#fountainTextG_2{
		animation-delay:0.9s;
		-o-animation-delay:0.9s;
		-ms-animation-delay:0.9s;
		-webkit-animation-delay:0.9s;
		-moz-animation-delay:0.9s;
	}
	#fountainTextG_3{
		animation-delay:1.05s;
		-o-animation-delay:1.05s;
		-ms-animation-delay:1.05s;
		-webkit-animation-delay:1.05s;
		-moz-animation-delay:1.05s;
	}
	#fountainTextG_4{
		animation-delay:1.2s;
		-o-animation-delay:1.2s;
		-ms-animation-delay:1.2s;
		-webkit-animation-delay:1.2s;
		-moz-animation-delay:1.2s;
	}
	#fountainTextG_5{
		animation-delay:1.35s;
		-o-animation-delay:1.35s;
		-ms-animation-delay:1.35s;
		-webkit-animation-delay:1.35s;
		-moz-animation-delay:1.35s;
	}
	#fountainTextG_6{
		animation-delay:1.5s;
		-o-animation-delay:1.5s;
		-ms-animation-delay:1.5s;
		-webkit-animation-delay:1.5s;
		-moz-animation-delay:1.5s;
	}
	#fountainTextG_7{
		animation-delay:1.64s;
		-o-animation-delay:1.64s;
		-ms-animation-delay:1.64s;
		-webkit-animation-delay:1.64s;
		-moz-animation-delay:1.64s;
	}
	#fountainTextG_8{
		animation-delay:1.79s;
		-o-animation-delay:1.79s;
		-ms-animation-delay:1.79s;
		-webkit-animation-delay:1.79s;
		-moz-animation-delay:1.79s;
	}
	#fountainTextG_9{
		animation-delay:1.94s;
		-o-animation-delay:1.94s;
		-ms-animation-delay:1.94s;
		-webkit-animation-delay:1.94s;
		-moz-animation-delay:1.94s;
	}
	#fountainTextG_10{
		animation-delay:2.09s;
		-o-animation-delay:2.09s;
		-ms-animation-delay:2.09s;
		-webkit-animation-delay:2.09s;
		-moz-animation-delay:2.09s;
	}

	@keyframes bounce_fountainTextG{
		0%{
			transform:scale(1);
			color:rgb(0,0,0);
		}

		100%{
			transform:scale(.5);
			color:rgb(255,255,255);
		}
	}

	@-o-keyframes bounce_fountainTextG{
		0%{
			-o-transform:scale(1);
			color:rgb(0,0,0);
		}

		100%{
			-o-transform:scale(.5);
			color:rgb(255,255,255);
		}
	}

	@-ms-keyframes bounce_fountainTextG{
		0%{
			-ms-transform:scale(1);
			color:rgb(0,0,0);
		}

		100%{
			-ms-transform:scale(.5);
			color:rgb(255,255,255);
		}
	}

	@-webkit-keyframes bounce_fountainTextG{
		0%{
			-webkit-transform:scale(1);
			color:rgb(0,0,0);
		}

		100%{
			-webkit-transform:scale(.5);
			color:rgb(255,255,255);
		}
	}

	@-moz-keyframes bounce_fountainTextG{
		0%{
			-moz-transform:scale(1);
			color:rgb(0,0,0);
		}

		100%{
			-moz-transform:scale(.5);
			color:rgb(255,255,255);
		}
	}
</style>

<h2 class="section-title"><?php esc_html_e('Demo Data Installation', 'tmm_db_migrate'); ?></h2>

<div class="option">

	<div class="controls alternative">

		<div class="option">
			<input  id="demodata" type="radio" class="showhide" data-show-hide="demo_data" name="install_data" value="0" checked="checked" />
			<label for="demodata"><span></span><?php esc_html_e('Demo Data Install', 'tmm_db_migrate'); ?></label>
			<input  id="carproducers" type="radio" class="showhide" data-show-hide="carproducers_data" name="install_data" value="1" />
			<label for="carproducers"><span></span><?php esc_html_e('Import Carproducers', 'tmm_db_migrate'); ?></label>
		</div>

		<ul  class="show-hide-items">

			<li class="demo_data">
				<a href="#" class="button button-primary button-large" id="button_prepare_import_data"><?php esc_html_e('Demo Data Install', 'tmm_db_migrate'); ?></a>
			</li>
			<li class="carproducers_data" style="display:none;">
				<a href="#" class="button button-primary button-large" id="button_import_carproducers"><?php esc_html_e('Import Carproducers', 'tmm_db_migrate'); ?></a>
			</li>
		</ul>
	</div>

	<div class="explain alternative">

		<?php
		$count_posts = wp_count_posts();
		$count_pages = wp_count_posts('page');
		$published_posts = $count_posts->publish;
		$published_pages = $count_pages->publish;

		if ($published_posts > 3  || $published_pages > 3) { ?>

			<h3 class="red"><?php esc_html_e('Important Notice:', 'tmm_db_migrate'); ?></h3>
			<p class="red big"><?php esc_html_e('We just defined there are some posts/pages already there on your website, therefore it is not a clean WordPress Installation!', 'tmm_db_migrate'); ?></p>
			<p class="big"><?php esc_html_e('Please note, that your current database(all your content) will be overwritten after clicking "Demo Data Install" button and there is no way to revert it back, so we would kindly ask you making a database backup before installing demo content.', 'tmm_db_migrate'); ?></p>

		<?php } else { ?>

			<h3 class="green"><?php esc_html_e('Everything is fine.', 'tmm_db_migrate'); ?><br/><?php esc_html_e('You are ready to go...', 'tmm_db_migrate'); ?></h3>

		<?php } ?>

	</div>

	</div>


<ul class="show-hide-items">

	<li class="demo_data">

	<?php
	TMM_OptionsHelper::draw_theme_option(array(
		'title' => esc_html__('Import Attachments', 'tmm_db_migrate'),
		'type' => 'checkbox',
		'name' => 'tmm_migrate_upload_attachments',
		'default_value' => 1,
		'value' => 1,
		'css_class' => '',
		'description' => esc_html__('Download and import file attachments (images, videos, audios)', 'tmm_db_migrate')
	));

//	TMM_OptionsHelper::draw_theme_option(array(
//		'title' => esc_html__('Backup DB', 'tmm_db_migrate'),
//		'type' => 'checkbox',
//		'name' => 'tmm_migrate_backup',
//		'default_value' => 1,
//		'value' => 1,
//		'css_class' => '',
//		'description' => esc_html__('Backup your database content before importing. Placed in ', 'tmm_db_migrate') . "'/uploads/tmm_backup/'"
//	));
	?>

	</li>
</ul>

<ul id="tmm_db_migrate_process_imp"></ul>

<!--
<hr>
<br><br>

<h2 class="section-title"><?php esc_html_e('Export Data', 'tmm_db_migrate'); ?></h2>

<div class="option">

	<div class="controls alternative">

		<a href="#" class="button button-primary button-large" id="button_prepare_export_data"><?php esc_html_e('Export Data', 'tmm_db_migrate'); ?></a>

		<ul id="tmm_db_migrate_process"></ul>

	</div>

	<div class="explain alternative">

		<p><?php esc_html_e('In Case you need to transfer your website to another domain the easiest way to export all the data is here.', 'tmm_db_migrate'); ?></p>
		<p><?php esc_html_e('Video guide on how to do that properly is coming soon...', 'tmm_db_migrate'); ?></p>

	</div>

</div>

-->

<hr>

<h2><?php esc_html_e('Import Locations', 'tmm_db_migrate'); ?></h2>
<?php
TMM_OptionsHelper::draw_theme_option(array(
	'name' => 'locations_zip',
	'type' => 'upload_zip',
	'default_value' => '',
	'description' => esc_html__('Only zip files.<br> While importing big countries you need to login to increase the server execution time to 300 sec (max_execution_time in PHP settings) and increase the  memory_limit to  128M as well.', 'tmm_db_migrate'),
	'id' => 'upload_locations',
));
?>

<?php
$memory = size_format( wp_convert_hr_to_bytes( WP_MEMORY_LIMIT ) );
$time = ini_get('max_execution_time');
?>

<hr>

<h2><?php esc_html_e('Server Info', 'tmm_db_migrate'); ?></h2>

<div class="option">
	<div class="controls"><?php esc_html_e( 'WP Memory Limit', 'tmm_db_migrate' ); ?></div>
	<div class="explain<?php echo (int) $memory < 128 ? ' red' : ''; ?>">
		<?php echo $memory; ?>
		<?php
		if ( (int) $memory < 128 ) {
			echo '<br>' . esc_html__( 'You need to increase the server memory limit up to 128MB. See: ', 'tmm_db_migrate' ) .
			     '<a class="blue" href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' .
			        esc_html__( 'Increasing memory allocated to PHP', 'tmm_db_migrate' ) .
			     '</a>';
		}
		?>
	</div>
</div>

<div class="option">
	<div class="controls"><?php esc_html_e( 'PHP Max Execution Time', 'tmm_db_migrate' ); ?></div>
	<div class="explain<?php echo (int) $time < 300 ? ' red' : ''; ?>">
		<?php echo $time; ?>
		<?php
		if ( (int) $time < 300 ) {
			echo '<br>';
			esc_html_e( 'You need to increase the server waiting time to 300 sec', 'tmm_db_migrate' );
		}
		?>
	</div>
</div>

<div class="option">
	<div class="controls"><?php esc_html_e( 'PHP Max Input Vars', 'tmm_db_migrate' ); ?></div>
	<div class="explain"><?php echo ini_get('max_input_vars'); ?></div>
</div>

<div class="option">
	<div class="controls"><?php esc_html_e( 'PHP Post Max Size', 'tmm_db_migrate' ); ?></div>
	<div class="explain"><?php echo size_format( wp_convert_hr_to_bytes( ini_get('post_max_size') ) ); ?></div>
</div>

<div class="option">
	<div class="controls"><?php esc_html_e( 'WP Max Upload File Size', 'tmm_db_migrate' ); ?></div>
	<div class="explain"><?php echo esc_html( size_format( wp_max_upload_size() ) );	?></div>
</div>
