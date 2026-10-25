<?php
/**
 * AJAX Search Elementor widget.
 *
 * @package MooseWidgets
 */

namespace MooseWidgets\Widgets\Ajax_Search;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget extends Widget_Base {
	private static bool $hooks_registered = false;

	public static function register_hooks(): void {
		if ( self::$hooks_registered ) {
			return;
		}

		add_action( 'wp_ajax_moose_widgets_search', array( self::class, 'ajax_search' ) );
		add_action( 'wp_ajax_nopriv_moose_widgets_search', array( self::class, 'ajax_search' ) );
		self::$hooks_registered = true;
	}

	public static function ajax_search(): void {
		check_ajax_referer( 'moose_widgets_search', 'nonce' );

		$query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
		$limit = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 5;
		$limit = max( 1, min( 12, $limit ) );

		$requested_types = isset( $_POST['post_types'] ) ? (array) $_POST['post_types'] : array( 'post' );
		$requested_types = array_map( 'sanitize_key', wp_unslash( $requested_types ) );
		$public_types    = get_post_types( array( 'public' => true ), 'names' );
		$post_types      = array_values( array_intersect( $requested_types, $public_types ) );
		$post_types      = array_values( array_diff( $post_types, array( 'attachment' ) ) );

		if ( '' === $query || empty( $post_types ) ) {
			wp_send_json_success( array( 'items' => array() ) );
		}

		$search = new WP_Query(
			array(
				's'                   => $query,
				'post_type'           => $post_types,
				'post_status'         => 'publish',
				'posts_per_page'      => $limit,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);

		$items = array();
		foreach ( $search->posts as $post ) {
			$excerpt = has_excerpt( $post ) ? $post->post_excerpt : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 22 );
			$items[] = array(
				'id'        => (int) $post->ID,
				'title'     => html_entity_decode( get_the_title( $post ), ENT_QUOTES, get_bloginfo( 'charset' ) ?: 'UTF-8' ),
				'url'       => get_permalink( $post ),
				'thumbnail' => get_the_post_thumbnail_url( $post, 'thumbnail' ) ?: '',
				'excerpt'   => html_entity_decode( wp_strip_all_tags( $excerpt ), ENT_QUOTES, get_bloginfo( 'charset' ) ?: 'UTF-8' ),
				'date'      => get_the_date( '', $post ),
				'type'      => get_post_type_object( $post->post_type )->labels->singular_name ?? $post->post_type,
			);
		}

		wp_send_json_success( array( 'items' => $items ) );
	}

