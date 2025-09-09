<?php
class SH_Shortcodes {
    public static function init(){
        add_shortcode('simplehours_today', array(__CLASS__,'today'));
        add_shortcode('simplehours_until', array(__CLASS__,'until'));
        add_shortcode('simplehours_fullweek', array(__CLASS__,'fullweek'));
        add_shortcode('holiday-message', array(__CLASS__,'holiday_message'));
    }

    public static function get_data(){
        $weekly = get_option(SH_Settings::OPTION_WEEKLY, array());
        $holidays = get_option(SH_Settings::OPTION_HOLIDAYS, array());
        return array($weekly, $holidays);
    }

    public static function today(){
        list($weekly, $holidays) = self::get_data();
        $today = wp_date('Y-m-d');

        if (is_array($holidays)) {
            foreach ($holidays as $h) {
                if ($today >= ($h['from'] ?? '') && $today <= ($h['to'] ?? '')){
                    if (isset($h['closed'])) {
                        $start  = $h['start'] ?? '';
                        $finish = $h['finish'] ?? '';
                        if (!$start || !$finish) {
                            return "Sorry, we're closed today ({$h['label']}).";
                        }
                        break;
                    }
                }
            }
        }

        $intervals = self::get_intervals_for_date($weekly, $holidays, $today);

        if (empty($intervals)) {
            return "Sorry, we're closed today.";
        }

        $parts = array();
        foreach ($intervals as $i) {
            $parts[] = self::format_time($i[0]) . ' to ' . self::format_time($i[1]);
        }
        return "We're open from " . implode(' and ', $parts) . ".";
    }

    public static function until(){
        list($weekly, $holidays) = self::get_data();
        $now      = new DateTime('now', wp_timezone());
        $today    = $now->format('Y-m-d');
        $time     = $now->format('H:i');
        $intervals = self::get_intervals_for_date($weekly, $holidays, $today);

        foreach ($intervals as $i) {
            if ($time >= $i[0] && $time < $i[1]) {
                return "Open today until " . self::format_time($i[1]) . ".";
            }
            if ($time < $i[0]) {
                return "Next open at " . self::format_time($i[0]) . " today.";
            }
        }

        $next_date = new DateTime('now', wp_timezone());
        for ($i=1;$i<=7;$i++){
            $d    = $next_date->add(new DateInterval('P1D'))->format('Y-m-d');
            $ints = self::get_intervals_for_date($weekly, $holidays, $d);
            if (!empty($ints)) {
                $dt = new DateTime($d, wp_timezone());
                $dn = $dt->format('l');
                return "Next open at " . self::format_time($ints[0][0]) . " on {$dn}.";
            }
        }
    }

    private static function get_open_time($weekly, $holidays, $date){
        $ints = self::get_intervals_for_date($weekly, $holidays, $date);
        return !empty($ints) ? $ints[0][0] : '';
    }

    private static function get_intervals_for_date($weekly, $holidays, $date){
        $base = self::get_weekly_intervals($weekly, $date);

        if (is_array($holidays)) {
            foreach ($holidays as $h) {
                if ($date >= ($h['from'] ?? '') && $date <= ($h['to'] ?? '')){
                    if (isset($h['closed'])) {
                        $start  = $h['start'] ?? '';
                        $finish = $h['finish'] ?? '';
                        if ($start && $finish) {
                            return self::subtract_interval($base, $start, $finish);
                        }
                        return array();
                    }
                }
            }
        }
        return $base;
    }

    private static function get_weekly_intervals($weekly, $date){
        $dt = new DateTime($date, wp_timezone());
        $dn = $dt->format('l');
        if (!isset($weekly[$dn]) || !empty($weekly[$dn]['closed'])){
            return array();
        }
        $out = array();
        if (!empty($weekly[$dn]['open']) && !empty($weekly[$dn]['close'])) {
            $out[] = array($weekly[$dn]['open'], $weekly[$dn]['close']);
        }
        if (!empty($weekly[$dn]['open2']) && !empty($weekly[$dn]['close2'])) {
            $out[] = array($weekly[$dn]['open2'], $weekly[$dn]['close2']);
        }
        return $out;
    }

