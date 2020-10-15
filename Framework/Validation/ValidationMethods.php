<?php

namespace Framework\Validation;

use DateTime;

trait ValidationMethods
{

    /**
     * Check if input does not already exist in specified table
     * 
     * @param array $input
     * @param string $table
     * @return boolean
     */
    private function uniqueFilter(array $input, string $table)
    {
        $stmt = $this->database->prepare('SELECT 1 FROM ' . $table . ' WHERE ' . $input[0] . ' = ?');
        $stmt->bind_param("s", $input[1]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is filled
     * 
     * @param array $input
     * @return boolean
     */
    private function requiredFilter(array $input)
    {
        if ($input[1] !== '') {
            return true;
        }
        $this->runFilters = false;
        $this->newValidationError($input);
    }

    /**
     * Check if input is valid json
     * 
     * @param array $input
     * @return boolean
     */
    private function jsonCheck(array $input)
    {
        json_decode($input[1]);
        if (json_last_error() == JSON_ERROR_NONE) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is of type file
     * 
     * @parameter array $input
     * @return boolean
     */
    private function fileFilter($input)
    {
        if (file_exists($input[1]['tmp_name'])) {
            return true;
        }
        $this->runFilters = false;
        $this->newValidationError($input);
    }

    /**
     * Check if input is of type image
     * 
     * @parameter array $input
     * @return boolean
     */
    private function imageFilter(array $input)
    {
        if (exif_imagetype($input[1]['tmp_name'])) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if file is not larger then specified value
     * 
     * @parameter array $input
     * @parameter string $size
     * @return boolean
     */
    private function fileSizeFilter(array $input, string $size)
    {
        if ($input[1]['size'] <= $size) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input exists in table
     * 
     * @param array $input
     * @param string $table
     * @return boolean
     */
    private function existsFilter(array $input, string $table)
    {
        $stmt = $this->database->prepare('SELECT 1 FROM ' . $table . ' WHERE ' . $input[0] . ' = ?');
        $stmt->bind_param("s", $input[1]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows !== 0) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is a phone number
     * 
     * @param array $input
     * @return boolean
     */
    private function phoneFilter(array $input)
    {
        if (preg_match('/^[0-9]{10}+$/', $input[1])) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is equal to specific length
     * 
     * @param array $input
     * @param string $length
     * @return boolean
     */
    private function lengthCheck(array $input, string $length)
    {
        if (strlen($input[1]) == $length) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is different then given string
     * 
     * @param array $input
     * @param @differInput
     * @return boolean
     */
    private function differentFilter(array $input, string $differInput)
    {
        if ($differInput !== $input[1]) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is a valid ip address
     * 
     * @param array $input
     * @return boolean
     */
    private function ipaddressFilter(array $input)
    {
        if (filter_var($input[1], FILTER_VALIDATE_IP)) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is valid email address
     * 
     * @param array $input
     * @return boolean
     */
    private function emailFilter(array $input)
    {
        if (filter_var($input[1], FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is of type string
     * 
     * @param array $input
     * @return boolean
     */
    private function stringFilter(array $input)
    {
        if (is_string($input[1])) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is a date
     * 
     * @param array $input
     * @return boolean
     */
    private function dateFilter(array $input)
    {
        if (DateTime::createFromFormat('Y-m-d', $input[1]) !== FALSE) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is a date after specified date
     * 
     * @param array $input
     * @param string $date
     * @return boolean
     */
    private function afterDateFilter(array $input, string $date)
    {
        if (DateTime::createFromFormat('Y-m-d', $date) <  DateTime::createFromFormat('Y-m-d', $input[1])) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is an active url
     * 
     * @param array $input
     * @return boolean
     */
    private function activeUrlFilter(array $input)
    {
        if (dns_get_record($input[1], DNS_MX)) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is of type integer
     * 
     * @param array $input
     * @return boolean
     */
    private function intFilter(array $input)
    {
        if (is_int($input[1])) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Get input item with same name but _confirmation behind it and check if the value is the same
     * 
     * @param array $input
     * @return boolean
     */
    private function confirmationFilter(array $input)
    {
        if ($this->postData[$input[0] . '_confirmation'] === $this->postData[$input[0]]) {
            return true;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is of type boolean
     * 
     * @param array $input
     * @param string $strict
     * @return boolean
     */
    private function booleanFilter(array $input, string $strict)
    {
        switch ($strict) {
            case 'true':
                if ($input[1] === true || $input[1] === false || $input[1] === 1 || $input[1] === 0) {
                    return true;
                }
                break;
            case 'false':
                if ($input[1] === true || $input[1] === false || $input[1] == 1 || $input[1] == 0) {
                    return true;
                }
                break;
        }
        $this->newValidationError($input);
    }

    /**
     * Check if input is of type array
     * 
     * @param array $input
     * @return boolean
     */
    private function arrayFilter($input)
    {
        if (is_array($input[1])) {
            return true;
        }
        $this->newValidationError($input);
    }

}