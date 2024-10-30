<?php

class Cardzware_Greeting_Cards_Update_Api_Url_Validator
{
    public static function is_valid($response): bool
    {
        if (!$response) {
            return false;
        }

        if (!isset($response['api_url'])) {
            return false;
        }

        if (!is_string($response['api_url']) || $response['api_url'] === '') {
            return false;
        }

        return true;
    }
}
