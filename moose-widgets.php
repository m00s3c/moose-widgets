<?php
/**
 * Plugin Name: Moose Widgets
 * Plugin URI: https://github.com/m00s3c/moose-widgets
 * Description: A lightweight, extensible collection of premium Elementor widgets.
 * Version: 1.1.13
 * Author: webmaster.sh
 * Author URI: https://webmaster.sh
 * Text Domain: moose-widgets
 * Domain Path: /languages
 * Requires at least: 6.2
 * Requires PHP: 8.0
 * Elementor tested up to: 3.25
 */

namespace MooseWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MOOSE_WIDGETS_VERSION', '1.1.13' );
define( 'MOOSE_WIDGETS_FILE', __FILE__ );
define( 'MOOSE_WIDGETS_PATH', plugin_dir_path( __FILE__ ) );
define( 'MOOSE_WIDGETS_URL', plugin_dir_url( __FILE__ ) );

require_once MOOSE_WIDGETS_PATH . 'includes/plugin.php';

Plugin::instance();
