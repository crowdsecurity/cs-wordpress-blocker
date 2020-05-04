<div class="wrap">
	<h1>Crowdsec Plugin Configuration</h1>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
	</ul>
	<?php
	if (get_option("cache_successfully_refreshed") === "1") {
		echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">';
		echo '<p><strong>Cache is now empty!</strong></p>';
		echo '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';
		update_option("cache_successfully_refreshed", "0");
	}
	?>
	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
		<form method="post" action="options.php">
				<?php 
					settings_fields( 'crowdsec_plugin_settings' );
					do_settings_sections( 'crowdsec_settings' );
				?>
				<?php
					submit_button();
				?>
		</form>
		
		<br/>
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
			<input type="hidden" name="action" value="refresh_cache">
			<input type="submit" value="Refresh Cache" class="button button-primary">
		</form>
		</div>
	</div>
</div>