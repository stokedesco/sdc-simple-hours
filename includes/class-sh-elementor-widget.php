<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SH_Elementor_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'simple_hours';
    }

    public function get_title() {
        return __( 'Stoke Simple Hours', 'simple-hours' );
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
                'today'    => __( "Today's Hours", 'simple-hours' ),
                'until'    => __( 'Open Today Until', 'simple-hours' ),
                'fullweek' => __( 'Full Week Table', 'simple-hours' ),
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

        $this->add_responsive_control( 'text_align', [
            'label'     => __( 'Text Alignment', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => __( 'Left', 'simple-hours' ),   'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'simple-hours' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'simple-hours' ),  'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .simple-hours-output' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'icon_heading', [
            'label'     => __( 'Icon', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_control( 'text_icon', [
            'label' => __( 'Icon', 'simple-hours' ),
            'type'  => \Elementor\Controls_Manager::ICONS,
        ] );

        $this->add_control( 'icon_position', [
            'label'   => __( 'Icon Position', 'simple-hours' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'prepend' => __( 'Prepend', 'simple-hours' ),
                'append'  => __( 'Append', 'simple-hours' ),
            ],
            'default' => 'prepend',
            'condition' => [ 'text_icon[value]!' => '' ],
        ] );

        $this->add_control( 'icon_color_open', [
            'label'     => __( 'Icon Color (Open)', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'condition' => [ 'text_icon[value]!' => '' ],
            'selectors' => [
                '{{WRAPPER}} .simple-hours-output .simple-hours-icon-open' => 'color: {{VALUE}};',
                '{{WRAPPER}} .simple-hours-output .simple-hours-icon-open i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .simple-hours-output .simple-hours-icon-open svg' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
                '{{WRAPPER}} .simple-hours-output .simple-hours-icon-open svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'icon_color_closed', [
            'label'     => __( 'Icon Color (Closed)', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'condition' => [ 'text_icon[value]!' => '' ],
            'selectors' => [
                '{{WRAPPER}} .simple-hours-output .simple-hours-icon-closed' => 'color: {{VALUE}};',
                '{{WRAPPER}} .simple-hours-output .simple-hours-icon-closed i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .simple-hours-output .simple-hours-icon-closed svg' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
                '{{WRAPPER}} .simple-hours-output .simple-hours-icon-closed svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'icon_size', [
            'label' => __( 'Icon Size', 'simple-hours' ),
            'type'  => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem', '%' ],
            'range' => [
                'px'  => [ 'min' => 6,  'max' => 100 ],
                'em'  => [ 'min' => 0.5, 'max' => 10 ],
                'rem' => [ 'min' => 0.5, 'max' => 10 ],
                '%'   => [ 'min' => 10, 'max' => 200 ],
            ],
            'default' => [ 'size' => 20, 'unit' => 'px' ],
            'condition' => [ 'text_icon[value]!' => '' ],
            'selectors' => [

                '{{WRAPPER}} .simple-hours-output .simple-hours-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',

                '{{WRAPPER}} .simple-hours-output .simple-hours-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'table_style', [
            'label'     => __( 'Table', 'simple-hours' ),
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'format' => 'fullweek' ],
        ] );

        $this->add_control( 'heading_structure', [
            'label' => __( 'Structure', 'simple-hours' ),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ] );

        $this->add_control( 'table_width', [
            'label' => __( 'Table Width', 'simple-hours' ),
            'type'  => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ '%', 'px' ],
            'range' => [
                '%' => [ 'min' => 10, 'max' => 100 ],
                'px' => [ 'min' => 0, 'max' => 1000 ],
            ],
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'heading_typography', [
            'label'     => __( 'Typography', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'day_typography',
            'label'    => __( 'Day Typography', 'simple-hours' ),
            'selector' => '{{WRAPPER}} table.simple-hours-table th',
        ] );

        $this->add_control( 'day_color', [
            'label'     => __( 'Day Text Color', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table th' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'day_align', [
            'label'     => __( 'Day Text Align', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => __( 'Left', 'simple-hours' ),   'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'simple-hours' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'simple-hours' ),  'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table th' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'time_typography',
            'label'    => __( 'Time Typography', 'simple-hours' ),
            'selector' => '{{WRAPPER}} table.simple-hours-table td',
        ] );

        $this->add_control( 'time_color', [
            'label'     => __( 'Time Text Color', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table td' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'time_align', [
            'label'     => __( 'Time Text Align', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => __( 'Left', 'simple-hours' ),   'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'simple-hours' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'simple-hours' ),  'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table td' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'heading_backgrounds', [
            'label'     => __( 'Backgrounds', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_control( 'row_color_1', [
            'label'     => __( 'Row Colour 1', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table tr:nth-child(odd)' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'row_color_2', [
            'label'     => __( 'Row Colour 2', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table tr:nth-child(even)' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'heading_borders', [
            'label'     => __( 'Borders', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'outer_border',
            'label'    => __( 'Table Border', 'simple-hours' ),
            'selector' => '{{WRAPPER}} table.simple-hours-table',
        ] );

        $this->add_control( 'row_border_color', [
            'label'     => __( 'Row Border Color', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table tr' => 'border-bottom-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'row_border_style', [
            'label'   => __( 'Row Border Style', 'simple-hours' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'none'   => __( 'None', 'simple-hours' ),
                'solid'  => __( 'Solid', 'simple-hours' ),
                'dashed' => __( 'Dashed', 'simple-hours' ),
                'dotted' => __( 'Dotted', 'simple-hours' ),
                'double' => __( 'Double', 'simple-hours' ),
            ],
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table tr' => 'border-bottom-style: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'row_border_width', [
            'label' => __( 'Row Border Width', 'simple-hours' ),
            'type'  => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range' => [ 'px' => [ 'min' => 0, 'max' => 10 ] ],
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table tr' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'vertical_border_color', [
            'label'     => __( 'Vertical Border Color', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table th, {{WRAPPER}} table.simple-hours-table td' => 'border-right-color: {{VALUE}};',
                '{{WRAPPER}} table.simple-hours-table th:last-child, {{WRAPPER}} table.simple-hours-table td:last-child' => 'border-right-color: transparent;',
            ],
        ] );

        $this->add_control( 'vertical_border_style', [
            'label'   => __( 'Vertical Border Style', 'simple-hours' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'none'   => __( 'None', 'simple-hours' ),
                'solid'  => __( 'Solid', 'simple-hours' ),
                'dashed' => __( 'Dashed', 'simple-hours' ),
                'dotted' => __( 'Dotted', 'simple-hours' ),
                'double' => __( 'Double', 'simple-hours' ),
            ],
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table th, {{WRAPPER}} table.simple-hours-table td' => 'border-right-style: {{VALUE}};',
                '{{WRAPPER}} table.simple-hours-table th:last-child, {{WRAPPER}} table.simple-hours-table td:last-child' => 'border-right-style: none;',
            ],
        ] );

        $this->add_control( 'vertical_border_width', [
            'label' => __( 'Vertical Border Width', 'simple-hours' ),
            'type'  => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range' => [ 'px' => [ 'min' => 0, 'max' => 10 ] ],
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table th, {{WRAPPER}} table.simple-hours-table td' => 'border-right-width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} table.simple-hours-table th:last-child, {{WRAPPER}} table.simple-hours-table td:last-child' => 'border-right-width: 0;',
            ],
        ] );

        $this->add_control( 'heading_current_day', [
            'label'     => __( 'Current Day', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_control( 'current_day_bg', [
            'label'     => __( 'Current Day Background', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table tr.simple-hours-current-day' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'current_day_typography',
            'label'    => __( 'Current Day Typography', 'simple-hours' ),
            'selector' => '{{WRAPPER}} table.simple-hours-table tr.simple-hours-current-day th, {{WRAPPER}} table.simple-hours-table tr.simple-hours-current-day td',
        ] );

        $this->add_control( 'current_day_color', [
            'label'     => __( 'Current Day Text Color', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table tr.simple-hours-current-day, {{WRAPPER}} table.simple-hours-table tr.simple-hours-current-day th, {{WRAPPER}} table.simple-hours-table tr.simple-hours-current-day td' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'current_day_align', [
            'label'     => __( 'Current Day Text Align', 'simple-hours' ),
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => __( 'Left', 'simple-hours' ),   'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'simple-hours' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'simple-hours' ),  'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [
                '{{WRAPPER}} table.simple-hours-table tr.simple-hours-current-day th, {{WRAPPER}} table.simple-hours-table tr.simple-hours-current-day td' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $format   = isset( $settings['format'] ) ? $settings['format'] : 'today';

        $icon = '';
        if ( ! empty( $settings['text_icon']['value'] ) ) {
            $is_open    = SH_Shortcodes::is_open();
            $icon_class = $is_open
                ? 'elementor-icon simple-hours-icon simple-hours-icon-open'
                : 'elementor-icon simple-hours-icon simple-hours-icon-closed';

            ob_start();
            \Elementor\Icons_Manager::render_icon(
                $settings['text_icon'],
                [ 'aria-hidden' => 'true', 'class' => $icon_class ],
                'span'
            );
            $icon = ob_get_clean();
        }

        switch ( $format ) {
            case 'until':
                $out = SH_Shortcodes::until();
                if ( $icon ) {
                    $out = ( 'append' === $settings['icon_position'] ) ? $out . $icon : $icon . $out;
                }
                echo '<div class="simple-hours-output">' . $out . '</div>';
                break;
            case 'fullweek':
                $out = SH_Shortcodes::fullweek();
                echo $out;
                break;
            case 'today':
            default:
                $out = SH_Shortcodes::today();
                if ( $icon ) {
                    $out = ( 'append' === $settings['icon_position'] ) ? $out . $icon : $icon . $out;
                }
                echo '<div class="simple-hours-output">' . $out . '</div>';
                break;
        }
    }
}
