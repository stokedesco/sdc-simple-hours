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

    public function test_is_open() {
        $open_time  = strtotime('2023-06-26 10:00:00'); // Monday 10:00
        $closed_time = strtotime('2023-06-26 18:00:00'); // Monday 18:00
        $weekend_time = strtotime('2023-06-24 10:00:00'); // Saturday

        $this->assertTrue( SH_Shortcodes::is_open( $open_time ) );
        $this->assertFalse( SH_Shortcodes::is_open( $closed_time ) );
        $this->assertFalse( SH_Shortcodes::is_open( $weekend_time ) );
    }
}
