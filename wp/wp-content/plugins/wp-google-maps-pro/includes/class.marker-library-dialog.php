<?php

namespace WPGMZA;

class MarkerLibraryDialog
{
	protected $icons;
	
	public function __construct()
	{
		wp_enqueue_script('remodal', plugin_dir_url(__DIR__) . 'lib/remodal.min.js');
		wp_enqueue_style('remodal', plugin_dir_url(__DIR__) . 'lib/remodal.css');
		wp_enqueue_style('remodal-default-theme', plugin_dir_url(__DIR__) . 'lib/remodal-default-theme.css');
		
		wp_enqueue_script('wpgmza-marker-library-dialog', plugin_dir_url(__DIR__) . 'js/marker-library-dialog.js');
		wp_enqueue_style('wpgmza-marker-library-dialog', plugin_dir_url(__DIR__) . 'css/marker-library-dialog.css');
	}
	
	public function html()
	{
		?>
		<div class="wpgmza-marker-library-dialog">
			<button data-remodal-action="close" class="remodal-close"></button>
			<span style="float: right;">Powered by <a style="font-family: Roboto, sans-serif;" href="https://www.mappity.org">mappity.org</a></span>
			<iframe id="mappity" src="https://www.mappity.org?wpgmza-embed=1"></iframe>
		</div>
		<?php
	}
}
