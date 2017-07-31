<?php

// if enabled, try to reconcile plugin states agains the main network.
// otherwise just output any problems detected.
// not thoroughly tested, use with caution!
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

// these are not actually problems, so ignore them
$whitelist = <<<"EOD"
PROBLEM: 15zine-functionality is active-network on $hostname and inactive on ajs.$hostname
PROBLEM: bwp-google-xml-sitemaps is active on $hostname and inactive on ajs.$hostname
PROBLEM: password-protected is active-network on $hostname and active on ajs.$hostname
PROBLEM: typekit-fonts-for-wordpress is active on $hostname and inactive on ajs.$hostname
PROBLEM: 15zine-functionality is active-network on $hostname and inactive on aseees.$hostname
PROBLEM: bwp-google-xml-sitemaps is active on $hostname and inactive on aseees.$hostname
PROBLEM: buddypress-messages-spam-blocker is active on $hostname and inactive on aseees.$hostname
PROBLEM: disable-password-reset-extended is active-network on $hostname and inactive on aseees.$hostname
PROBLEM: typekit-fonts-for-wordpress is active on $hostname and inactive on aseees.$hostname
PROBLEM: 15zine-functionality is active-network on $hostname and inactive on caa.$hostname
PROBLEM: bwp-google-xml-sitemaps is active on $hostname and inactive on caa.$hostname
PROBLEM: buddypress-messages-spam-blocker is active on $hostname and inactive on caa.$hostname
PROBLEM: disable-password-reset-extended is active-network on $hostname and inactive on caa.$hostname
PROBLEM: so-widgets-bundle is active-network on $hostname and active on caa.$hostname
PROBLEM: 15zine-functionality is active-network on $hostname and inactive on mla.$hostname
PROBLEM: bwp-google-xml-sitemaps is active on $hostname and inactive on mla.$hostname
PROBLEM: buddypress-docs-minor-edit is active-network on $hostname and active on mla.$hostname
PROBLEM: buddypress-messages-spam-blocker is active on $hostname and inactive on mla.$hostname
PROBLEM: cbox-auth is inactive on $hostname and active-network on mla.$hostname
PROBLEM: menu-icons is active-network on $hostname and inactive on mla.$hostname
PROBLEM: options-framework is inactive on $hostname and active-network on mla.$hostname
PROBLEM: password-protected is active-network on $hostname and inactive on mla.$hostname
PROBLEM: so-widgets-bundle is active-network on $hostname and active on mla.$hostname
PROBLEM: typekit-fonts-for-wordpress is active on $hostname and inactive on mla.$hostname
PROBLEM: wordpress-mu-domain-mapping is inactive on $hostname and active-network on mla.$hostname
PROBLEM: wp-hide-dashboard is active on $hostname and active-network on mla.$hostname

EOD;
// the extra newline above is necessary to ensure the final line matches

chdir( '/srv/www/commons/current/' );

foreach ( $networks as $network ) {
	$url = $network . $hostname;
	$all_networks_plugins[ $url ] = json_decode( shell_exec( "wp --url=$url plugin list --format=json" ) );
}

array_walk( $all_networks_plugins, function( $plugins, $url ) use ( $whitelist, $hostname, $all_networks_plugins ) {

	// Compare each plugin on this network to the same on the first network.
	foreach ( $plugins as $k => $plugin ) {
		if ( $plugin != $all_networks_plugins[ $hostname ][ $k ] ) {
			$msg = sprintf(
				"PROBLEM: %s is %s on %s and %s on %s" . PHP_EOL,
				$plugin->name,
				$all_networks_plugins[ $hostname ][ $k ]->status,
				$hostname,
				$plugin->status,
				$url
			);

			if ( false !== strpos( $whitelist, $msg ) ) {
				// this "problem" is expected, ignore & move on
				continue;
			}

			echo $msg;

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
