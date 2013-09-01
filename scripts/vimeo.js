// By Chris Coyier & tweaked by Mathias Bynens

$(function() {

	// Find all YouTube videos
	var $Vimeo = $("iframe[src^='http://player.vimeo.com']"),

	    // The element that is fluid width
	    $fluidEl = $("#main");

	// Figure out and save aspect ratio for each video
	$Vimeo.each(function() {

		$(this)
			.data('aspectRatio', this.height / this.width)
			
			// and remove the hard coded width/height
			.removeAttr('height')
			.removeAttr('width');

	});

	// When the window is resized
	// (You'll probably want to debounce this)
	$(window).resize(function() {

		var newWidth = $fluidEl.width();
		
		// Resize all videos according to their own aspect ratio
		$Vimeo.each(function() {

			var $el = $(this);
			$el
				.width(newWidth)
				.height(newWidth * $el.data('aspectRatio'));

		});

	// Kick off one resize to fix all videos on page load
	}).resize();

});