<?php
// Remove the saved options when Flurry is uninstalled
if( defined( WP_UNINSTALL_PLUGIN ) ) {
	delete_option( 'flurry_settings' );
}
