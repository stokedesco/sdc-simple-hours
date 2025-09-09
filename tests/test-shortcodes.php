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
        update_option('sh_second_hours', 0);
        update_option('sh_time_format', '24');
        update_option('sh_holiday_pre_before', 'We will be closed for the');
        update_option('sh_holiday_pre_during', 'We are closed for the');
        update_option('sh_holiday_post', 'reopening on');
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

    public function test_second_hours() {
        update_option('sh_second_hours', 1);
        update_option('sh_weekly_hours', array(
            'Monday' => array('open' => '09:00', 'close' => '14:00', 'open2' => '17:00', 'close2' => '22:00'),
            'Tuesday' => array('closed' => 1),
            'Wednesday' => array('closed' => 1),
            'Thursday' => array('closed' => 1),
            'Friday' => array('closed' => 1),
            'Saturday' => array('closed' => 1),
            'Sunday' => array('closed' => 1),
        ));

        $evening_time = strtotime('2023-06-26 18:00:00'); // Monday 18:00
        $this->assertTrue( SH_Shortcodes::is_open( $evening_time ) );

        $output = do_shortcode('[simplehours_fullweek]');
        $this->assertStringContainsString('17:00 - 22:00', $output);
    }

    public function test_time_format_12_hour() {
        update_option('sh_time_format', '12');
        $output = do_shortcode('[simplehours_fullweek]');
        $this->assertStringContainsString('9:00 AM - 5:00 PM', $output);
    }

    public function test_is_open_respects_timezone() {
        update_option('timezone_string', 'America/New_York');
        $dt = new DateTime('2023-06-26 16:23:00', new DateTimeZone('America/New_York'));
        $ts = $dt->getTimestamp();
        $this->assertTrue( SH_Shortcodes::is_open( $ts ) );
    }

    public function test_fullweek_shows_upcoming_holiday_message() {
        $today = wp_date('Y-m-d');
        $from = wp_date('Y-m-d', strtotime($today . ' +7 days'));
        $to   = wp_date('Y-m-d', strtotime($today . ' +10 days'));
        update_option('sh_holiday_overrides', array(
            array('from' => $from, 'to' => $to, 'label' => 'Test Holiday', 'closed' => 1)
        ));
        $output = do_shortcode('[simplehours_fullweek]');
        $this->assertStringContainsString('Test Holiday', $output);
    }

    public function test_fullweek_shows_active_holiday_message() {
        $today = wp_date('Y-m-d');
        $to   = wp_date('Y-m-d', strtotime($today . ' +3 days'));
        update_option('sh_holiday_overrides', array(
            array('from' => $today, 'to' => $to, 'label' => 'Active Holiday', 'closed' => 1)
        ));
        $output = do_shortcode('[simplehours_fullweek]');
        $this->assertStringContainsString('Active Holiday', $output);
        $this->assertStringContainsString('We are closed for the', $output);
    }
}
