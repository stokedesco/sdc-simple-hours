<?php
class SH_Shortcodes {
    public static function init(){
        add_shortcode('simplehours_today', array(__CLASS__,'today'));
        add_shortcode('simplehours_until', array(__CLASS__,'until'));
        add_shortcode('simplehours_fullweek', array(__CLASS__,'fullweek'));
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
                if ($today >= $h['from'] && $today <= $h['to']){
                    if (isset($h['closed'])) return "Sorry, we're closed today ({$h['label']}).";
                    $label = empty($h['label']) ? '' : " ({$h['label']})";
                    return "We're open from " . self::format_time($h['open']) . " to " . self::format_time($h['close']) . "{$label}.";
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
        if (is_array($holidays)) {
            foreach ($holidays as $h) {
                if ($date >= $h['from'] && $date <= $h['to']){
                    if (isset($h['closed'])) return array();
                    return array(array($h['open'], $h['close']));
                }
            }
        }
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
        $ts   = $timestamp ? $timestamp : current_time('timestamp');
        $date = wp_date('Y-m-d', $ts);
        $time = wp_date('H:i', $ts);
        $ints = self::get_intervals_for_date($weekly, $holidays, $date);
        foreach ($ints as $i) {
            if ($time >= $i[0] && $time < $i[1]) return true;
        }
        return false;
    }

    public static function fullweek(){
        list($weekly,) = self::get_data();
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

}
