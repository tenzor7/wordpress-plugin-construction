<?php
/*
Plugin Name:       Protect normal plugins MU
Plugin URI:        https://github.com/szepeviktor/wordpress-plugin-construction
Description:       Prevent deletion of normal plugins
Version:           1.0.1
Author:            Viktor Szépe
License:           GNU General Public License v2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/szepeviktor/wordpress-plugin-construction/tree/master/mu-protect-plugins
*/

if ( ! function_exists( 'add_filter' ) ) {
    error_log( 'Malicious sign detected: wpf2b_direct_access '
        . addslashes( $_SERVER['REQUEST_URI'] )
    );
    ob_get_level() && ob_end_clean();
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.0 403 Forbidden' );
    exit();
}

class O1_Protect_Plugins {

	/**
	 * List of protected plugins.
	 * Add your plugins here!
	 *
	 * @var array
	 * @access private
	 */
	private $protected_plugins = array(
		'wp-solarized/wp-solarized.php'
	);

	/**
	 * Constructor.
	 *
	 * Registers filters, actions.
	 *
	 * @access public
	 */
	public function __construct() {

        //FIXME Is it faster this way? add_filter( 'pre_option_active_plugins', array( $this, 'fix_protected' ) );
		foreach ( $this->protected_plugins as $protected ) {
			// reactivate on deactivation
			add_action( 'deactivate_' . $protected,
				function ( $network_wide ) use ( $protected ) {
					$this->reactivate( $protected, $network_wide );
				}
			);
			// remove Deactivate and Delete actions
			add_filter( 'network_admin_plugin_action_links_' . $protected, array( $this, 'remove_actions' ) );
			add_filter( 'plugin_action_links_' . $protected, array( $this, 'remove_actions' ) );
		}
	}

	/**
	 * Activate a plugin when it is deactivated.
	 *
	 * @access public
	 * @param string $plugin Base plugin path from plugins directory.
	 * @param bool $network_wide Whether to enable the plugin for all sites in the network
	 *                           or just the current site.
	 */
	public function reactivate( $plugin, $network_wide ) {

		add_filter( 'pre_update_option_' . 'active_plugins',
			array( $this, 'revert_values' ), 10, 2 );
		add_filter( 'pre_update_site_option_' . 'active_sitewide_plugins',
			array( $this, 'revert_values' ), 10, 2 );
	}

	/**
	 * Revert the previous value.
	 *
	 * @access public
	 * @param string $value The new value.
	 * @param bool $old_value The previous value.
	 */
	public function revert_values( $value, $old_value ) {

		return $old_value;
	}

	/**
	 * Remove the "Deactivate" and "Delete" links from plugin actions.
	 *
	 * @access public
	 * @param string $plugin Base plugin path from plugins directory.
	 * @return array Remaining plugin actions.
	 */
	public function remove_actions( $actions ) {

		if ( isset( $actions['deactivate'] ) ) {
			unset( $actions['deactivate'] );
		}
		if ( isset( $actions['delete'] ) ) {
			unset( $actions['delete'] );
		}

		return $actions;
	}

}

new O1_Protect_Plugins();
