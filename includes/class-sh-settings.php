<?php
class SH_Settings {
    const OPTION_WEEKLY = 'sh_weekly_hours';
    const OPTION_HOLIDAYS = 'sh_holiday_overrides';
    const OPTION_DEBUG = 'sh_debug_mode';
    const OPTION_SCHEMA = 'sh_schema_enabled';
    const OPTION_SCHEMA_NAME = 'sh_schema_name';
    const OPTION_SCHEMA_TYPE = 'sh_schema_type';
    const OPTION_SECOND = 'sh_second_hours';
    const OPTION_TIME_FORMAT = 'sh_time_format';
    const OPTION_HOLIDAY_PRE_BEFORE = 'sh_holiday_pre_before';
    const OPTION_HOLIDAY_PRE_DURING = 'sh_holiday_pre_during';
    const OPTION_HOLIDAY_POST = 'sh_holiday_post';


    public function __construct() {
        add_action('admin_menu', array($this,'add_admin_menu'));
        add_action('admin_init', array($this,'settings_init'));
        add_action('admin_enqueue_scripts', array($this,'enqueue_scripts'));
    }

    public function add_admin_menu(){
        add_menu_page('Stoke Simple Hours', 'Simple Hours', 'manage_options', 'simple_hours', array($this,'options_page'), 'dashicons-clock');
    }

    public function settings_init(){
        register_setting('sh_settings', self::OPTION_WEEKLY);
        register_setting('sh_settings', self::OPTION_HOLIDAYS);
        register_setting('sh_settings', self::OPTION_DEBUG);
        register_setting('sh_settings', self::OPTION_SCHEMA);
        register_setting('sh_settings', self::OPTION_SCHEMA_NAME);
        register_setting('sh_settings', self::OPTION_SCHEMA_TYPE);
        register_setting('sh_settings', self::OPTION_SECOND);
        register_setting('sh_settings', self::OPTION_TIME_FORMAT);
        register_setting('sh_settings', self::OPTION_HOLIDAY_PRE_BEFORE);
        register_setting('sh_settings', self::OPTION_HOLIDAY_PRE_DURING);
        register_setting('sh_settings', self::OPTION_HOLIDAY_POST);

        add_settings_section('sh_section', 'Settings', null, 'sh_settings');

        add_settings_field('sh_time_format', 'Time Format', array($this,'time_format_render'), 'sh_settings','sh_section');
        add_settings_field('sh_second', 'Enable Second Hours', array($this,'second_render'), 'sh_settings','sh_section');
        add_settings_field('sh_weekly', 'Weekly Hours', array($this,'weekly_render'), 'sh_settings','sh_section');
        add_settings_field('sh_holidays','Holiday Overrides', array($this,'holidays_render'),'sh_settings','sh_section');
        add_settings_field('sh_holiday_pre_before','Upcoming Holiday Text', array($this,'holiday_pre_before_render'),'sh_settings','sh_section');
        add_settings_field('sh_holiday_pre_during','Active Holiday Text', array($this,'holiday_pre_during_render'),'sh_settings','sh_section');
        add_settings_field('sh_holiday_post','Holiday Post Text', array($this,'holiday_post_render'),'sh_settings','sh_section');
        add_settings_field('sh_debug','Debug Mode', array($this,'debug_render'),'sh_settings','sh_section');
        add_settings_field('sh_schema','Schema Markup', array($this,'schema_render'),'sh_settings','sh_section');

        add_settings_section(
            'sh_shortcodes',
            'Shortcodes',
            array($this, 'shortcodes_info'),
            'sh_settings'
        );
    }

    public function time_format_render(){
        $val = get_option(self::OPTION_TIME_FORMAT, '24');
        echo "<select name='".self::OPTION_TIME_FORMAT."'>";
        echo "<option value='24' ".selected($val,'24',false).">24-hour</option>";
        echo "<option value='12' ".selected($val,'12',false).">12-hour (AM/PM)</option>";
        echo "</select>";
    }

    public function second_render(){
        $enabled = get_option(self::OPTION_SECOND, false);
        echo "<label><input type='checkbox' id='sh_enable_second_hours' name='".self::OPTION_SECOND."' value='1' ".($enabled?'checked':'')."/> Allow second open/close time</label>";
    }

    public function weekly_render(){
        $values = get_option(self::OPTION_WEEKLY, array());
        $days = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
        $second = get_option(self::OPTION_SECOND, false);
        echo '<table class="sh-table">';
        foreach($days as $day){
            $open   = isset($values[$day]['open']) ? esc_attr($values[$day]['open']) : '';
            $close  = isset($values[$day]['close'])? esc_attr($values[$day]['close']):'';
            $open2  = isset($values[$day]['open2']) ? esc_attr($values[$day]['open2']) : '';
            $close2 = isset($values[$day]['close2'])? esc_attr($values[$day]['close2']):'';
            $closed = isset($values[$day]['closed'])?$values[$day]['closed']:false;
            echo "<tr><th>{$day}</th>";
            echo "<td><input type='time' name='".self::OPTION_WEEKLY."[{$day}][open]' value='{$open}' ".($closed?'disabled':'')." /></td>";
            echo "<td><input type='time' name='".self::OPTION_WEEKLY."[{$day}][close]' value='{$close}' ".($closed?'disabled':'')." /></td>";
            if ($second){
                echo "<td class='sh-second-hours'><input type='time' name='".self::OPTION_WEEKLY."[{$day}][open2]' value='{$open2}' ".($closed?'disabled':'')." /></td>";
                echo "<td class='sh-second-hours'><input type='time' name='".self::OPTION_WEEKLY."[{$day}][close2]' value='{$close2}' ".($closed?'disabled':'')." /></td>";
            }
            echo "<td><label><input type='checkbox' name='".self::OPTION_WEEKLY."[{$day}][closed]' value='1' ".($closed?'checked':'')." data-day='{$day}' class='sh-day-closed'/> Closed</label></td>";
            echo '</tr>';
        }
        echo '</table>';
    }

