<?php

/**
 * Field Validation Helper
 *
 * PHP Version 5.3
 * 
 * @category Helpers
 * @package  Layout
 * @author   Alex Wyett <alex@wyett.co.uk>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     http://www.wyett.co.uk
 */


/**
 * Field Validation Helper
 *
 * PHP Version 5.3
 * 
 * @category Helpers
 * @package  Layout
 * @author   Alex Wyett <alex@wyett.co.uk>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     http://www.wyett.co.uk
 */
class FieldValidation
{
    /**
     * Validate all fields within a given array
     * 
     * @param array &$postData        The data to validate
     * @param array $validationFields Field settings to validate against
     * 
     * @return array
     */
    public static function validateAllFields(&$postData, $validationFields)
    {
        // Errors array which will be returned
        $errors = array();
        
        // Loop through brochure fields and look for value
        foreach ($validationFields as $fieldName => $field) {
            if (isset($postData[$fieldName])) {
                $fieldTest = self::validateField(
                    $postData[$fieldName], 
                    $field
                );
                if ($fieldTest !== true) {
                    $errors[$fieldName] = $fieldTest;
                }
            } else {
                if (self::_isFieldRequired($field)) {
                    $errors[$fieldName] = ' is required';
                } else {
                    $postData[$fieldName] = self::_getDefaultFieldValue($field);
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate a customer data field based on the field settings
     * 
     * @param string &$value Posted value
     * @param array  $field  Field settings
     * 
     * @return mixed String if fails validation|boolean
     */
    public static function validateField(&$value, $field)
    {
        // Check for max length exceeds
        if (isset($field['maxLength'])) {
            if (strlen($value) > $field['maxLength']) {
                return ' maximum length is ' . $field['maxLength'];
            }
        }
        
        // Check for min length
        if (isset($field['minLength'])) {
            if (strlen($value) < $field['minLength'] && strlen($value) > 0) {
                return ' minimum length is ' . $field['minLength'];
            }
        }
        
        // Check field is required
        if (self::_isFieldRequired($field)) {
            if (strlen($value) == 0) {
                return ' is required';
            }
        } else {
            // Add in default value
            if (strlen($value) == 0) {
                $value = self::_getDefaultFieldValue($field);
            }
        }
        
        // Check for min length
        if (isset($field['type'])) {
            switch($field['type']) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return ' is an invalid email address';
                }
                break;
            case 'boolean':
                if (strtolower($value) == 'y' 
                    || strtolower($value) == 'true' 
                    || strtolower($value) == 'yes'
                    || strtolower($value) == 'on'
                ) {
                    $value = true;
                } else {
                    $value = false;
                }
                break;
            case 'integer':
                if (!is_numeric($value)) {
                    return ' is not a number';
                }
                break;
            }
        }
        
        // All seems OK
        return true;
    }


    /**
     * Check to see if a field is required or not
     * 
     * @param array $field booking field
     * 
     * @return boolean
     */
    private static function _isFieldRequired($field)
    {
        if (isset($field['required'])) {
            return $field['required'];
        }
        
        return false;
    }
    
    /**
     * Gets the default value of a field
     * 
     * @param array $field Customer booking field
     * 
     * @return boolean
     */
    private static function _getDefaultFieldValue($field)
    {
        if (isset($field['default'])) {
            return $field['default'];
        }
        
        return '';
    }
}

