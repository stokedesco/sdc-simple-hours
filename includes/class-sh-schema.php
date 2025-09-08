<?php
class SH_Schema {
    public function __construct(){
        if ( get_option( SH_Settings::OPTION_SCHEMA ) ) {
            add_action('wp_head', array($this,'output_schema'));
        }
    }
    public function output_schema(){
        list($weekly, $holidays) = SH_Shortcodes::get_data();
        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => get_option( SH_Settings::OPTION_SCHEMA_TYPE, 'LocalBusiness' ),
            'name'     => get_option( SH_Settings::OPTION_SCHEMA_NAME, get_bloginfo('name') ),
            'openingHoursSpecification' => array(),
        );

        if (is_array($weekly)) {
            foreach ($weekly as $day => $v) {
                if (!empty($v['closed'])) {
                    continue;
                }
                if (!empty($v['open']) && !empty($v['close'])) {
                    $schema['openingHoursSpecification'][] = array(
                        '@type'     => 'OpeningHoursSpecification',
                        'dayOfWeek' => $day,
                        'opens'     => $v['open'],
                        'closes'    => $v['close'],
                    );
                }
                if (!empty($v['open2']) && !empty($v['close2'])) {
                    $schema['openingHoursSpecification'][] = array(
                        '@type'     => 'OpeningHoursSpecification',
                        'dayOfWeek' => $day,
                        'opens'     => $v['open2'],
                        'closes'    => $v['close2'],
                    );
                }
            }
        }

        if (is_array($holidays)) {
            foreach ($holidays as $h) {
                if (isset($h['closed'])) {
                    continue;
                }
                $schema['openingHoursSpecification'][] = array(
                    '@type'        => 'OpeningHoursSpecification',
                    'validFrom'    => $h['from'],
                    'validThrough' => $h['to'],
                    'opens'        => $h['open'],
                    'closes'       => $h['close'],
                );
            }
        }

        if (! empty($schema['openingHoursSpecification'])) {
            echo "<script type='application/ld+json'>" . wp_json_encode($schema) . "</script>";
        }
    }
}
new SH_Schema();
