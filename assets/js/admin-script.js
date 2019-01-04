jQuery( document ).ready( function($) {

	//Fitler sites
	$( '.approval' ).on( 'click', function(e) {
		e.preventDefault();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'approve_site',
				approve_site: $(this).attr("data-title")
			},
			success: function( data ) {
				if ( data.success === true ) {
					console.log(data.data.approvedSite);
					console.log(data.success);
					console.log('working!');
				}
			},
		});

		$(this).parent().remove();
	});

});