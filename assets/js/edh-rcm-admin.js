( function() {
	var radios = document.querySelectorAll( 'input[name="edh_recent_posts_taxonomy"]' );
	var rowCat = document.getElementById( 'edh-rp-row-category' );
	var rowTag = document.getElementById( 'edh-rp-row-tag' );

	function toggle( val ) {
		rowCat.style.display = ( 'category' === val ) ? '' : 'none';
		rowTag.style.display = ( 'post_tag' === val ) ? '' : 'none';
	}

	radios.forEach( function( radio ) {
		radio.addEventListener( 'change', function() {
			toggle( this.value );
		} );
	} );
} )();
