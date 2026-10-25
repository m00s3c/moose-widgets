<?php
/**
 * Automatic widget discovery and registration.
 *
 * @package MooseWidgets
 */

namespace MooseWidgets;

use Elementor\Elements_Manager;
use Elementor\Widgets_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Widget_Loader {
	private static ?Widget_Loader $instance = null;

	private function __construct() {
		add_action( 'init', array( $this, 'register_widget_hooks' ), 5 );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	public static function instance(): Widget_Loader {
		return self::$instance ??= new self();
	}

	public function register_category( Elements_Manager $elements_manager ): void {
		$elements_manager->add_category(
			'moose-widgets',
			array(
				'title' => esc_html__( 'Moose Widgets', 'moose-widgets' ),
				'icon'  => 'eicon-apps',
			)
		);
	}

	public function register_widgets( Widgets_Manager $widgets_manager ): void {
		$files = glob( MOOSE_WIDGETS_PATH . 'widgets/*/widget.php' ) ?: array();

		foreach ( $files as $file ) {
			$slug       = basename( dirname( $file ) );
			$class_part = str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $slug ) ) );
			$class_name = 'MooseWidgets\\Widgets\\' . $class_part . '\\Widget';

			require_once $file;

			if ( class_exists( $class_name ) && is_subclass_of( $class_name, '\\Elementor\\Widget_Base' ) ) {
				// Widgets may optionally expose frontend hooks (for example, AJAX
				// endpoints) while remaining self-contained in their widget class.
				if ( is_callable( array( $class_name, 'register_hooks' ) ) ) {
					$class_name::register_hooks();
				}

				$widgets_manager->register( new $class_name() );
			}
		}
	}

	/**
	 * Register optional widget-level hooks early enough for admin-ajax.php.
	 */
	public function register_widget_hooks(): void {
		$files = glob( MOOSE_WIDGETS_PATH . 'widgets/*/widget.php' ) ?: array();

		foreach ( $files as $file ) {
			$slug       = basename( dirname( $file ) );
			$class_part = str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $slug ) ) );
			$class_name = 'MooseWidgets\\Widgets\\' . $class_part . '\\Widget';

			require_once $file;
			if ( class_exists( $class_name ) && is_callable( array( $class_name, 'register_hooks' ) ) ) {
				$class_name::register_hooks();
			}
		}
	}
}
