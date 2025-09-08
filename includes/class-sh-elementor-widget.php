<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SH_Elementor_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'simple_hours';
    }

    public function get_title() {
        return __( 'Simple Hours', 'simple-hours' );
    }

    public function get_icon() {
        return 'eicon-clock';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Content', 'simple-hours' ),
        ] );

        $this->add_control( 'format', [
            'label'   => __( 'Format', 'simple-hours' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'today',
            'options' => [
                'today'    => __( 'Today', 'simple-hours' ),
                'until'    => __( 'Until', 'simple-hours' ),
                'fullweek' => __( 'Full Week', 'simple-hours' ),
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'text_style', [
            'label'     => __( 'Text', 'simple-hours' ),
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'format!' => 'fullweek' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'text_typography',
            'selector' => '{{WRAPPER}} .simple-hours-output',
        ] );

        $this->add_control( 'text_color', [
            'label'     => __( 'Text Color', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .simple-hours-output' => 'color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'table_style', [
            'label'     => __( 'Table', 'simple-hours' ),
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'format' => 'fullweek' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'table_typography',
            'selector' => '{{WRAPPER}} table.simple-hours-table, {{WRAPPER}} table.simple-hours-table th, {{WRAPPER}} table.simple-hours-table td',
        ] );

        $this->add_control( 'table_text_color', [
            'label'     => __( 'Text Color', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'table_bg_color', [
            'label'     => __( 'Background Color', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'table_border',
            'selector' => '{{WRAPPER}} table.simple-hours-table, {{WRAPPER}} table.simple-hours-table th, {{WRAPPER}} table.simple-hours-table td',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $format   = isset( $settings['format'] ) ? $settings['format'] : 'today';
        switch ( $format ) {
            case 'until':
                $out = SH_Shortcodes::until();
                echo '<div class="simple-hours-output">' . $out . '</div>';
                break;
            case 'fullweek':
                $out = SH_Shortcodes::fullweek();
                $out = str_replace( '<table>', '<table class="simple-hours-table">', $out );
                echo $out;
                break;
            case 'today':
            default:
                $out = SH_Shortcodes::today();
                echo '<div class="simple-hours-output">' . $out . '</div>';
                break;
        }
    }
}
