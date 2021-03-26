var timer;														// Global variable to call masonry

$(document).ready(function() {

    // This script adds clicks to each website clicked and hence increases its ranking in the search engine

    $(".result").on("click", function() {						// When clicking a result it adds 1 click to database (works with increaseLinkClicks)
		
		var id = $(this).attr("data-linkId");
		var url = $(this).attr("href");

		if(!id) {
			alert("data-linkId attribute not found");
		}

		increaseLinkClicks(id, url);

		return false;
	});

	// Masonry image Section

	var grid = $(".imageResults");

	grid.on("layoutComplete", function() {

		$(".gridItem img").css("visibility", "visible");

	});

	grid.masonry({

		itemSelector: ".gridItem",
		columnWidth: 200,
		gutter: 5,
		fitWidth: true,
		isInitLayout: false

	});

	$("[data-fancybox]").fancybox( {							// Image display - click on image and fancybox displays

		caption : function( instance, item ) {
			var caption = $(this).data('caption') || '';
			var siteUrl = $(this).data('siteurl') || '';
	
			if ( item.type === 'image' ) {
				caption = (caption.length ? caption + '<br />' : '') 
				+ '<a href="' + item.src + '">View Image</a><br>' 
				+ '<a href="' + siteUrl + '">Visit Page</a>';
			}
	
			return caption;
		},

		afterShow : function( instance, item ) {				// After clickin on image - Add + 1 clicks to image in database
		
			increaseImageClicks(item.src);

		}

	});

});			// End of (document).ready(function()


function loadImage(src, className) {										// Load masonry images and sets broken images to "broken" in database

	var image = $("<img>");

	image.on("load", function() {
		
		$("." + className + " a").append(image);

		clearTimeout(timer);												// Clears timer

		timer = setTimeout(function() {

			$(".imageResults").masonry();

		}, 200);															// After 500 milliseconds call masonry to call images

	});

	image.on("error", function() {

		$("." + className).remove();

		$.post("Ajax/setBroken.php", {src: src});

	});

	image.attr("src", src);

}	// End of loadImage function


function increaseLinkClicks(linkId, url) {							// When clicking a result it adds 1 click to database

	$.post("ajax/updateLinkCount.php", {linkId: linkId})
	.done(function(result) {
		if(result != "") {
			alert(result);
			return;
		}

		window.location.href = url;
	});

}	// End of increaseLinkClicks function

function increaseImageClicks(imageUrl) {							// When clicking a result it adds 1 click to database

	$.post("ajax/updateImageCount.php", {imageUrl: imageUrl})
	.done(function(result) {
		if(result != "") {
			alert(result);
			return;
		}

	});

}	// End of increaseImageClicks function