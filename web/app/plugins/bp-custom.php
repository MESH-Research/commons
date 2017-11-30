<?php

load_plugin_textdomain( 'buddypress-sitewide-activity-widget', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

load_plugin_textdomain( 'invite-anyone', false, dirname( basename( __FILE__ ) ). '/languages/' );

// prevent users from seeing one another's followers (can only see their own)
// unfortunately there's no filter to prevent running the query, but we can at least empty the result before rendering
function hcommons_filter_get_followers( $followers ) {
	if ( bp_displayed_user_id() !== get_current_user_id() ) {
		$followers = [];
	}
	return $followers;
}
add_filter( 'bp_follow_get_followers', 'hcommons_filter_get_followers' );

function hcommons_filter_groups_activity_new_update_action( $activity_action ) {
	$activity_action = preg_replace( '/(in the group <a href="[^"]*)(">)/', '\1activity\2', $activity_action );
	return $activity_action;
}
add_filter( 'groups_activity_new_update_action', 'hcommons_filter_groups_activity_new_update_action' );


/**
 * remove urls in comments from comment notification emails so that we don't trigger spam filters
 */
function hcommons_filter_comment_notification_text( $text ) {
	$delimiter = 'You can see all comments on this post here:';
	$exploded_text = explode( $delimiter, $text );

	// http://stackoverflow.com/a/6165666/700113
	$pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
	$replace = '<url removed>';
	$exploded_text[0] = preg_replace( $pattern, $replace, $exploded_text[0] );

	$text = $exploded_text[0] . $delimiter . $exploded_text[1];

	return $text;
}
add_filter( 'comment_notification_text', 'hcommons_filter_comment_notification_text' );

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

		$style = implode( ';', [
			'background-color: white',
			'color: black',
			'line-height: 3em',
			'position: relative',
			'text-align: center',
			'width: 100%',
			'z-index: 100',
		] );

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

/**
 * Comments are not nested correctly if caching is on, so disable it for commentpress.
 */
function hcommons_selectively_disable_object_cache() {
	$theme = wp_get_theme();

	if ( false !== strpos( strtolower( $theme->get( 'Name' ) ), 'commentpress' ) ) {
		wp_cache_add_non_persistent_groups( array( 'comment' ) );
	}
}
add_action( 'plugins_loaded', 'hcommons_selectively_disable_object_cache' );


// shibboleth attempts to put users back where they came from after authenticating with the redirect_to param.
// that param is not always preserved through the login flow, so handle it here with a cookie to be sure.
function hcommons_maybe_redirect_after_login() {
	// Some pages need to be excluded from being redirect targets.
	$is_blacklisted = function() {
		$blacklist = [
			'/',
			'/clear-session/',
			'/logged-out/',
			'/not-a-member/',
			'/wp-admin/admin-ajax.php',
		];

		return (
			in_array( $_SERVER['REQUEST_URI'], $blacklist ) ||
			false !== strpos( $_SERVER['REQUEST_URI'], '/wp-login.php' ) ||
			false !== strpos( $_SERVER['REQUEST_URI'], '/wp-json/' )
		);
	};

	$param_name = 'redirect_to';
	$cookie_name = $param_name;

	// Once user has authenticated, maybe redirect to original destination.
	if ( is_user_logged_in() ) {

		// unset cookie on each network domains
		foreach ( get_networks() as $network ) {
			setcookie( $cookie_name, '', time() - YEAR_IN_SECONDS, COOKIEPATH, $network->cookie_domain );
		}

		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			// only redirect if we're not already there
			if ( false === strpos( $_COOKIE[ $cookie_name ], $_SERVER['REQUEST_URI'] ) ) {
				// Can't use wp_safe_redirect due to filters, just send directly.
				header( 'Location: ' . $_COOKIE[ $cookie_name ] );
				exit;
			}
		}

	// Otherwise, as long as this isn't a blacklisted page, set cookie.
	} else if ( ! $is_blacklisted() ) {
		// Direct access to protected group docs is handled with another redirect, leave as-is.
		if (
			! isset( $_COOKIE['bp-message'] ) ||
			! preg_match( '/You must be a logged-in member/', $_COOKIE['bp-message'] )
		) {
			$cookie_value = isset( $_REQUEST[ $param_name ] ) ? $_REQUEST[ $param_name ] : get_site_url() . $_SERVER['REQUEST_URI'];

			setcookie( $cookie_name, $cookie_value, time() + MINUTE_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}

		// No need to set duplicate cookies, once we set one we're done with this request.
		remove_action( 'bp_do_404', __METHOD__ );
		remove_action( 'init', __METHOD__, 15 );
		remove_action( 'wp', __METHOD__ );
	}
}
if ( ! ( defined( 'DOMAIN_MAPPING' ) && DOMAIN_MAPPING ) ) {
	// bp_do_404 runs before wp, so needs an additional hook to set cookie for hidden content etc.
	add_action( 'bp_do_404', 'hcommons_maybe_redirect_after_login' );
	// priority 15 to allow shibboleth_auto_login() to run first
	add_action( 'init', 'hcommons_maybe_redirect_after_login', 15 );
	// to catch cookies set by cac_catch_group_doc_request
	add_action( 'wp', 'hcommons_maybe_redirect_after_login' );
}


/**
 * use mapped domain rather than the internal domain when possible
 * intended to make all links to style center use style.mla.org
 */
function hcommons_filter_get_blog_permalink( $permalink ) {
	global $blogs_template, $wpdb;

	if ( ! isset( $wpdb->dmtable ) ) {
		$wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';
	}

	$mapped_domain = $wpdb->get_var( "SELECT domain FROM {$wpdb->dmtable} WHERE blog_id = {$blogs_template->blog->blog_id}" );

	if ( $mapped_domain ) {
		$permalink = "https://$mapped_domain";
	}

	return $permalink;
}
add_filter( 'bp_get_blog_permalink', 'hcommons_filter_get_blog_permalink' );

/**
 * filter topic permalinks to restore ?view=all where necessary.
 * that string is removed by a filter in bbpress/includes/extend/buddypress/groups.php:
 * add_filter( 'bbp_get_topic_permalink',   array( $this, 'map_topic_permalink_to_group' ), 10, 2 );
 */
function hcommons_filter_bbp_get_topic_permalink( $topic_permalink, $topic_id ) {
	// the logic to decide whether this actually gets added is handled internally by bbp_get_view_all(),
	// so here we can just call it and let bbpress decide whether to add it or not.
	return bbp_add_view_all( $topic_permalink );
}
// priority 20 to override map_topic_permalink_to_group()
add_filter( 'bbp_get_topic_permalink', 'hcommons_filter_bbp_get_topic_permalink', 20, 2 );


function hcommons_filter_wp_redirect( $url ) {
	if ( strpos( $url, 'action=bpnoaccess' ) !== false ) {
		$url = add_query_arg( array( 'action' => 'shibboleth' ), $url );
	}
	return $url;
}
add_filter( 'wp_redirect', 'hcommons_filter_wp_redirect' );

/**
 * Intercept URLs generated by buddypress-group-email-subscription to redirect
 * action=bpnoaccess to action=shibboleth.
 *
 * ...and other URLs from bookmarks, etc. - everyone should use shibboleth.
 */
function hcommons_maybe_redirect_login() {
	if (
		isset( $_REQUEST['action'] ) &&
		'shibboleth' !== $_REQUEST['action']
	) {
		$url = add_query_arg( [ 'action' => 'shibboleth' ] );
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			setcookie( 'redirect_to', $_REQUEST['redirect_to'], time() + MINUTE_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}
		wp_safe_redirect( $url );
	}
}
add_action( 'login_init', 'hcommons_maybe_redirect_login' );

/**
 * Filter the login redirect to prevent landing on wp-admin when logging in with shibboleth.
 *
 * @param string $location
 * @return string $location Modified url
 */
function hcommons_remove_admin_redirect( $location ) {
	if (
		isset( $_REQUEST['action'] ) &&
		'shibboleth' === $_REQUEST['action'] &&
		strpos( $location, 'wp-admin' ) !== false
	) {
		$location = get_site_url();
	}
	return $location;
}
add_filter( 'wp_safe_redirect_fallback', 'hcommons_remove_admin_redirect' );
add_filter( 'login_redirect', 'hcommons_remove_admin_redirect' );

/**
 * inject BP_Email into wp_mail
 */
function hcommons_filter_wp_mail( $args ) {
	extract( $args );

	// replace default footer to remove "unsubscribe" since that isn't handled for non-bp-email types
	add_action( 'bp_before_email_footer', 'ob_start', 999, 0 );
	add_action( 'bp_after_email_footer', 'ob_get_clean', -999, 0 );
	add_action( 'bp_after_email_footer', 'hcommons_email_footer' );

	// load template markup
	ob_start();
	add_filter( 'bp_locate_template_and_load', '__return_true' );
	bp_locate_template( 'assets/emails/single-bp-email.php', true, false );
	remove_filter( 'bp_locate_template_and_load', '__return_true' );
	$template = ob_get_contents();
	ob_end_clean();

	$args['message'] = bp_core_replace_tokens_in_text( $template, [
		'content' => make_clickable( nl2br( $message ) ),
		'recipient.name' => 'there', // since we don't know the user's actual name
	] );

	// wp core sets headers to a string value joined by newlines for e.g. comment notifications.
	// most plugins use/keep the array set by apply_filter( 'wp_mail' ).
	// cast to array
	if ( is_string( $args['headers'] ) ) {
		$args['headers'] = explode( "\n", $args['headers'] );
	}
	// remove existing content-type header if present
	$args['headers'] = array_filter( $args['headers'], function( $v ) {
		return strpos( strtolower( $v ), 'content-type' ) === false;
	} );
	// set html content-type
	$args['headers'][] = 'Content-Type: text/html';

	// clean up
	remove_action( 'bp_before_email_footer', 'ob_start', 999, 0 );
	remove_action( 'bp_after_email_footer', 'ob_get_clean', -999, 0 );
	remove_action( 'bp_after_email_footer', 'hcommons_email_footer' );

	return $args;
}
add_filter( 'wp_mail', 'hcommons_filter_wp_mail' );

/**
 * used in hcommons_filter_wp_mail()
 */
function hcommons_email_footer() {
	$settings = bp_email_get_appearance_settings();
	echo $settings['footer_text'];
}

/**
 * sometimes we don't want to use our html filter (e.g. bbpress has its own),
 * but there's no way to tell inside wp_mail when that's the case - this is a workaround
 */
function hcommons_unfilter_wp_mail() {
	remove_filter( 'wp_mail', 'hcommons_filter_wp_mail' );
}
add_action( 'bbp_pre_notify_subscribers', 'hcommons_unfilter_wp_mail' );
add_action( 'bbp_pre_notify_forum_subscribers', 'hcommons_unfilter_wp_mail' );
// no action available for this one, so abuse a filter instead
add_filter( 'newsletters_execute_mail_message', function( $message ) {
	hcommons_unfilter_wp_mail();
	return $message;
} );

/**
 * Set the group default tab to 'forum' if the current group has a forum
 * attached to it.
 */
function hcommons_override_cbox_set_group_default_tab( $retval ) {
        // check if bbPress or legacy forums are active and configured properly
        if ( ( function_exists( 'bbp_is_group_forums_active' ) && bbp_is_group_forums_active() ) ||
                ( function_exists( 'bp_forums_is_installed_correctly' ) && bp_forums_is_installed_correctly() ) ) {

                // if current group does not have a forum attached, stop now!
                if ( ! bp_group_is_forum_enabled( groups_get_current_group() ) ) {
                        return $retval;
                }

                // Allow non-logged-in users to view a private group's homepage.
                if ( false === is_user_logged_in() && groups_get_current_group() && 'private' === bp_get_new_group_status() ) {
                        return $retval;
                }

                // reconfigure the group's nav
                add_action( 'bp_actions', 'hcommons_override_config_group_nav', 99 );

                // finally, use 'forum' as the default group tab
                return 'home';
        }

        return $retval;
}
add_filter( 'bp_groups_default_extension', 'hcommons_override_cbox_set_group_default_tab', 100 );

/**
 * On the current group page, reconfigure the group nav when a forum is
 * enabled for the group.
 *
 * What we do here is:
 *  - move the 'Forum' tab to the beginning of the nav
 *  - rename the 'Home' tab to 'Activity'
 */
function hcommons_override_config_group_nav() {
        $group_slug = bp_current_item();

        // BP 2.6+.
        if ( function_exists( 'bp_rest_api_init' ) ) {
                buddypress()->groups->nav->edit_nav( array( 'position' => 1 ), 'forum', $group_slug );
                buddypress()->groups->nav->edit_nav( array( 'position' => 0 ), 'home', $group_slug );
                buddypress()->groups->nav->edit_nav( array( 'name' => __( 'Activity', 'buddypress' ) ), 'home', $group_slug );

        // Older versions of BP.
        } else {
                buddypress()->bp_options_nav[$group_slug]['home']['position'] = 0;
                buddypress()->bp_options_nav[$group_slug]['forum']['position'] = 1;
                buddypress()->bp_options_nav[$group_slug]['home']['name']      = __( 'Activity', 'buddypress' );
        }

}

/**
 * override core function to remove actual check on referer since we have lots of domains
 * this is an attempt to prevent "are you sure you want to do this?" errors
 */
if ( !function_exists('check_admin_referer') ) :
function check_admin_referer( $action = -1, $query_arg = '_wpnonce' ) {
	if ( -1 == $action )
		_doing_it_wrong( __FUNCTION__, __( 'You should specify a nonce action to be verified by using the first parameter.' ), '3.2.0' );

	$adminurl = strtolower(admin_url());
	$referer = strtolower(wp_get_referer());
	$result = isset($_REQUEST[$query_arg]) ? wp_verify_nonce($_REQUEST[$query_arg], $action) : false;

	/**
	 * Fires once the admin request has been validated or not.
	 *
	 * @since 1.5.1
	 *
	 * @param string    $action The nonce action.
	 * @param false|int $result False if the nonce is invalid, 1 if the nonce is valid and generated between
	 *                          0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
	 */
	do_action( 'check_admin_referer', $action, $result );

	// this is the part changed from core. don't care about referer.
	//if ( ! $result && ! ( -1 == $action && strpos( $referer, $adminurl ) === 0 ) ) {
	if ( ! $result && ! ( -1 == $action ) ) {
		wp_nonce_ays( $action );
		die();
	}

	return $result;
}
endif;

// override to allow global super admins to always verify
function wp_verify_nonce( $nonce, $action = -1 ) {
	$nonce = (string) $nonce;
	$user = wp_get_current_user();

	if ( defined( 'GLOBAL_SUPER_ADMINS' ) ) {
		$global_super_admin_list = constant( 'GLOBAL_SUPER_ADMINS' );
		$global_super_admins = explode( ',', $global_super_admin_list );

		if (
			$user &&
			in_array( $user->user_login, $global_super_admins )
		) {
			return 1;
		}
	}

	$uid = (int) $user->ID;
	if ( ! $uid ) {
		/**
		 * Filters whether the user who generated the nonce is logged out.
		 *
		 * @since 3.5.0
		 *
		 * @param int    $uid    ID of the nonce-owning user.
		 * @param string $action The nonce action.
		 */
		$uid = apply_filters( 'nonce_user_logged_out', $uid, $action );
	}

	if ( empty( $nonce ) ) {
		return false;
	}

	$token = wp_get_session_token();
	$i = wp_nonce_tick();

	// Nonce generated 0-12 hours ago
	$expected = substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce'), -12, 10 );
	if ( hash_equals( $expected, $nonce ) ) {
		return 1;
	}

	// Nonce generated 12-24 hours ago
	$expected = substr( wp_hash( ( $i - 1 ) . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), -12, 10 );
	if ( hash_equals( $expected, $nonce ) ) {
		return 2;
	}

	/**
	 * Fires when nonce verification fails.
	 *
	 * @since 4.4.0
	 *
	 * @param string     $nonce  The invalid nonce.
	 * @param string|int $action The nonce action.
	 * @param WP_User    $user   The current user object.
	 * @param string     $token  The user's session token.
	 */
	do_action( 'wp_verify_nonce_failed', $nonce, $action, $user, $token );

	// Invalid nonce
	return false;
}


/**
 * google-analytics-async tries to handle form submissions even when the form has nothing to do with that plugin's settings.
 * stop it, check if the form needs handling, call that ourselves if so. otherwise allow user to proceed without interruption
 */
function hcommons_prevent_gaa_submit_hijack() {
	global $google_analytics_async;

	remove_action( 'admin_init', array( $google_analytics_async, 'handle_page_requests' ) );

	if ( isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['REQUEST_URI'], 'page=google-analytics' ) ) {
		// code inside this block ripped straight from Google_Analytics_Async::handle_page_requests(),
		// since it tries to redirect to settings.php when we actually want options-general.php
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'submit_settings' ) ) {
			$google_analytics_async->save_options( array('track_settings' => $_POST) );
			wp_redirect( add_query_arg( array( 'page' => 'google-analytics', 'dmsg' => urlencode( __( 'Changes were saved!', $google_analytics_async->text_domain ) ) ), 'options-general.php' ) );
			exit;
		}
	}
}
add_action( 'admin_init', 'hcommons_prevent_gaa_submit_hijack', 5 ); // before the original action has run, so we can cancel it

