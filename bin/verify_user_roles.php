<?php

$networks = [
	'',
	'ajs.',
	'aseees.',
	'caa.',
	'mla.',
	'up.',
];

$roles = [
	'administrator',
	'editor',
	'author',
];

$all_networks_users = [];

$hostname = trim( shell_exec( 'hostname' ) );

chdir( '/srv/www/commons/current/' );

foreach ( $networks as $network ) {
	$url = $network . $hostname;
	$all_networks_users[ $url ] = [];

	// super-admins are not real roles and so require a separate command with slightly different processing
	$super_admins = json_decode( shell_exec(
		"wp --url=$url super-admin list --format=json"
	) );
	array_walk( $super_admins, function( &$super_admin ) {
		return $super_admin = $super_admin->user_login;
	} );
	$all_networks_users[ $url ][ 'super-admin' ] = $super_admins;

	// now real roles
	foreach ( $roles as $role ) {
		$all_networks_users[ $url ][ $role ] = json_decode( shell_exec(
			"wp --url=$url user list --role=$role --format=json --field=user_login"
		) );
	}
}

print_r( $all_networks_users );