    public function holidays_render(){
        $values = get_option(self::OPTION_HOLIDAYS, array());
        echo '<table id="sh-holidays" class="sh-table">';
        echo '<tr><th>From</th><th>To</th><th>Label</th><th>Closed?</th><th>Start</th><th>Finish</th><th>Action</th></tr>';
        if (is_array($values)){
            foreach($values as $i=>$h){
                $from=esc_attr($h['from']);
                $to=esc_attr($h['to']);
                $label=esc_attr($h['label']);
                $closed=isset($h['closed'])?$h['closed']:false;
                $start=esc_attr($h['start']??'');
                $finish=esc_attr($h['finish']??'');
                echo "<tr>";
                echo "<td><input type='date' name='".self::OPTION_HOLIDAYS."[{$i}][from]' value='{$from}' /></td>";
                echo "<td><input type='date' name='".self::OPTION_HOLIDAYS."[{$i}][to]' value='{$to}' /></td>";
                echo "<td><input type='text' name='".self::OPTION_HOLIDAYS."[{$i}][label]' value='{$label}' /></td>";
                echo "<td><input type='checkbox' name='".self::OPTION_HOLIDAYS."[{$i}][closed]' value='1' ".($closed?'checked':'')." class='sh-holiday-closed'></td>";
                echo "<td><input type='time' name='".self::OPTION_HOLIDAYS."[{$i}][start]' value='".($closed?$start:'')."' ".($closed?'':'disabled')." /></td>";
                echo "<td><input type='time' name='".self::OPTION_HOLIDAYS."[{$i}][finish]' value='".($closed?$finish:'')."' ".($closed?'':'disabled')." /></td>";
                echo "<td><button class='button sh-remove-holiday'>Remove</button></td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        echo '<button class="button" id="sh-add-holiday">Add Holiday</button>';
    }

    public function holiday_pre_before_render(){
        $val = get_option(self::OPTION_HOLIDAY_PRE_BEFORE, 'We will be closed for the');
        echo "<input type='text' name='".self::OPTION_HOLIDAY_PRE_BEFORE."' value='".esc_attr($val)."' class='regular-text' />";
    }

    public function holiday_pre_during_render(){
        $val = get_option(self::OPTION_HOLIDAY_PRE_DURING, 'We are closed for the');
        echo "<input type='text' name='".self::OPTION_HOLIDAY_PRE_DURING."' value='".esc_attr($val)."' class='regular-text' />";
    }

    public function holiday_post_render(){
        $val = get_option(self::OPTION_HOLIDAY_POST, 'reopening on');
        echo "<input type='text' name='".self::OPTION_HOLIDAY_POST."' value='".esc_attr($val)."' class='regular-text' />";
    }

    public function debug_render(){
        $val = get_option(self::OPTION_DEBUG, false);
        echo "<label><input type='checkbox' name='".self::OPTION_DEBUG."' value='1' ".($val?'checked':'')."/> Enable Debug Mode</label>";
    }

    public function schema_render(){
        $enabled = get_option(self::OPTION_SCHEMA, false);
        $name    = get_option(self::OPTION_SCHEMA_NAME, get_bloginfo('name'));
        $type    = get_option(self::OPTION_SCHEMA_TYPE, 'LocalBusiness');
        echo "<label class='sh-field'><input type='checkbox' name='".self::OPTION_SCHEMA."' value='1' ".($enabled?'checked':'')."/> Enable schema.org markup</label>";
        echo "<label class='sh-field'>Business Name: <input type='text' name='".self::OPTION_SCHEMA_NAME."' value='".esc_attr($name)."' /></label>";
        echo "<label class='sh-field'>Business Type: <input type='text' name='".self::OPTION_SCHEMA_TYPE."' value='".esc_attr($type)."' /></label>";
    }

    public function shortcodes_info() {
        echo '<p>Use these shortcodes to display opening hours:</p>';
        echo '<ul>';
        echo '<li><code>[simplehours_today]</code> – e.g. “We\'re open from 9:00 to 17:00.”</li>';
        echo '<li><code>[simplehours_until]</code> – e.g. “Open today until 17:00.”</li>';
        echo '<li><code>[simplehours_fullweek]</code> – outputs a full week table of hours.</li>';
        echo '<li><code>[holiday-message]</code> – displays the current or upcoming holiday message.</li>';
        echo '</ul>';
    }

    public function enqueue_scripts($hook){
        if ($hook!='toplevel_page_simple_hours') return;
        wp_enqueue_style('simple-hours-admin', SH_URL.'assets/admin.css');
        wp_enqueue_script('simple-hours-admin', SH_URL.'assets/admin.js', array('jquery'), null, true);
    }

    public function options_page(){
        ?>
        <div class="wrap sh-settings">
            <h1>Stoke Simple Hours Settings</h1>
            <form method="post" action="options.php">
            <?php
            settings_fields('sh_settings');
            do_settings_sections('sh_settings');
            submit_button();
            ?>
            </form>
            <p class="sh-footer-credit">Plugin built by <a href="https://stokedesign.co" target="_blank">Stoke Design Co</a></p>
        </div>
        <?php
    }
}

new SH_Settings();
