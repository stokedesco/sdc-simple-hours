<?php
class SH_Settings {
    const OPTION_WEEKLY = 'sh_weekly_hours';
    const OPTION_HOLIDAYS = 'sh_holiday_overrides';
    const OPTION_DEBUG = 'sh_debug_mode';

    public function __construct() {
        add_action('admin_menu', array($this,'add_admin_menu'));
        add_action('admin_init', array($this,'settings_init'));
        add_action('admin_enqueue_scripts', array($this,'enqueue_scripts'));
    }

    public function add_admin_menu(){
        add_options_page('Stoke Simple Hours', 'Stoke Simple Hours', 'manage_options', 'simple_hours', array($this,'options_page'));
    }

    public function settings_init(){
        register_setting('sh_settings', self::OPTION_WEEKLY);
        register_setting('sh_settings', self::OPTION_HOLIDAYS);
        register_setting('sh_settings', self::OPTION_DEBUG);

        add_settings_section('sh_section', 'Settings', null, 'sh_settings');

        add_settings_field('sh_weekly', 'Weekly Hours', array($this,'weekly_render'), 'sh_settings','sh_section');
        add_settings_field('sh_holidays','Holiday Overrides', array($this,'holidays_render'),'sh_settings','sh_section');
        add_settings_field('sh_debug','Debug Mode', array($this,'debug_render'),'sh_settings','sh_section');

        add_settings_section(
            'sh_shortcodes',
            'Shortcodes',
            array($this, 'shortcodes_info'),
            'sh_settings'
        );
    }

    public function weekly_render(){
        $values = get_option(self::OPTION_WEEKLY, array());
        $days = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
        echo '<table>';
        foreach($days as $day){
            $open = isset($values[$day]['open']) ? esc_attr($values[$day]['open']) : '';
            $close= isset($values[$day]['close'])? esc_attr($values[$day]['close']):'';
            $closed = isset($values[$day]['closed'])?$values[$day]['closed']:false;
            echo "<tr><th>{$day}</th>";
            echo "<td><input type='time' name='".self::OPTION_WEEKLY."[{$day}][open]' value='{$open}' ".($closed?'disabled':'')." /></td>";
            echo "<td><input type='time' name='".self::OPTION_WEEKLY."[{$day}][close]' value='{$close}' ".($closed?'disabled':'')." /></td>";
            echo "<td><label><input type='checkbox' name='".self::OPTION_WEEKLY."[{$day}][closed]' value='1' ".($closed?'checked':'')." data-day='{$day}' class='sh-day-closed'/> Closed</label></td>";
            echo '</tr>';
        }
        echo '</table>';
    }

    public function holidays_render(){
        $values = get_option(self::OPTION_HOLIDAYS, array());
        echo '<table id="sh-holidays">';
        echo '<tr><th>From</th><th>To</th><th>Label</th><th>Closed?</th><th>Open</th><th>Close</th><th>Action</th></tr>';
        if (is_array($values)){
            foreach($values as $i=>$h){
                $from=esc_attr($h['from']);
                $to=esc_attr($h['to']);
                $label=esc_attr($h['label']);
                $closed=isset($h['closed'])?$h['closed']:false;
                $open=esc_attr($h['open']??'');
                $close=esc_attr($h['close']??'');
                echo "<tr>";
                echo "<td><input type='date' name='".self::OPTION_HOLIDAYS."[{$i}][from]' value='{$from}' /></td>";
                echo "<td><input type='date' name='".self::OPTION_HOLIDAYS."[{$i}][to]' value='{$to}' /></td>";
                echo "<td><input type='text' name='".self::OPTION_HOLIDAYS."[{$i}][label]' value='{$label}' /></td>";
                echo "<td><input type='checkbox' name='".self::OPTION_HOLIDAYS."[{$i}][closed]' value='1' ".($closed?'checked':'')." class='sh-holiday-closed'></td>";
                echo "<td><input type='time' name='".self::OPTION_HOLIDAYS."[{$i}][open]' value='".($closed?'':$open)."' ".($closed?'disabled':'')." /></td>";
                echo "<td><input type='time' name='".self::OPTION_HOLIDAYS."[{$i}][close]' value='".($closed?'':$close)."' ".($closed?'disabled':'')." /></td>";
                echo "<td><button class='button sh-remove-holiday'>Remove</button></td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        echo '<button class="button" id="sh-add-holiday">Add Holiday</button>';
    }

    public function debug_render(){
        $val = get_option(self::OPTION_DEBUG, false);
        echo "<label><input type='checkbox' name='".self::OPTION_DEBUG."' value='1' ".($val?'checked':'')."/> Enable Debug Mode</label>";
    }

    public function shortcodes_info() {
        echo '<p>Use these shortcodes to display opening hours:</p>';
        echo '<ul>';
        echo '<li><code>[simplehours_today]</code> – e.g. “We\'re open from 9:00 to 17:00.”</li>';
        echo '<li><code>[simplehours_until]</code> – e.g. “Open today until 17:00.”</li>';
        echo '<li><code>[simplehours_fullweek]</code> – outputs a full week table of hours.</li>';
        echo '</ul>';
    }

    public function enqueue_scripts($hook){
        if ($hook!='settings_page_simple_hours') return;
        wp_enqueue_script('simple-hours-admin', SH_URL.'assets/admin.js', array('jquery'), null, true);
    }

    public function options_page(){
        ?>
        <div class="wrap">
            <h1>Stoke Simple Hours Settings</h1>
            <form method="post" action="options.php">
            <?php
            settings_fields('sh_settings');
            do_settings_sections('sh_settings');
            submit_button();
            ?>
            </form>
        </div>
        <?php
    }
}

new SH_Settings();
