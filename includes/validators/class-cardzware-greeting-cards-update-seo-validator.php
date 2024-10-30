<?php

class Cardzware_Greeting_Cards_Update_Seo_Validator
{
    public const SEO_KEY = 'seo';

    public const PAGE_TITLE_KEY = 'page_title';
    public const META_DESC_KEY = 'meta_description';
    public const PAGE_TEXT_ABOVE_KEY = 'page_text_above';
    public const PAGE_TEXT_BELOW_KEY = 'page_text_below';
    public const CARD_CATEGORY_ID_KEY = 'card_category_id';

    private const SEO_KEYS = [self::PAGE_TITLE_KEY, self::META_DESC_KEY, self::PAGE_TEXT_ABOVE_KEY,  self::PAGE_TEXT_BELOW_KEY, self::CARD_CATEGORY_ID_KEY];

    public static function is_valid($response): bool
    {
        if (!$response) {
            return false;
        }

        if (!isset($response[self::SEO_KEY])) {
            return false;
        }

        if (!is_array($response[self::SEO_KEY])) {
            return false;
        }

        if (count($response[self::SEO_KEY]) != count(self::SEO_KEYS)) {
            return false;
        }

        foreach(array_keys($response['seo']) as $key) {
            if (!in_array($key, self::SEO_KEYS)) {
                return false;
            }
        }

        foreach($response[self::SEO_KEY] as $value) {
            if (str_contains($value, '<script>')) {
                return false;
            }
        }

        return true;
    }
}
