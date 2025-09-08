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
        $today   = date('Y-m-d');
        $dayname = date('l');

        if (is_array($holidays)) {
            foreach ($holidays as $h) {
                if ($today >= $h['from'] && $today <= $h['to']){
                    if (isset($h['closed'])) return "Sorry, we're closed today ({$h['label']}).";
                    return "We're open from {$h['open']} to {$h['close']} ({$h['label']}).";
                }
            }
        }
        if (!empty($weekly[$dayname]['closed'])){
            return "Sorry, we're closed today.";
        }
        $o = $weekly[$dayname]['open'];
        $c = $weekly[$dayname]['close'];
        return "We're open from {$o} to {$c}.";
    }

    public static function until(){
        list($weekly, $holidays) = self::get_data();
        $now   = new DateTime();
        $today = $now->format('Y-m-d');
        $time  = $now->format('H:i');

        if (is_array($holidays)) {
            foreach ($holidays as $h) {
                if ($today >= $h['from'] && $today <= $h['to']){
                    if (isset($h['closed'])) {
                        $tom = $now->add(new DateInterval('P1D'))->format('Y-m-d');
                        return "Next open at " . self::get_open_time($weekly, $holidays, $tom);
                    }
                    if ($time < $h['close']){
                        return "Open today until {$h['close']}.";
                    }
                    return "Next open at " . self::get_open_time($weekly, $holidays, $today);
                }
            }
        }
        $day = date('l');
        if (!empty($weekly[$day]['closed']) || $time > $weekly[$day]['close']){
            $next_date = new DateTime();
            for ($i=1;$i<=7;$i++){
                $d = $next_date->add(new DateInterval('P1D'))->format('Y-m-d');
                $dn = date('l', strtotime($d));
                if (is_array($holidays)) {
                    foreach ($holidays as $h) {
                        if ($d >= $h['from'] && $d <= $h['to'] && isset($h['closed'])) {
                            continue 2;
                        }
                        if ($d >= $h['from'] && $d <= $h['to']) {
                            return "Next open at {$h['open']} tomorrow.";
                        }
                    }
                }
                if (empty($weekly[$dn]['closed'])) {
                    return "Next open at " . $weekly[$dn]['open'] . " on " . $dn . ".";
                }
            }
        }
        if ($time < $weekly[$day]['close']){
            return "Open today until " . $weekly[$day]['close'] . ".";
        }
    }

    private static function get_open_time($weekly, $holidays, $date){
        $dn = date('l', strtotime($date));
        if (is_array($holidays)) {
            foreach ($holidays as $h) {
                if ($date >= $h['from'] && $date <= $h['to'] && !isset($h['closed'])) {
                    return $h['open'];
                }
            }
        }
        return $weekly[$dn]['open'];
    }

    public static function fullweek(){
        list($weekly,) = self::get_data();
        $out         = '<table class="simple-hours-table">';
        $current_day = date('l');

        if (is_array($weekly)) {
            foreach ($weekly as $day => $v) {
                $row_class = $day === $current_day ? ' class="simple-hours-current-day"' : '';
                if (!empty($v['closed'])) {
                    $hours = 'Closed';
                } else {
                    $hours = "{$v['open']} - {$v['close']}";
                }
                $out .= "<tr{$row_class}><th class=\"simple-hours-day\">$day</th><td class=\"simple-hours-time\">$hours</td></tr>";
            }
        }

        $out .= '</table>';
        return $out;
    }

}
