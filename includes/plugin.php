<?php
/**
 * Main plugin bootstrap.
 *
 * @package MooseWidgets
 */

namespace MooseWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {
	private static ?Plugin $instance = null;

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init(): void {
		load_plugin_textdomain( 'moose-widgets', false, dirname( plugin_basename( MOOSE_WIDGETS_FILE ) ) . '/languages' );

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'elementor_missing_notice' ) );
			return;
		}

		if ( version_compare( PHP_VERSION, '8.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
			return;
		}

		require_once MOOSE_WIDGETS_PATH . 'includes/assets.php';
		require_once MOOSE_WIDGETS_PATH . 'includes/widget-loader.php';

		Assets::instance();
		Widget_Loader::instance();
	}

	public function elementor_missing_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		?>
		<div class="notice notice-warning"><p>
			<?php echo esc_html__( 'Moose Widgets requires Elementor to be installed and activated.', 'moose-widgets' ); ?>
		</p></div>
		<?php
	}

	public function php_version_notice(): void {
		?>
		<div class="notice notice-error"><p>
			<?php echo esc_html__( 'Moose Widgets requires PHP 8.0 or newer.', 'moose-widgets' ); ?>
		</p></div>
		<?php
	}
}
