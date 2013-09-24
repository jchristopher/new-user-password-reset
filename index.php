<?php

/**
 * Plugin Name: New User Password Reset
 * Plugin URI:  https://github.com/jchristopher/new-user-password-reset
 * Description: Automatically trigger a password reset when creating new users to allow for more secure account introductions
 * Author:      Jonathan Christopher
 * Author URI:  http://mondaybynoon.com/
 * Version:     1.0
 * Text Domain: nupr
 * Domain Path: /languages/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Store whether or not we're in the admin
if( !defined( 'IS_ADMIN' ) ) define( 'IS_ADMIN',  is_admin() );

class newUserPasswordReset {

	public $version     = '1.0';
	public $textDomain  = 'nupr';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'adminInit' ) );
		add_action( 'admin_print_scripts-user-new.php', array( $this, 'enqueueScript' ) );
		add_action( 'user_register', array( $this, 'triggerPasswordReset' ) );
	}

	public function adminInit() {
		load_plugin_textdomain( 'new-user-password-reset', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	function enqueueScript() {
		if ( ! current_user_can( 'edit_users' ) )
			return;

		wp_enqueue_script( 'new-user-password-reset', plugin_dir_url( __FILE__ ) . 'new-user-password-reset.js', array( 'jquery' ), $this->version, true );

		$jsTrans = array(
			'autoreset' => __( 'Automatically issue a password reset', $this->textDomain ),
			'nonce'     => wp_create_nonce( 'nuprnonce' ),
		);
		wp_localize_script( 'new-user-password-reset', 'new_user_password_reset_l10n', $jsTrans );
	}

	function triggerPasswordReset( $user_id ) {
		if( ! isset( $_POST['auto_password_reset'] ) || ! current_user_can( 'edit_users' ) || ! isset( $_POST['auto_password_reset_nonce'] ) || ! wp_verify_nonce( $_POST['auto_password_reset_nonce'], 'nuprnonce' ) )
			return;

		$userInfo = get_userdata( $user_id );
		$target   = site_url() . '/wp-login.php?action=lostpassword';
		$args     = array(
			'body'        => array( 'user_login' => $userInfo->user_login ),
			'sslverify'   => false,
		);
		wp_remote_post( $target, $args );
	}

}

new newUserPasswordReset();
