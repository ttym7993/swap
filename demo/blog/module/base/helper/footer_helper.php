<?php
class footer_helper extends kern\helper {
    public static function run(array $context) {
        self::set('settings', setting_service::get_settings());
        self::render();
    }
}
