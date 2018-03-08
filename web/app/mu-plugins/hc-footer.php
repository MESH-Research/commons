<?php


/**
 * append some text to the bottom of any/all themes to tell users about HC and its networks
 */
function hcommons_wp_footer() {
	function is_society_blog() {
		$society_blog_ids = [
			constant( 'HC_ROOT_BLOG_ID' ),
			constant( 'UP_ROOT_BLOG_ID' ),
			constant( 'AJS_ROOT_BLOG_ID' ),
			constant( 'ASEEES_ROOT_BLOG_ID' ),
			constant( 'CAA_ROOT_BLOG_ID' ),
			constant( 'MLA_ROOT_BLOG_ID' ),
		];

		return in_array( (string) get_current_blog_id(), $society_blog_ids );
	}

	if (
		class_exists( 'Humanities_Commons' ) &&
		! empty( Humanities_Commons::$society_id ) &&
		! is_society_blog()
	) {
		$main_site_domain = Humanities_Commons::$main_site->domain;
		$society_id = Humanities_Commons::$society_id;

		$society_url = sprintf(
			'https://%s%s',
			( 'hc' === $society_id ) ? '' : $society_id . '.',
			$main_site_domain
		);

		$theme = wp_get_theme();
		$theme_name = $theme->get( 'TextDomain' );

        if( 'twentyfifteen' === $theme_name ) {

		$style = implode( ';', [
			'background-color: white',
			'color: black',
			'position: relative',
			'text-align: center',
			'width: 100%',
			'z-index: 100',
		] );

		} else {
			$style = implode( ';', [
			'background-color: white',
			'color: black',
			'line-height: 3em',
			'position: relative',
			'text-align: center',
			'width: 100%',
			'z-index: 100',
		] );
		}

		$text = sprintf(
			'<div id="hcommons-network-footer" style="%s">This site is part of %s<em><a href="%s">Humanities Commons</a></em>. <a href="%s">Explore other sites on this network</a> or <a href="%s">register to build your own</a>.</div>',
			$style,
			( 'hc' === $society_id ) ? '' : sprintf( 'the %s network on ', strtoupper( $society_id ) ),
			'https://' . $main_site_domain,
			trailingslashit( $society_url ) . 'sites',
			$society_url
		);

		// fix commentpress
		$script = '<script>jQuery(".cp_sidebar_toc #hcommons-network-footer").appendTo("#footer").css({"line-height": "2em"});</script>';

		echo $text . $script;
	}
}
add_action( 'wp_footer', 'hcommons_wp_footer' );
