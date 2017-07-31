<?php

// if enabled, try to reconcile plugin states agains the main network.
// otherwise just output any problems detected.
const FIX_PLUGIN_STATES = false;

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

			if ( FIX_PLUGIN_STATES ) {
				$cmd = "wp --url=$url plugin ";

				switch ( $all_networks_plugins[ $hostname ][ $k ]->status ) {
				case 'active':
					if ( false !== strpos( $plugin->status, 'network' ) ) {
						// network active on this network, but only site active on main.
						// deactivate before reactivating at correct scope
						echo shell_exec( "$cmd deactivate --network {$plugin->name}" );
					}
					$cmd .= 'activate ';
					break;
				case 'active-network':
					$cmd .= 'activate --network ';
					break;
				case 'inactive':
					$cmd .= 'deactivate ';
					if ( false !== strpos( $plugin->status, 'network' ) ) {
						$cmd .= '--network ';
					}
					break;
				}

				$cmd .= $plugin->name;

				echo $cmd . PHP_EOL;

				echo shell_exec( $cmd );
			}
		}
	}

} );
