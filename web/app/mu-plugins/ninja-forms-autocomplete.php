<?php

function hcommons_ninja_forms_autocomplete() {
?>
	<iframe name="ninja-forms-autocomplete" style="display:none" src="https://about:blank"></iframe>
	<script>
		jQuery( function( $ ) {
			var HcNfAutocomplete = Marionette.Object.extend( {
				initialize: function() {
					this.listenTo( Backbone.Radio.channel( 'forms' ), 'submit:response', this.actionSubmit );
				},
				actionSubmit: function( response ) {
					$( '.nf-form-wrap form' )
						.attr( 'target', 'ninja-forms-autocomplete' )
						.attr( 'action', ajaxurl )
						[0].submit();
				},
			});
			new HcNfAutocomplete();
		} );
	</script>
<?php
}
add_action( 'wp_footer', 'hcommons_ninja_forms_autocomplete', 100 );