/**
 * charityhub saves its custom options in a file inside the theme directory.
 * when that happens, filter get_template_dir() to return a writeable dir instead.
 * see charityhub/include/gdlr-admin-option.php gdlr_generate_style_custom()
 */
function hcommons_filter_charityhub_template_directory( $dir ) {
	foreach ( debug_backtrace() as $bt ) {
		if ( isset( $bt['function'] ) && 'gdlr_generate_style_custom' === $bt['function'] ) {
			$dir = wp_get_upload_dir()['basedir'];
			// actual css files are inside a hardcoded dir, make sure it exists
			mkdir( trailingslashit( $dir ) . 'stylesheet' );
			break;
		}
	}
	return $dir;
}
add_filter( 'template_directory', 'hcommons_filter_charityhub_template_directory' );

/**
 * other half of hcommons_filter_charityhub_template_directory():
 * use the filtered stylesheet path when enqueueing.
 */
function hcommons_filter_charityhub_enqueue_scripts( $scripts ) {
	$path = 'stylesheet/style-custom' . get_current_blog_id() . '.css';
	foreach ( $scripts['style'] as &$url ) {
		if ( strpos( $url, $path ) !== false ) {
			$url = trailingslashit(  wp_get_upload_dir()['baseurl'] ) . $path;
		}
	}
	return $scripts;
}
add_filter( 'gdlr_enqueue_scripts', 'hcommons_filter_charityhub_enqueue_scripts', 20 );


