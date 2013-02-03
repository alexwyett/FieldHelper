<?php

/**
 * Field Helper user form example wuth validation.
 *
 * PHP Version 5.3
 *
 * @category  FieldHelper
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Alex Wyett
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.wyett.co.uk
 */

require_once '..' . DIRECTORY_SEPARATOR . 
    'core' . DIRECTORY_SEPARATOR . 'FieldHelper.class.php';
require_once '..' . DIRECTORY_SEPARATOR . 
    'core' . DIRECTORY_SEPARATOR . 'FieldValidation.class.php';

// Field settings
$fields = array(
    'title' => array(
        'type' => 'select',
        'values' => array(
            'Mr' => 'Mr',
            'Mrs' => 'Mrs',
            'Ms' => 'Ms',
            'Miss' => 'Miss',
            'Prof' => 'Prof',
            'Rev' => 'Rev'
        ),
        'attributes' => array(
            'id' => 'title'
        )
    ),
    'firstName' => array(
        'type' => 'text',
        'attributes' => array(
            'id' => 'firstName'
        )
    ),
    'lastName' => array(
        'type' => 'text',
        'attributes' => array(
            'id' => 'lastName'
        )
    ),
    'dob' => array(
        'type' => 'dob'
    )
);

// Validation settings
$validation = array(
    'title' => array(
        'required' => true,
        'maxLength' => 5
    ),
    'firstName' => array(
        'maxLength' => 50
    ),
    'lastName' => array(
        'required' => true,
        'maxLength' => 50
    )
);

// Validate fields if form has been submitted
if (count($_GET) > 0) {
    $errors = FieldValidation::validateAllFields($_GET, $validation);
    var_dump($errors);
}

// Create new FieldHelper Factory and use $_GET array as values array to 
// persist user entry
$fhp = new FieldHelper($fields, $_GET);
$fhp->create();

?>

<form method="get">
    <fieldset>
        <legend>User Form</legend>
        <div>
            <label for="title">Title</label>
            <?php echo $fhp->getElement('title'); ?>
        </div>
        <div>
            <label for="firstName">First name</label>
            <?php echo $fhp->getElement('firstName'); ?>
        </div>
        <div>
            <label for="lastName">Last name</label>
            <?php echo $fhp->getElement('lastName'); ?>
        </div>
        <div>
            <label for="dob">Date Of Birth</label>
            <?php echo implode('', $fhp->getElement('dob')); ?>
        </div>
    </fieldset>
    <input type="submit" value="Submit">
</form>