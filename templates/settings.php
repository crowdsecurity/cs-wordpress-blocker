<div class="wrap">
	<h1>Crowdsec Plugin</h1>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">

			<form method="post" action="options.php">
				<?php 
					$options = get_option( 'crowdsec_api_token' );
					echo "<h2> " . $options . '</h2>';
					settings_fields( 'crowdsec_plugin_settings' );
					do_settings_sections( 'crowdsec_settings' );
					submit_button();
				?>
			</form>
			
		</div>
	</div>
</div>