    private static function subtract_interval($intervals, $start, $finish){
        $out = array();
        foreach ($intervals as $int){
            list($o, $c) = $int;
            if ($finish <= $o || $start >= $c){
                $out[] = $int;
                continue;
            }
            if ($start > $o){
                $out[] = array($o, $start);
            }
            if ($finish < $c){
                $out[] = array($finish, $c);
            }
        }
        return $out;
    }

    private static function format_time($time){
        $format = get_option(SH_Settings::OPTION_TIME_FORMAT, '24');
        if ($format === '12') {
            $dt = DateTime::createFromFormat('H:i', $time);
            if ($dt) {
                return $dt->format('g:i A');
            }
        }
        return $time;
    }

    public static function is_open($timestamp = null){
        list($weekly, $holidays) = self::get_data();


        $tz = wp_timezone();

        if ($timestamp) {
            $now = new DateTime("@{$timestamp}");
            $now->setTimezone($tz);
        } else {
            $now = new DateTime('now', $tz);
        }

        $date = $now->format('Y-m-d');
        $time = $now->format('H:i');

        $ints = self::get_intervals_for_date($weekly, $holidays, $date);
        foreach ($ints as $i) {
            if ($time >= $i[0] && $time < $i[1]) return true;
        }
        return false;
    }

    public static function fullweek(){
        list($weekly, $holidays) = self::get_data();
        $out         = '<table class="simple-hours-table">';
        $current_day = wp_date('l');
        $second      = get_option(SH_Settings::OPTION_SECOND, false);

        if (is_array($weekly)) {
            foreach ($weekly as $day => $v) {
                $row_class = $day === $current_day ? ' class="simple-hours-current-day"' : '';
                if (!empty($v['closed'])) {
                    $hours1 = 'Closed';
                    $hours2 = '';
                } else {
                    $hours1 = !empty($v['open']) && !empty($v['close']) ? self::format_time($v['open']) . ' - ' . self::format_time($v['close']) : '';
                    $hours2 = ($second && !empty($v['open2']) && !empty($v['close2'])) ? self::format_time($v['open2']) . ' - ' . self::format_time($v['close2']) : '';
                }
                if ($second) {
                    $out .= "<tr{$row_class}><th class=\"simple-hours-day\">$day</th><td class=\"simple-hours-time\">$hours1</td><td class=\"simple-hours-time\">$hours2</td></tr>";
                } else {
                    $out .= "<tr{$row_class}><th class=\"simple-hours-day\">$day</th><td class=\"simple-hours-time\">$hours1</td></tr>";
                }
            }
        }

        $out .= '</table>';
        return $out;
    }

    private static function get_holiday_message($weekly, $holidays){
        if (!is_array($holidays)) return '';
        $today = wp_date('Y-m-d');
        $limit = wp_date('Y-m-d', strtotime($today . ' +14 days'));
        $pre_before = get_option(SH_Settings::OPTION_HOLIDAY_PRE_BEFORE, 'We will be closed for the');
        $pre_during = get_option(SH_Settings::OPTION_HOLIDAY_PRE_DURING, 'We are closed for the');
        $post = get_option(SH_Settings::OPTION_HOLIDAY_POST, 'reopening on');

        foreach ($holidays as $h) {
            if (empty($h['closed'])) continue;
            $from = $h['from'] ?? '';
            $to   = $h['to'] ?? '';
            if (!$from || !$to) continue;
            $reopen = self::get_next_open_date($weekly, $holidays, $to);
            $end = wp_date(get_option('date_format'), strtotime($reopen));
            if ($today >= $from && $today <= $to) {
                return trim($pre_during) . ' ' . $h['label'] . ', ' . trim($post) . ' ' . $end;
            }
            if ($from > $today && $from <= $limit) {
                return trim($pre_before) . ' ' . $h['label'] . ' ' . trim($post) . ' ' . $end;
            }
        }
        return '';
    }

    private static function get_next_open_date($weekly, $holidays, $date){
        $current = $date;
        for ($i = 0; $i < 14; $i++) {
            $ints = self::get_intervals_for_date($weekly, $holidays, $current);
            if (!empty($ints)) return $current;
            $current = wp_date('Y-m-d', strtotime($current . ' +1 day'));
        }
        return $date;
    }

    public static function holiday_message(){
        list($weekly, $holidays) = self::get_data();
        $message = self::get_holiday_message($weekly, $holidays);
        if ($message){
            return '<div class="simple-hours-holiday-text">' . esc_html($message) . '</div>';
        }
        return '';
    }

}
