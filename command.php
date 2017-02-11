<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Does a search-replace switching all urls that contain siteurl to https on all sites in a WordPress multisite.
 *
 * @when after_wp_load
 */
$https_migrate_command = function( $args, $assoc_args ) {
	if ( isset( $assoc_args['dry-run'] ) ) {
		$dry_run = '--dry-run';
	} else {
		$dry_run = '';
	}
	$response = WP_CLI::launch_self( 'site list', array(), array( 'format' => 'json' ), false, true );
	$sites = json_decode( $response->stdout );
	foreach ( $sites as $site ) {
		WP_CLI::log( "Changing urls on {$site->url}..." );
		$site_url = untrailingslashit( $site->url );
		$secure_site_url = str_replace( 'http://', 'https://', $site_url );
		$response = WP_CLI::runcommand( 'search-replace ' . $site_url . ' ' . $secure_site_url . ' ' . $dry_run . ' --skip-columns=guid --url=' . $site_url );
		WP_CLI::log( $response->stdout );
	}
};
WP_CLI::add_command( 'https-migrate', $https_migrate_command );
