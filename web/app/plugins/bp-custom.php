<?php
if ( 'CBOX-MLA' != wp_get_theme() ) :
/* we like the buddy panel now
function hcommons_buddyboss_no_buddypanel( $classes ) {

        if ( 'aseees' === get_network_option( '', 'society_id' ) ) {
                //hide buddypanel
                $classes[] = 'page-template-page-no-buddypanel';
                $classes[] = 'left-menu-open';
        }
        return array_unique( $classes );

}
add_filter( 'body_class','hcommons_buddyboss_no_buddypanel' );
*/
function mla_filter_gettext( $translated, $original, $domain ) {
        // This is an array of original strings
        // and what they should be replaced with
        $strings = array(
                'Username' => 'User name', // per MLA house style
                'login' => 'log-in', // per MLA house style
                'Group Blog' => 'Site', // bp-groupblog textdomain fix
                'Blogs' => 'Sites', // bp-groupblog textdomain fix
                'Blog' => 'Site', // bp-groupblog textdomain fix
                'Friends' => 'Contacts', // it's a formality thing
                'Friend' => 'Contact',
                'Friendships' => 'Contacts',
                'Howdy' => 'Hello',
                // Add some more strings here
        );

        // See if the current string is in the $strings array
        // If so, replace it's translation
        if ( ! empty( $strings[ $original ] ) ) {
                // This accomplishes the same thing as __()
                // but without running it through the filter again
                $translations = get_translations_for_domain( $domain );
                $translated = $translations->translate( $strings[ $original ] );
        }

        return $translated;
}
add_filter( 'gettext', 'mla_filter_gettext', 10, 3 );

function mla_is_group_committee( $group_id = 0 ) {
        // use the current group if we're not passed one.
        if ( 0 == $group_id ) $group_id = bp_get_current_group_id();

        // if mla_oid starts with "M," it's a committee
        return ('M' == substr( groups_get_groupmeta( $group_id, 'mla_oid' ), 0, 1 ) ) ? true : false;
}

endif;
