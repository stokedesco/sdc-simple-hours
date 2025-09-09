<?php
class SH_Logger {
    const LOG_FILE = 'simple-hours.log';
    public function __construct(){
        add_action('init', array($this,'maybe_enable'));
    }
    public function maybe_enable(){
        if (get_option(SH_Settings::OPTION_DEBUG)){
            add_action('sh_log', array($this,'log'), 10, 2);
        }
    }
    public function log($message, $context=array()){
        $file  = WP_CONTENT_DIR . '/uploads/' . self::LOG_FILE;
        $entry = wp_date('c') . ' ' . $message . ' ' . json_encode($context) . "\n";
        file_put_contents($file, $entry, FILE_APPEND);
    }
}
new SH_Logger();
