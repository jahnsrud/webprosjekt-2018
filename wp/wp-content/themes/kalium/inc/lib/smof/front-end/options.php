<?php
$saved_text = 'Settings Saved';
?>
<div class="wrap" id="of_container">

	<div id="of-popup-save" class="of-save-popup">
		<div class="of-save-save"><i class="fa fa-thumbs-up"></i> Options Updated</div>
	</div>

	<div id="of-popup-reset" class="of-save-popup">
		<div class="of-save-reset"><i class="fa fa-refresh"></i> Options Reset</div>
	</div>

	<div id="of-popup-fail" class="of-save-popup">
		<div class="of-save-fail"><i class="fa fa-times-circle"></i> Error!</div>
	</div>

	<span style="display: none;" id="hooks"><?php echo json_encode(of_get_header_classes_array()); ?></span>
	<input type="hidden" id="reset" value="<?php if(isset($_REQUEST['reset'])) echo $_REQUEST['reset']; ?>" />
	<input type="hidden" id="security" name="security" value="<?php echo wp_create_nonce('of_ajax_nonce'); ?>" />

	<form id="of_form" method="post" action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ) ?>" enctype="multipart/form-data" >

		<div id="header">

			<div class="logo">
				<h2>
					<?php echo THEMENAME; ?>
					<a href="<?php echo admin_url( 'admin.php?page=laborator-about' ); ?>" class="theme_version"><?php echo kalium()->getVersion(); ?></a>
				</h2>
				
					<?php if ( laborator_is_holiday_season() ) : ?>
					<div class="holidays-pine"></div>
					<?php endif; ?>
			</div>

			<div id="js-warning">Warning: This options panel will not work properly without javascript!</div>
			<a href="https://laborator.co" target="_blank" class="icon-option"></a>
			<div class="clear"></div>

    	</div>

		<div id="info_bar" class="hidden">

			<a>
				<div id="expand_options" class="expand">Expand</div>
			</a>

			<img style="display:none" src="<?php echo ADMIN_DIR; ?>assets/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />

			<button id="of_save" type="button" class="button-primary">
				<span class="loading-spinner">
					<i class="fa fa-refresh fa-spin"></i>
				</span>
				<em data-success="<?php echo $saved_text; ?>">Save All Changes</em>
			</button>

		</div><!--.info_bar-->

		<div id="main">

			<div id="of-nav">
				<ul>
				  <?php echo $options_machine->Menu ?>
				</ul>
			</div>

			<div id="content">
		  		<?php echo $options_machine->Inputs /* Settings */ ?>
		  	</div>

			<div class="clear"></div>
			
			<a href="#of_save" class="of-save-sticky">
				<i class="fa fa-save"></i>
				<i class="fa fa-refresh fa-spin"></i>
				<span class="save-text">Save All Changes</span>
			</a>

		</div>

		<div class="save_bar">

			<img style="display:none" src="<?php echo ADMIN_DIR; ?>assets/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
			<button id ="of_save" type="button" class="button-primary">
				<span class="loading-spinner">
					<i class="fa fa-refresh fa-spin"></i>
				</span>
				<em data-success="<?php echo $saved_text; ?>">Save All Changes</em>
			</button>
			<button id ="of_reset" type="button" class="button submit-button reset-button">Options Reset</button>
			<img style="display:none" src="<?php echo ADMIN_DIR; ?>assets/images/loading-bottom.gif" class="ajax-reset-loading-img ajax-loading-img-bottom" alt="Working..." />

		</div><!--.save_bar-->

	</form>

	<div style="clear:both;"></div>

</div><!--wrap-->
<div class="smof_footer_info hidden">Slightly Modified Options Framework <strong><?php echo SMOF_VERSION; ?></strong></div>