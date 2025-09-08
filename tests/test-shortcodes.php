<?php
class SimpleHours_Shortcodes_Test extends WP_UnitTestCase {
    public function setUp(): void {
        parent::setUp();
        update_option('sh_weekly_hours', array(
            'Monday' => array('open' => '09:00', 'close' => '17:00'),
            'Tuesday' => array('open' => '09:00', 'close' => '17:00'),
            'Wednesday' => array('open' => '09:00', 'close' => '17:00'),
            'Thursday' => array('open' => '09:00', 'close' => '17:00'),
            'Friday' => array('open' => '09:00', 'close' => '17:00'),
            'Saturday' => array('closed' => 1),
            'Sunday' => array('closed' => 1)
        ));
        update_option('sh_holiday_overrides', array());
    }

    public function test_today_shortcode_outputs_text() {
        $output = do_shortcode('[simplehours_today]');
        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

    public function test_fullweek_has_current_day_class() {
        $output = do_shortcode('[simplehours_fullweek]');
        $this->assertStringContainsString('simple-hours-current-day', $output);
    }
}
