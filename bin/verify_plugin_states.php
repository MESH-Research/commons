<?php

/**
 * Verify that plugins have consistent states across all networks.
 * Output any discrepancies.
 */

$networks = [
	'',
	'ajs.',
	'aseees.',
	'caa.',
	'mla.',
];

$all_networks_plugins = [];

$hostname = trim( shell_exec( 'hostname' ) );

chdir( '/srv/www/commons/current/' );

foreach ( $networks as $network ) {
	$url = $network . $hostname;
	$all_networks_plugins[ $url ] = json_decode( shell_exec( "wp --url=$url plugin list --format=json" ) );
}

array_walk( $all_networks_plugins, function( $plugins, $url ) use ( $hostname, $all_networks_plugins ) {

	// Compare each plugin on this network to the same on the first network.
	foreach ( $plugins as $k => $plugin ) {
		if ( $plugin != $all_networks_plugins[ $hostname ][ $k ] ) {
			printf(
				"PROBLEM: %s is %s on %s and %s on %s" . PHP_EOL,
				$plugin->name,
				$all_networks_plugins[ $hostname ][ $k ]->status,
				$hostname,
				$plugin->status,
				$url
			);
		}
	}

} );