	public function get_name(): string { return 'moose-ajax-search'; }
	public function get_title(): string { return esc_html__( 'AJAX Search', 'moose-widgets' ); }
	public function get_icon(): string { return 'eicon-search'; }
	public function get_categories(): array { return array( 'moose-widgets' ); }
	public function get_keywords(): array { return array( 'search', 'ajax', 'live', 'results', 'moose' ); }
	public function get_style_depends(): array { return array( 'moose-widgets-ajax-search' ); }
	public function get_script_depends(): array { return array( 'moose-widgets-ajax-search' ); }

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_trigger_styles();
		$this->register_panel_styles();
		$this->register_form_styles();
		$this->register_result_styles();
	}

	private function register_content_controls(): void {
		$this->start_controls_section( 'section_search', array( 'label' => esc_html__( 'Search', 'moose-widgets' ) ) );
		$this->add_control( 'layout', array(
			'label' => esc_html__( 'Display', 'moose-widgets' ), 'type' => Controls_Manager::SELECT, 'default' => 'dropdown',
			'options' => array(
				'dropdown' => esc_html__( 'Icon + Dropdown', 'moose-widgets' ),
				'bar'      => esc_html__( 'Icon + Expanding Bar', 'moose-widgets' ),
				'inline'   => esc_html__( 'Always Visible', 'moose-widgets' ),
			),
		) );
		$this->add_control( 'trigger_icon', array( 'label' => esc_html__( 'Trigger Icon', 'moose-widgets' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-search', 'library' => 'fa-solid' ), 'condition' => array( 'layout!' => 'inline' ) ) );
		$this->add_control( 'trigger_text', array( 'label' => esc_html__( 'Trigger Text', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'placeholder' => esc_html__( 'Search', 'moose-widgets' ), 'condition' => array( 'layout!' => 'inline' ) ) );
		$this->add_control( 'placeholder', array( 'label' => esc_html__( 'Placeholder', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Search…', 'moose-widgets' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'submit_text', array( 'label' => esc_html__( 'Submit Button Text', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Search', 'moose-widgets' ) ) );
		$this->add_control( 'submit_icon', array( 'label' => esc_html__( 'Submit Icon', 'moose-widgets' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-search', 'library' => 'fa-solid' ) ) );
		$this->add_control( 'show_submit_button', array( 'label' => esc_html__( 'Show Submit Button', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'show_submit_text', array( 'label' => esc_html__( 'Show Submit Text', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => '' ) );
		$this->add_control( 'close_on_outside', array( 'label' => esc_html__( 'Close When Clicking Outside', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => array( 'layout!' => 'inline' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_results', array( 'label' => esc_html__( 'Live Results', 'moose-widgets' ) ) );
		$this->add_control( 'enable_ajax', array( 'label' => esc_html__( 'Enable Live Results', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$post_type_options = array();
		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			if ( 'attachment' !== $post_type->name ) { $post_type_options[ $post_type->name ] = $post_type->labels->singular_name; }
		}
		$this->add_control( 'post_types', array( 'label' => esc_html__( 'Search Content Types', 'moose-widgets' ), 'type' => Controls_Manager::SELECT2, 'multiple' => true, 'options' => $post_type_options, 'default' => array( 'post', 'page' ), 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_control( 'result_limit', array( 'label' => esc_html__( 'Maximum Results', 'moose-widgets' ), 'type' => Controls_Manager::NUMBER, 'default' => 5, 'min' => 1, 'max' => 12, 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_control( 'minimum_characters', array( 'label' => esc_html__( 'Minimum Characters', 'moose-widgets' ), 'type' => Controls_Manager::NUMBER, 'default' => 2, 'min' => 1, 'max' => 10, 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_control( 'debounce', array( 'label' => esc_html__( 'Search Delay (ms)', 'moose-widgets' ), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 100, 'max' => 1500, 'step' => 50, 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_control( 'show_image', array( 'label' => esc_html__( 'Show Featured Image', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_control( 'show_excerpt', array( 'label' => esc_html__( 'Show Excerpt', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_control( 'show_date', array( 'label' => esc_html__( 'Show Date', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_control( 'show_type', array( 'label' => esc_html__( 'Show Content Type', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_control( 'no_results_text', array( 'label' => esc_html__( 'No Results Text', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'No results found.', 'moose-widgets' ) ) );
		$this->add_control( 'view_all_text', array( 'label' => esc_html__( 'View All Text', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'View all results', 'moose-widgets' ) ) );
		$this->end_controls_section();
	}

	private function register_trigger_styles(): void {
		$this->start_controls_section( 'style_trigger', array( 'label' => esc_html__( 'Trigger', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'layout!' => 'inline' ) ) );
		$this->add_responsive_control( 'trigger_align', array( 'label' => esc_html__( 'Alignment', 'moose-widgets' ), 'type' => Controls_Manager::CHOOSE, 'options' => array( 'flex-start' => array( 'title' => 'Left', 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => 'Center', 'icon' => 'eicon-text-align-center' ), 'flex-end' => array( 'title' => 'Right', 'icon' => 'eicon-text-align-right' ) ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search' => 'align-items: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'trigger_icon_size', array( 'label' => esc_html__( 'Icon Size', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 22 ), 'range' => array( 'px' => array( 'min' => 10, 'max' => 80 ) ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__trigger-icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mw-ajax-search__trigger-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'trigger_typography', 'selector' => '{{WRAPPER}} .mw-ajax-search__trigger' ) );
		$this->start_controls_tabs( 'trigger_tabs' );
		$this->start_controls_tab( 'trigger_normal', array( 'label' => esc_html__( 'Normal', 'moose-widgets' ) ) );
		$this->add_control( 'trigger_color', array( 'label' => esc_html__( 'Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#1d2330', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__trigger' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'trigger_bg', array( 'label' => esc_html__( 'Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__trigger' => 'background-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'trigger_hover', array( 'label' => esc_html__( 'Hover', 'moose-widgets' ) ) );
		$this->add_control( 'trigger_hover_color', array( 'label' => esc_html__( 'Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2563eb', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__trigger:hover, {{WRAPPER}} .mw-ajax-search__trigger:focus-visible' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'trigger_hover_bg', array( 'label' => esc_html__( 'Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__trigger:hover, {{WRAPPER}} .mw-ajax-search__trigger:focus-visible' => 'background-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control( 'trigger_padding', array( 'label' => esc_html__( 'Padding', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'trigger_border', 'selector' => '{{WRAPPER}} .mw-ajax-search__trigger' ) );
		$this->add_responsive_control( 'trigger_radius', array( 'label' => esc_html__( 'Border Radius', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	private function register_panel_styles(): void {
		$this->start_controls_section( 'style_panel', array( 'label' => esc_html__( 'Search Panel', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'panel_background', 'types' => array( 'classic', 'gradient' ), 'selector' => '{{WRAPPER}} .mw-ajax-search__panel' ) );
		$this->add_responsive_control( 'panel_width', array(
			'label' => esc_html__( 'Search Content Width', 'moose-widgets' ),
			'description' => esc_html__( 'In expanding-bar mode, the background row stays full width while this controls the search field container.', 'moose-widgets' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%', 'vw' ),
			'range' => array( 'px' => array( 'min' => 240, 'max' => 1400 ), '%' => array( 'min' => 50, 'max' => 100 ), 'vw' => array( 'min' => 20, 'max' => 100 ) ),
			'default' => array( 'unit' => 'px', 'size' => 520 ),
			'mobile_default' => array( 'unit' => '%', 'size' => 100 ),
			'selectors' => array(
				'{{WRAPPER}} .mw-ajax-search--dropdown .mw-ajax-search__panel' => 'width: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .mw-ajax-search__panel-inner' => 'max-width: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'panel_padding', array( 'label' => esc_html__( 'Padding', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 10, 'right' => 16, 'bottom' => 10, 'left' => 16, 'unit' => 'px' ), 'tablet_default' => array( 'top' => 10, 'right' => 14, 'bottom' => 10, 'left' => 14, 'unit' => 'px' ), 'mobile_default' => array( 'top' => 8, 'right' => 12, 'bottom' => 8, 'left' => 12, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__panel-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'panel_offset', array( 'label' => esc_html__( 'Distance From Trigger', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 10 ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search--dropdown .mw-ajax-search__panel' => 'top: calc(100% + {{SIZE}}{{UNIT}});', '{{WRAPPER}} .mw-ajax-search--bar .mw-ajax-search__panel' => 'margin-top: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'panel_border', 'selector' => '{{WRAPPER}} .mw-ajax-search__panel' ) );
		$this->add_responsive_control( 'panel_radius', array( 'label' => esc_html__( 'Border Radius', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 4, 'right' => 4, 'bottom' => 4, 'left' => 4, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'panel_shadow', 'selector' => '{{WRAPPER}} .mw-ajax-search__panel' ) );
		$this->end_controls_section();
	}

	private function register_form_styles(): void {
		$this->start_controls_section( 'style_form', array( 'label' => esc_html__( 'Search Field & Button', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'form_gap', array( 'label' => esc_html__( 'Gap', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 8 ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__form' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'input_height', array( 'label' => esc_html__( 'Field Height', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 40 ), 'range' => array( 'px' => array( 'min' => 32, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__input, {{WRAPPER}} .mw-ajax-search__submit' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'input_typography', 'selector' => '{{WRAPPER}} .mw-ajax-search__input' ) );
		$this->add_control( 'input_color', array( 'label' => esc_html__( 'Input Text', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#1d2330', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__input' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'placeholder_color', array( 'label' => esc_html__( 'Placeholder', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#7a8291', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__input::placeholder' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'input_bg', array( 'label' => esc_html__( 'Input Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__input' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'input_border_color', array( 'label' => esc_html__( 'Input Border', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#d8dce5', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__input' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'input_focus_color', array( 'label' => esc_html__( 'Focus Border', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2563eb', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__input:focus' => 'border-color: {{VALUE}}; box-shadow: 0 0 0 3px color-mix(in srgb, {{VALUE}} 18%, transparent);' ) ) );
		$this->add_responsive_control( 'input_radius', array( 'label' => esc_html__( 'Input Radius', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 3, 'right' => 3, 'bottom' => 3, 'left' => 3, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'submit_typography', 'selector' => '{{WRAPPER}} .mw-ajax-search__submit' ) );
		$this->add_control( 'submit_color', array( 'label' => esc_html__( 'Button Text', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__submit' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'submit_bg', array( 'label' => esc_html__( 'Button Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2563eb', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__submit' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'submit_hover_bg', array( 'label' => esc_html__( 'Button Hover', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#1d4ed8', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__submit:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'submit_padding', array( 'label' => esc_html__( 'Button Padding', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 8, 'right' => 13, 'bottom' => 8, 'left' => 13, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'submit_radius', array( 'label' => esc_html__( 'Button Radius', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 3, 'right' => 3, 'bottom' => 3, 'left' => 3, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	private function register_result_styles(): void {
		$this->start_controls_section( 'style_results', array( 'label' => esc_html__( 'Live Results', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'enable_ajax' => 'yes' ) ) );
		$this->add_responsive_control( 'results_gap', array( 'label' => esc_html__( 'Results Spacing', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 8 ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__results-list' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'result_padding', array( 'label' => esc_html__( 'Result Padding', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 7, 'right' => 7, 'bottom' => 7, 'left' => 7, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__result-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_control( 'result_bg', array( 'label' => esc_html__( 'Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__result-link' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'result_hover_bg', array( 'label' => esc_html__( 'Hover Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#f4f5fa', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__result-link:hover, {{WRAPPER}} .mw-ajax-search__result-link:focus-visible' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'image_size', array( 'label' => esc_html__( 'Image Size', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 52 ), 'range' => array( 'px' => array( 'min' => 36, 'max' => 160 ) ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__result-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'image_radius', array( 'label' => esc_html__( 'Image Radius', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 4, 'right' => 4, 'bottom' => 4, 'left' => 4, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__result-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_control( 'title_color', array( 'label' => esc_html__( 'Title Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#1d2330', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__result-title' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .mw-ajax-search__result-title' ) );
		$this->add_control( 'meta_color', array( 'label' => esc_html__( 'Meta Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#7a8291', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__result-meta, {{WRAPPER}} .mw-ajax-search__result-excerpt' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'meta_typography', 'selector' => '{{WRAPPER}} .mw-ajax-search__result-meta, {{WRAPPER}} .mw-ajax-search__result-excerpt' ) );
		$this->add_control( 'view_all_color', array( 'label' => esc_html__( 'View All Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2563eb', 'selectors' => array( '{{WRAPPER}} .mw-ajax-search__view-all' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings   = $this->get_settings_for_display();
		$id         = 'mw-ajax-search-' . $this->get_id();
		$results_id = $id . '-results';
		$config     = array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'moose_widgets_search' ),
			'enabled'      => 'yes' === $settings['enable_ajax'],
			'postTypes'    => array_values( (array) $settings['post_types'] ),
			'limit'        => max( 1, min( 12, (int) $settings['result_limit'] ) ),
			'minChars'     => max( 1, (int) $settings['minimum_characters'] ),
			'debounce'     => max( 100, (int) $settings['debounce'] ),
			'showImage'    => 'yes' === $settings['show_image'],
			'showExcerpt'  => 'yes' === $settings['show_excerpt'],
			'showDate'     => 'yes' === $settings['show_date'],
			'showType'     => 'yes' === $settings['show_type'],
			'noResults'    => (string) $settings['no_results_text'],
			'viewAll'      => (string) $settings['view_all_text'],
			'closeOutside' => 'yes' === $settings['close_on_outside'],
		);
		?>
		<div class="mw-ajax-search mw-ajax-search--<?php echo esc_attr( $settings['layout'] ); ?><?php echo 'inline' === $settings['layout'] ? ' is-open' : ''; ?>" data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<?php if ( 'inline' !== $settings['layout'] ) : ?>
				<button class="mw-ajax-search__trigger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $id ); ?>" aria-label="<?php echo esc_attr__( 'Open search', 'moose-widgets' ); ?>">
					<span class="mw-ajax-search__trigger-icon" aria-hidden="true"><?php $this->render_search_icon( $settings['trigger_icon'] ); ?></span>
					<?php if ( $settings['trigger_text'] ) : ?><span><?php echo esc_html( $settings['trigger_text'] ); ?></span><?php endif; ?>
				</button>
			<?php endif; ?>
			<div id="<?php echo esc_attr( $id ); ?>" class="mw-ajax-search__panel"<?php echo 'inline' !== $settings['layout'] ? ' aria-hidden="true" hidden' : ''; ?>>
				<div class="mw-ajax-search__panel-inner">
					<form class="mw-ajax-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label class="screen-reader-text" for="<?php echo esc_attr( $id . '-input' ); ?>"><?php echo esc_html__( 'Search for:', 'moose-widgets' ); ?></label>
						<input id="<?php echo esc_attr( $id . '-input' ); ?>" class="mw-ajax-search__input" type="search" name="s" placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>" autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="<?php echo esc_attr( $results_id ); ?>" aria-expanded="false">
						<?php if ( 'yes' === $settings['show_submit_button'] ) : ?>
							<button class="mw-ajax-search__submit" type="submit" aria-label="<?php echo esc_attr( $settings['submit_text'] ?: __( 'Search', 'moose-widgets' ) ); ?>">
								<?php if ( ! empty( $settings['submit_icon']['value'] ) ) : ?><span aria-hidden="true"><?php $this->render_search_icon( $settings['submit_icon'] ); ?></span><?php endif; ?>
								<?php if ( 'yes' === $settings['show_submit_text'] ) : ?><span><?php echo esc_html( $settings['submit_text'] ); ?></span><?php endif; ?>
							</button>
						<?php endif; ?>
					</form>
					<div id="<?php echo esc_attr( $results_id ); ?>" class="mw-ajax-search__results" role="listbox" aria-live="polite" hidden></div>
				</div>
			</div>
		</div>
		<?php
	}

	private function render_search_icon( array $icon ): void {
		$value = (string) ( $icon['value'] ?? '' );
		if ( '' === $value || 'fas fa-search' === $value ) {
			echo '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m16 16 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
			return;
		}
		Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
	}
}
