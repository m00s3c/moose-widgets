<?php
/**
 * Fullscreen Menu Elementor widget.
 *
 * @package MooseWidgets
 */

namespace MooseWidgets\Widgets\Fullscreen_Menu;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget extends Widget_Base {
	public function get_name(): string {
		return 'moose-fullscreen-menu';
	}

	public function get_title(): string {
		return esc_html__( 'Fullscreen Menu', 'moose-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-menu-bar';
	}

	public function get_categories(): array {
		return array( 'moose-widgets' );
	}

	public function get_keywords(): array {
		return array( 'menu', 'navigation', 'overlay', 'fullscreen', 'hamburger', 'moose' );
	}

	public function get_style_depends(): array {
		return array( 'moose-widgets-fullscreen-menu' );
	}

	public function get_script_depends(): array {
		return array( 'moose-widgets-fullscreen-menu' );
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_trigger_styles();
		$this->register_overlay_styles();
		$this->register_navigation_styles();
		$this->register_contact_styles();
		$this->register_social_styles();
		$this->register_button_styles();
		$this->register_close_styles();
	}

	private function register_content_controls(): void {
		$this->start_controls_section( 'section_trigger', array( 'label' => esc_html__( 'Trigger', 'moose-widgets' ) ) );
		$this->add_control( 'trigger_icon', array(
			'label' => esc_html__( 'Toggle Icon', 'moose-widgets' ), 'type' => Controls_Manager::ICONS,
			'default' => array( 'value' => 'fas fa-bars', 'library' => 'fa-solid' ),
			'description' => esc_html__( 'Choose an Elementor icon or upload your own SVG.', 'moose-widgets' ),
		) );
		$this->add_control( 'trigger_text', array( 'label' => esc_html__( 'Text', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'placeholder' => esc_html__( 'Menu', 'moose-widgets' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'trigger_aria_label', array( 'label' => esc_html__( 'Accessible Label', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Open menu', 'moose-widgets' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_navigation', array( 'label' => esc_html__( 'Navigation Sections', 'moose-widgets' ) ) );
		$sections = new Repeater();
		$sections->add_control( 'heading', array( 'label' => esc_html__( 'Heading', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Resources', 'moose-widgets' ), 'dynamic' => array( 'active' => true ) ) );
		$sections->add_control( 'links', array(
			'label' => esc_html__( 'Links', 'moose-widgets' ), 'type' => Controls_Manager::TEXTAREA,
			'default' => "Journal | /journal\nField Notes | /field-notes\nGuides | /guides",
			'description' => esc_html__( 'One link per line: Label | URL | new-tab. The third value is optional.', 'moose-widgets' ),
		) );
		$this->add_control( 'navigation_sections', array(
			'label' => esc_html__( 'Sections', 'moose-widgets' ), 'type' => Controls_Manager::REPEATER,
			'fields' => $sections->get_controls(), 'title_field' => '{{{ heading }}}',
			'default' => array(
				array( 'heading' => esc_html__( 'Explore', 'moose-widgets' ), 'links' => "Journal | #\nField Notes | #\nGuides | #" ),
				array( 'heading' => esc_html__( 'The Studio', 'moose-widgets' ), 'links' => "Our Approach | #\nSelected Work | #\nSay Hello | #" ),
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_contact', array( 'label' => esc_html__( 'Contact Section', 'moose-widgets' ) ) );
		$this->add_control( 'show_contact', array( 'label' => esc_html__( 'Show Contact', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'contact_title', array( 'label' => esc_html__( 'Title', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Start a Conversation', 'moose-widgets' ), 'condition' => array( 'show_contact' => 'yes' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'contact_icon', array( 'label' => esc_html__( 'Icon', 'moose-widgets' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'far fa-envelope', 'library' => 'fa-regular' ), 'condition' => array( 'show_contact' => 'yes' ) ) );
		$this->add_control( 'contact_description', array( 'label' => esc_html__( 'Description', 'moose-widgets' ), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__( 'Have a project in mind or just want to compare notes? Drop us a line and we will get back to you soon.', 'moose-widgets' ), 'condition' => array( 'show_contact' => 'yes' ), 'dynamic' => array( 'active' => true ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_social', array( 'label' => esc_html__( 'Social Icons', 'moose-widgets' ) ) );
		$this->add_control( 'social_title', array( 'label' => esc_html__( 'Title', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Find Us Online', 'moose-widgets' ) ) );
		$social = new Repeater();
		$social->add_control( 'icon', array( 'label' => esc_html__( 'Icon', 'moose-widgets' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fab fa-instagram', 'library' => 'fa-brands' ) ) );
		$social->add_control( 'url', array( 'label' => esc_html__( 'URL', 'moose-widgets' ), 'type' => Controls_Manager::URL, 'placeholder' => 'https://', 'options' => array( 'url', 'is_external', 'nofollow' ) ) );
		$social->add_control( 'label', array( 'label' => esc_html__( 'Accessible Label', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Instagram', 'moose-widgets' ) ) );
		$this->add_control( 'social_icons', array( 'label' => esc_html__( 'Icons', 'moose-widgets' ), 'type' => Controls_Manager::REPEATER, 'fields' => $social->get_controls(), 'title_field' => '{{{ label }}}', 'default' => array( array(), array( 'label' => 'Facebook', 'icon' => array( 'value' => 'fab fa-facebook-f', 'library' => 'fa-brands' ) ) ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_buttons', array( 'label' => esc_html__( 'Bottom Buttons', 'moose-widgets' ) ) );
		$buttons = new Repeater();
		$buttons->add_control( 'text', array( 'label' => esc_html__( 'Text', 'moose-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Learn More', 'moose-widgets' ) ) );
		$buttons->add_control( 'link', array( 'label' => esc_html__( 'Link', 'moose-widgets' ), 'type' => Controls_Manager::URL, 'options' => array( 'url', 'is_external', 'nofollow' ) ) );
		$buttons->add_control( 'icon', array( 'label' => esc_html__( 'Icon', 'moose-widgets' ), 'type' => Controls_Manager::ICONS ) );
		$buttons->add_control( 'style', array( 'label' => esc_html__( 'Style', 'moose-widgets' ), 'type' => Controls_Manager::SELECT, 'default' => 'primary', 'options' => array( 'primary' => esc_html__( 'Primary', 'moose-widgets' ), 'secondary' => esc_html__( 'Secondary', 'moose-widgets' ), 'outline' => esc_html__( 'Outline', 'moose-widgets' ) ) ) );
		$this->add_control( 'buttons', array( 'label' => esc_html__( 'Buttons (maximum 3)', 'moose-widgets' ), 'type' => Controls_Manager::REPEATER, 'fields' => $buttons->get_controls(), 'title_field' => '{{{ text }}}', 'prevent_empty' => false, 'default' => array() ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_behavior', array( 'label' => esc_html__( 'Behavior & Animation', 'moose-widgets' ) ) );
		$this->add_control( 'animation', array( 'label' => esc_html__( 'Animation', 'moose-widgets' ), 'type' => Controls_Manager::SELECT, 'default' => 'fade-up', 'options' => array( 'fade' => esc_html__( 'Fade', 'moose-widgets' ), 'fade-up' => esc_html__( 'Fade Up', 'moose-widgets' ), 'slide-right' => esc_html__( 'Slide Right', 'moose-widgets' ), 'zoom' => esc_html__( 'Zoom', 'moose-widgets' ) ) ) );
		$this->add_control( 'animation_duration', array( 'label' => esc_html__( 'Duration (ms)', 'moose-widgets' ), 'type' => Controls_Manager::NUMBER, 'default' => 420, 'min' => 100, 'max' => 1500, 'step' => 10, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__overlay' => '--mw-duration: {{VALUE}}ms;' ) ) );
		$this->add_control( 'close_on_backdrop', array( 'label' => esc_html__( 'Close on Backdrop Click', 'moose-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->end_controls_section();
	}

	private function register_trigger_styles(): void {
		$this->start_controls_section( 'style_trigger', array( 'label' => esc_html__( 'Trigger', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'trigger_alignment', array( 'label' => esc_html__( 'Alignment', 'moose-widgets' ), 'type' => Controls_Manager::CHOOSE, 'options' => array( 'flex-start' => array( 'title' => 'Left', 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => 'Center', 'icon' => 'eicon-text-align-center' ), 'flex-end' => array( 'title' => 'Right', 'icon' => 'eicon-text-align-right' ) ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu' => 'align-items: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'trigger_typography', 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__trigger' ) );

		$this->start_controls_tabs( 'trigger_state_tabs' );
		$this->start_controls_tab( 'trigger_normal_tab', array( 'label' => esc_html__( 'Normal', 'moose-widgets' ) ) );
		$this->add_control( 'trigger_color', array( 'label' => esc_html__( 'Text & Icon Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#15171f', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'trigger_background', array( 'label' => esc_html__( 'Background Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'trigger_border_color', array( 'label' => esc_html__( 'Border Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger' => 'border-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();

		$this->start_controls_tab( 'trigger_hover_tab', array( 'label' => esc_html__( 'Hover', 'moose-widgets' ) ) );
		$this->add_control( 'trigger_hover_color', array( 'label' => esc_html__( 'Text & Icon Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger:hover, {{WRAPPER}} .mw-fullscreen-menu__trigger:focus-visible' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'trigger_hover_background', array( 'label' => esc_html__( 'Background Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2563eb', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger:hover, {{WRAPPER}} .mw-fullscreen-menu__trigger:focus-visible' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'trigger_hover_border_color', array( 'label' => esc_html__( 'Border Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger:hover, {{WRAPPER}} .mw-fullscreen-menu__trigger:focus-visible' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'trigger_hover_movement', array( 'label' => esc_html__( 'Lift', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 20 ) ), 'default' => array( 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger:hover, {{WRAPPER}} .mw-fullscreen-menu__trigger:focus-visible' => 'transform: translateY(-{{SIZE}}{{UNIT}});' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control( 'trigger_icon_size', array( 'label' => esc_html__( 'Icon Size', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 8, 'max' => 100 ) ), 'default' => array( 'size' => 28 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger-icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mw-fullscreen-menu__trigger-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'trigger_gap', array( 'label' => esc_html__( 'Icon Gap', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'trigger_padding', array( 'label' => esc_html__( 'Padding', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'trigger_border', 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__trigger' ) );
		$this->add_responsive_control( 'trigger_radius', array( 'label' => esc_html__( 'Border Radius', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_control( 'trigger_transition', array( 'label' => esc_html__( 'Transition Duration (ms)', 'moose-widgets' ), 'type' => Controls_Manager::NUMBER, 'default' => 180, 'min' => 0, 'max' => 1000, 'step' => 10, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__trigger' => '--mw-trigger-duration: {{VALUE}}ms;' ) ) );
		$this->end_controls_section();
	}

	private function register_overlay_styles(): void {
		$this->start_controls_section( 'style_overlay', array( 'label' => esc_html__( 'Overlay & Layout', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'overlay_background', 'types' => array( 'classic', 'gradient' ), 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__overlay' ) );
		$this->add_control( 'overlay_blur', array( 'label' => esc_html__( 'Backdrop Blur', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__overlay' => 'backdrop-filter: blur({{SIZE}}{{UNIT}}); -webkit-backdrop-filter: blur({{SIZE}}{{UNIT}});' ) ) );
		$this->add_responsive_control( 'content_width', array( 'label' => esc_html__( 'Content Width', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%', 'vw' ), 'range' => array( 'px' => array( 'min' => 320, 'max' => 1800 ), '%' => array( 'min' => 30, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 1240 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__content' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'overlay_padding', array( 'label' => esc_html__( 'Content Padding', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%', 'vw' ), 'default' => array( 'top' => 64, 'right' => 40, 'bottom' => 32, 'left' => 40, 'unit' => 'px' ), 'tablet_default' => array( 'top' => 60, 'right' => 30, 'bottom' => 30, 'left' => 30, 'unit' => 'px' ), 'mobile_default' => array( 'top' => 68, 'right' => 22, 'bottom' => 26, 'left' => 22, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'columns', array( 'label' => esc_html__( 'Navigation Columns', 'moose-widgets' ), 'type' => Controls_Manager::SELECT, 'default' => '3', 'tablet_default' => '2', 'mobile_default' => '1', 'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__nav-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ) ) );
		$this->add_responsive_control( 'column_gap', array( 'label' => esc_html__( 'Column Gap', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'size' => 50 ), 'tablet_default' => array( 'size' => 36 ), 'mobile_default' => array( 'size' => 24 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__nav-grid' => 'column-gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'row_gap', array( 'label' => esc_html__( 'Row Gap', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'size' => 30 ), 'tablet_default' => array( 'size' => 26 ), 'mobile_default' => array( 'size' => 24 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__nav-grid' => 'row-gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'block_gap', array( 'label' => esc_html__( 'Content Block Spacing', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'size' => 30 ), 'tablet_default' => array( 'size' => 28 ), 'mobile_default' => array( 'size' => 26 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__body' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	private function register_navigation_styles(): void {
		$this->start_controls_section( 'style_navigation', array( 'label' => esc_html__( 'Headings & Links', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'heading_color', array( 'label' => esc_html__( 'Heading Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__section-title' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__section-title' ) );
		$this->add_responsive_control( 'heading_margin', array( 'label' => esc_html__( 'Heading Margin', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__section-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_control( 'link_color', array( 'label' => esc_html__( 'Link Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#94a3b8', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__link' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'link_hover_color', array( 'label' => esc_html__( 'Link Hover Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#60a5fa', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__link:hover, {{WRAPPER}} .mw-fullscreen-menu__link:focus-visible' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'link_typography', 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__link' ) );
		$this->add_responsive_control( 'link_spacing', array( 'label' => esc_html__( 'Link Spacing', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 7 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__links' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	private function register_contact_styles(): void {
		$this->start_controls_section( 'style_contact', array( 'label' => esc_html__( 'Contact', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_contact' => 'yes' ) ) );
		$this->add_control( 'contact_title_color', array( 'label' => esc_html__( 'Title Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__contact-title' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'contact_title_typography', 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__contact-title' ) );
		$this->add_control( 'contact_text_color', array( 'label' => esc_html__( 'Text Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#94a3b8', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__contact-copy' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'contact_typography', 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__contact-copy' ) );
		$this->add_control( 'contact_icon_color', array( 'label' => esc_html__( 'Icon Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__contact-icon' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'contact_icon_bg', array( 'label' => esc_html__( 'Icon Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2563eb', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__contact-icon' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'contact_icon_background_size', array(
			'label' => esc_html__( 'Background Size', 'moose-widgets' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'min' => 24, 'max' => 200 ) ),
			'default' => array( 'unit' => 'px', 'size' => 52 ),
			'tablet_default' => array( 'unit' => 'px', 'size' => 48 ),
			'mobile_default' => array( 'unit' => 'px', 'size' => 44 ),
			'selectors' => array(
				'{{WRAPPER}} .mw-fullscreen-menu__contact-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'contact_icon_size', array( 'label' => esc_html__( 'Icon Size', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 20 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__contact-icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mw-fullscreen-menu__contact-icon svg' => 'width: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	private function register_social_styles(): void {
		$this->start_controls_section( 'style_social', array( 'label' => esc_html__( 'Social Icons', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'social_color', array( 'label' => esc_html__( 'Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#e2e8f0', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__social-link' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'social_hover_color', array( 'label' => esc_html__( 'Hover Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__social-link:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'social_border_color', array( 'label' => esc_html__( 'Border Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#475569', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__social-link' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'social_hover_bg', array( 'label' => esc_html__( 'Hover Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2563eb', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__social-link:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'social_size', array( 'label' => esc_html__( 'Size', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 38 ), 'range' => array( 'px' => array( 'min' => 24, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__social-link' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'social_gap', array( 'label' => esc_html__( 'Gap', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 12 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__social-list' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	private function register_button_styles(): void {
		$this->start_controls_section( 'style_buttons', array( 'label' => esc_html__( 'Buttons', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'button_typography', 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__button' ) );
		$this->add_control( 'button_primary_color', array( 'label' => esc_html__( 'Primary Text', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button--primary' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'button_primary_bg', array( 'label' => esc_html__( 'Primary Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2563eb', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button--primary' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'button_secondary_color', array( 'label' => esc_html__( 'Secondary Text', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button--secondary' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'button_secondary_bg', array( 'label' => esc_html__( 'Secondary Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#334155', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button--secondary' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'button_outline_color', array( 'label' => esc_html__( 'Outline Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#94a3b8', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button--outline' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'button_hover_bg', array( 'label' => esc_html__( 'Hover Background', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#1d4ed8', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'button_hover_color', array( 'label' => esc_html__( 'Hover Text', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'button_padding', array( 'label' => esc_html__( 'Padding', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 11, 'right' => 20, 'bottom' => 11, 'left' => 20, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'button_radius', array( 'label' => esc_html__( 'Border Radius', 'moose-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'default' => array( 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'button_gap', array( 'label' => esc_html__( 'Gap', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 16 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__buttons' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'button_shadow', 'selector' => '{{WRAPPER}} .mw-fullscreen-menu__button' ) );
		$this->end_controls_section();
	}

	private function register_close_styles(): void {
		$this->start_controls_section( 'style_close', array( 'label' => esc_html__( 'Close Button', 'moose-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'close_color', array( 'label' => esc_html__( 'Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__close' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'close_hover_color', array( 'label' => esc_html__( 'Hover Color', 'moose-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#60a5fa', 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__close:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'close_size', array( 'label' => esc_html__( 'Icon Size', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 34 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'close_top', array( 'label' => esc_html__( 'Top Position', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 32 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__close' => 'top: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'close_right', array( 'label' => esc_html__( 'Right Position', 'moose-widgets' ), 'type' => Controls_Manager::SLIDER, 'default' => array( 'size' => 40 ), 'selectors' => array( '{{WRAPPER}} .mw-fullscreen-menu__close' => 'right: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings   = $this->get_settings_for_display();
		$overlay_id = 'mw-fullscreen-menu-' . $this->get_id();
		?>
		<div class="mw-fullscreen-menu">
			<button class="mw-fullscreen-menu__trigger" type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="<?php echo esc_attr( $overlay_id ); ?>" aria-label="<?php echo esc_attr( $settings['trigger_aria_label'] ?: __( 'Open menu', 'moose-widgets' ) ); ?>">
				<span class="mw-fullscreen-menu__trigger-icon" aria-hidden="true"><?php $this->render_trigger_icon( $settings['trigger_icon'] ?? array() ); ?></span>
				<?php if ( '' !== trim( (string) $settings['trigger_text'] ) ) : ?><span><?php echo esc_html( $settings['trigger_text'] ); ?></span><?php endif; ?>
			</button>
			<div id="<?php echo esc_attr( $overlay_id ); ?>" class="mw-fullscreen-menu__overlay mw-fullscreen-menu__overlay--<?php echo esc_attr( $settings['animation'] ); ?>" role="dialog" aria-modal="true" aria-hidden="true" aria-label="<?php echo esc_attr__( 'Fullscreen navigation', 'moose-widgets' ); ?>" data-close-backdrop="<?php echo 'yes' === $settings['close_on_backdrop'] ? 'true' : 'false'; ?>" hidden>
				<button class="mw-fullscreen-menu__close" type="button" aria-label="<?php echo esc_attr__( 'Close menu', 'moose-widgets' ); ?>"><span aria-hidden="true"></span></button>
				<div class="mw-fullscreen-menu__content" tabindex="-1">
					<div class="mw-fullscreen-menu__body">
						<?php $this->render_navigation( $settings['navigation_sections'] ); ?>
						<?php $this->render_contact( $settings ); ?>
						<?php $this->render_social( $settings ); ?>
						<?php $this->render_buttons( array_slice( $settings['buttons'], 0, 3 ) ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a dependency-free hamburger for the widget default.
	 *
	 * Older saved widgets use the Font Awesome `fas fa-bars` default. Treat that
	 * value as the built-in icon too, because Font Awesome may be disabled by
	 * Elementor's Inline Font Icons experiment on the public site.
	 */
	private function render_trigger_icon( array $icon ): void {
		$value = (string) ( $icon['value'] ?? '' );

		if ( '' === $value || 'mw-default-hamburger' === $value || 'fas fa-bars' === $value ) {
			?>
			<svg class="mw-fullscreen-menu__hamburger" viewBox="0 0 24 24" width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
				<path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
			</svg>
			<?php
			return;
		}

		Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
	}

	private function render_navigation( array $sections ): void {
		if ( empty( $sections ) ) { return; }
		echo '<nav class="mw-fullscreen-menu__nav" aria-label="' . esc_attr__( 'Main navigation', 'moose-widgets' ) . '"><div class="mw-fullscreen-menu__nav-grid">';
		foreach ( $sections as $section ) {
			$links = $this->parse_links( (string) ( $section['links'] ?? '' ) );
			echo '<section class="mw-fullscreen-menu__section">';
			if ( ! empty( $section['heading'] ) ) { echo '<h2 class="mw-fullscreen-menu__section-title">' . esc_html( $section['heading'] ) . '</h2>'; }
			if ( $links ) {
				echo '<ul class="mw-fullscreen-menu__links">';
				foreach ( $links as $link ) {
					$target = $link['new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : '';
					echo '<li><a class="mw-fullscreen-menu__link" href="' . esc_url( $link['url'] ) . '"' . $target . '>' . esc_html( $link['label'] ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				echo '</ul>';
			}
			echo '</section>';
		}
		echo '</div></nav>';
	}

	private function parse_links( string $value ): array {
		$links = array();
		foreach ( preg_split( '/\R/', $value ) ?: array() as $line ) {
			$parts = array_map( 'trim', explode( '|', $line ) );
			if ( empty( $parts[0] ) ) { continue; }
			$links[] = array( 'label' => $parts[0], 'url' => $parts[1] ?? '#', 'new_tab' => isset( $parts[2] ) && in_array( strtolower( $parts[2] ), array( 'new-tab', 'blank', 'yes' ), true ) );
		}
		return $links;
	}

	private function render_contact( array $settings ): void {
		if ( 'yes' !== $settings['show_contact'] ) { return; }
		echo '<section class="mw-fullscreen-menu__contact">';
		if ( $settings['contact_title'] ) { echo '<h2 class="mw-fullscreen-menu__contact-title">' . esc_html( $settings['contact_title'] ) . '</h2>'; }
		echo '<div class="mw-fullscreen-menu__contact-row"><span class="mw-fullscreen-menu__contact-icon" aria-hidden="true">';
		Icons_Manager::render_icon( $settings['contact_icon'], array( 'aria-hidden' => 'true' ) );
		echo '</span><div class="mw-fullscreen-menu__contact-copy">' . wp_kses_post( $settings['contact_description'] ) . '</div></div></section>';
	}

	private function render_social( array $settings ): void {
		if ( empty( $settings['social_icons'] ) ) { return; }
		echo '<section class="mw-fullscreen-menu__social">';
		if ( $settings['social_title'] ) { echo '<h2 class="mw-fullscreen-menu__section-title">' . esc_html( $settings['social_title'] ) . '</h2>'; }
		echo '<div class="mw-fullscreen-menu__social-list">';
		foreach ( $settings['social_icons'] as $item ) {
			$url = $item['url']['url'] ?? '';
			if ( ! $url ) { continue; }
			$target = ! empty( $item['url']['is_external'] ) ? ' target="_blank"' : '';
			$rel = array_filter( array( ! empty( $item['url']['nofollow'] ) ? 'nofollow' : '', ! empty( $item['url']['is_external'] ) ? 'noopener' : '' ) );
			echo '<a class="mw-fullscreen-menu__social-link" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $item['label'] ?: __( 'Social profile', 'moose-widgets' ) ) . '"' . $target . ( $rel ? ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) );
			echo '</a>';
		}
		echo '</div></section>';
	}

	private function render_buttons( array $buttons ): void {
		if ( empty( $buttons ) ) { return; }
		echo '<div class="mw-fullscreen-menu__buttons">';
		foreach ( $buttons as $button ) {
			if ( empty( $button['text'] ) ) { continue; }
			$url = $button['link']['url'] ?? '#';
			$target = ! empty( $button['link']['is_external'] ) ? ' target="_blank"' : '';
			$rel = ! empty( $button['link']['nofollow'] ) ? ' rel="nofollow"' : '';
			echo '<a class="mw-fullscreen-menu__button mw-fullscreen-menu__button--' . esc_attr( $button['style'] ) . '" href="' . esc_url( $url ) . '"' . $target . $rel . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( ! empty( $button['icon']['value'] ) ) { Icons_Manager::render_icon( $button['icon'], array( 'aria-hidden' => 'true' ) ); }
			echo '<span>' . esc_html( $button['text'] ) . '</span></a>';
		}
		echo '</div>';
	}
}
