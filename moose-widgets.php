<?php
/**
 * Plugin Name: Moose Widgets
 * Description: A lightweight, extensible collection of premium Elementor widgets.
 * Version: 1.1.11
 * Author: m00s3c
 * Author URI: https://moose.sh
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

define( 'MOOSE_WIDGETS_VERSION', '1.1.11' );
define( 'MOOSE_WIDGETS_FILE', __FILE__ );
define( 'MOOSE_WIDGETS_PATH', plugin_dir_path( __FILE__ ) );
define( 'MOOSE_WIDGETS_URL', plugin_dir_url( __FILE__ ) );

require_once MOOSE_WIDGETS_PATH . 'includes/plugin.php';

Plugin::instance();