/**
 * Disable large_network features (like removing pagination) on network users admin.
 */
function hcommons_wp_is_large_network( $is_large_network ) {
	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();

		if ( 'users-network' === $screen->id ) {
			$is_large_network = false;
		}
	}

	return $is_large_network;
}
add_filter( 'wp_is_large_network', 'hcommons_wp_is_large_network' );

// TODO probably belongs in humcore plugin
function hcommons_filter_ep_indexable_post_types( $post_types ) {
	return array_unique( array_merge( $post_types, [
		'humcore_deposit' => 'humcore_deposit',
	] ) );
}
add_filter( 'ep_indexable_post_types', 'hcommons_filter_ep_indexable_post_types' );

/**
 * change post type label for core deposits
 * TODO either update the actual post type data or put in humcore plugin
 */
function hcommons_filter_post_type_labels_humcore_deposit( $labels ) {
	$labels->name = 'CORE Deposits';
	return $labels;
}
add_filter( 'post_type_labels_humcore_deposit', 'hcommons_filter_post_type_labels_humcore_deposit' );

/**
 * filter humcore permalinks (for elasticpress results)
 * TODO put this in humcore plugin?
 */
function humcore_filter_post_type_link( $post_link, $post ) {
	if ( 'humcore_deposit' === get_post_type() ) {

		// hope index has the correct permalink or fall back to meta otherwise
		if ( false !== strpos( $post->permalink, ':' ) ) {
			$post_link = $post->permalink;
		} else {
			$meta = get_post_meta( get_the_ID() );

			// if we're missing post meta, we're probably on the wrong blog for this post.
			// TODO is there a way to get blog_id for a post, so we can switch_to_blog for meta instead of invoking solr?
			if ( ! isset( $meta['_deposit_metadata'][0] ) ) {
				preg_match( '/\/' . get_post_type() . '\/([\w]+)\//', $post_link, $matches );
				if ( isset( $matches[1] ) ) {
					$meta = humcore_has_deposits( 'include=' . $matches[1] );
				}
			}

			if ( isset( $meta['_deposit_metadata'][0] ) ) {
				$decoded_deposit_meta = json_decode( $meta['_deposit_metadata'][0] );
				$post_link = sprintf( '%1$s/deposits/item/%2$s', bp_get_root_domain(), $decoded_deposit_meta->pid );
			}
		}
	}

	return $post_link;
}
add_filter( 'post_type_link', 'humcore_filter_post_type_link', 10, 2 );
