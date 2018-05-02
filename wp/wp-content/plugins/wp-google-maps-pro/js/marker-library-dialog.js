/**
 * @namespace WPGMZA
 * @module MarkerLibraryDialog
 * @requires WPGMZA
 */
(function($) {

	var searchTimeoutID, lazyLoaded, currentCallback;
	
	if(!window.WPGMZA)
		window.WPGMZA = {};
	
	WPGMZA.MarkerLibraryDialog = function(element)
	{
		var self = this;
		
		this.element = element;
		
		$(element).remodal();
		
		window.addEventListener("message", function(event) {
			
			if(event.data.action != "download_marker")
				return;
			
			$.ajax({
				
				url: ajaxurl,
				type: "POST",
				data: {
					action: "wpgmza_upload_base64_image",
					data: event.data.data,
					mimeType: "image/png"
				},
				success: function(data, status, xhr) {
					var url = data.url;
					currentCallback(url);
					$(self.element).remodal().close();
				}
				
			});
			
		}, false);
	}
	
	WPGMZA.MarkerLibraryDialog.prototype.open = function(callback)
	{
		currentCallback = callback;
		
		$(this.element).remodal().open();
	}
	
	WPGMZA.MarkerLibraryDialog.prototype.onSearch = function()
	{
		// Escape special regex characters and build regex
		var string = this.searchInput.val().replace(/[-\\^$*+?.()|[\]{}]/g, '\\$&');
		var regexp = new RegExp(string, "i");
		
		$(this.element).find("img").each(function(index, img) {
			var li = $(img).closest("li");
			var filename = $(img).attr("title").replace(/\.png$/, "");
			
			if(string.length && !filename.match(regexp))
				$(li).addClass("wpgmza-marker-library-no-result");
			else
				$(li).removeClass("wpgmza-marker-library-no-result");
		});
	}
	
	WPGMZA.MarkerLibraryDialog.prototype.onIconSelected = function(event)
	{
		currentCallback(event.target.src);
		$(this.element).remodal().close();
	}
	
	$(document).ready(function(event) {
		
		var el = $(".wpgmza-marker-library-dialog");
		
		if(!el.length)
			return;
		
		WPGMZA.markerLibraryDialog = new WPGMZA.MarkerLibraryDialog(el);
		
		function bindButtonClickHandler(button)
		{
			var targetName = $(button).attr("data-target-name");
			var target = $("[name='" + targetName + "']");
			var preview = $(target).parent().find("img");
			
			// Elements are layed out differently on the category edit page
			if(window.location.href.match(/categories&action=edit/))
				preview = $("#wpgmza_mm>img");
			
			$(button).on("click", function() {
				WPGMZA.markerLibraryDialog.open(function(src) {
					target.val(src);
					target.change();
					
					preview.attr("src", src);
					
					if(!$(button).hasClass('wpgmza-marker-directions-library'))
						$("#wpgmza_cmm>img").attr("src", src);
				});
			});
		}
		
		$("input.wpgmza-marker-library").each(function(index, el) {
			bindButtonClickHandler(el);
		});
		
	});
	
})(jQuery);