<?php

class Cardzware_Greeting_Cards_Save_Settings_Validator
{
    public static function is_valid($response): bool
    {
        if (!$response) {
            return false;
        }

        if (!isset($response['api_key']) || !isset($response['client_id']) || !isset($response['api_url'])) {
            return false;
        }

        if (!is_string($response['api_key']) || $response['api_key'] === '') {
            return false;
        }

        if (!is_string($response['api_url']) || $response['api_url'] === '') {
            return false;
        }

        if (!is_numeric($response['client_id']) || $response['client_id'] === '' || $response['client_id'] <= 0) {
            return false;
        }

        return true;
    }
}
