<?php
/**
 * Widget asset registration.
 *
 * @package MooseWidgets
 */

namespace MooseWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Assets {
	private static ?Assets $instance = null;

	private function __construct() {
		// Register on WordPress' normal frontend lifecycle first. Elementor's
		// dedicated hooks remain as fallbacks for editor/preview requests.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 5 );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_styles' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'register_styles' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'register_styles' ) );
	}

	public static function instance(): Assets {
		return self::$instance ??= new self();
	}

	public function register_assets(): void {
		$this->register_styles();
		$this->register_scripts();
	}

	public function register_styles(): void {
		foreach ( $this->asset_files( 'style.css' ) as $slug => $file ) {
			wp_register_style( 'moose-widgets-' . $slug, MOOSE_WIDGETS_URL . $file, array(), $this->asset_version( $file ) );
		}
	}

	public function register_scripts(): void {
		foreach ( $this->asset_files( 'script.js' ) as $slug => $file ) {
			wp_register_script( 'moose-widgets-' . $slug, MOOSE_WIDGETS_URL . $file, array(), $this->asset_version( $file ), true );
		}
	}

	private function asset_version( string $file ): string {
		$path = MOOSE_WIDGETS_PATH . $file;

		return file_exists( $path ) ? (string) filemtime( $path ) : MOOSE_WIDGETS_VERSION;
	}

	private function asset_files( string $filename ): array {
		$assets = array();
		$files  = glob( MOOSE_WIDGETS_PATH . 'widgets/*/' . $filename ) ?: array();

		foreach ( $files as $file ) {
			$slug            = basename( dirname( $file ) );
			$assets[ $slug ] = 'widgets/' . $slug . '/' . $filename;
		}

		return $assets;
	}
}
