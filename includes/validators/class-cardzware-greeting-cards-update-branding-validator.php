<?php

class Cardzware_Greeting_Cards_Update_Branding_Validator
{
    public const BRANDING_KEY = 'branding';
    public const TILES_SIZE_KEY = 'tiles_size';
    public const TILES_BG_COLOR_KEY = 'tiles_bg_color';
    public const TILES_COLOR_KEY = 'tiles_color';
    public const TILES_HOVER_COLOR_KEY = 'tiles_hover_color';
    public const TILES_BC_COLOR_KEY = 'bc_color';
    public const TILES_BC_HOVER_COLOR_KEY = 'bc_hover_color';

    public static function is_valid($response): bool
    {
        if (!$response) {
            return false;
        }

        if (!isset($response[self::BRANDING_KEY])) {
            return false;
        }

        if (!is_array($response[self::BRANDING_KEY])) {
            return false;
        }

        $brandingKeys = [self::TILES_SIZE_KEY, self::TILES_BG_COLOR_KEY, self::TILES_COLOR_KEY, self::TILES_HOVER_COLOR_KEY, self::TILES_BC_COLOR_KEY, self::TILES_BC_HOVER_COLOR_KEY];
        if (count($response[self::BRANDING_KEY]) != count(($brandingKeys))) {
            return false;
        }

        foreach(array_keys($response[self::BRANDING_KEY]) as $key) {
            if (!in_array($key, $brandingKeys)) {
                return false;
            }
        }

        if (!is_numeric($response[self::BRANDING_KEY][self::TILES_SIZE_KEY])) {
            return false;
        }

        array_shift($response[self::BRANDING_KEY]);
        foreach($response[self::BRANDING_KEY] as $value) {
            if (strpos($value, '#') < 0 || strpos($value, 'rgba') < 0) {
                return false;
            }
        }

        return true;
    }
}
