<?php

/**
 * Field Helper basic example.
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

// Field settings
$fields = array(
    'name' => array(
        'type'       => 'text',
        'attributes' => array(
            'id' => 'name'
        )
    )
);

// Create new FieldHelper Factory and use $_GET array as values array to 
// persist user entry
$fhp = new FieldHelper($fields, $_GET);
$fhp->create();

// Output field
$name = $fhp->getElement('name');

?>

<form method="get">
    <fieldset>
        <legend>Basic Form</legend>
        <div>
            <label for="name">Name</label>
            <?php echo $name; ?>
        </div>
    </fieldset>
    <input type="submit" value="Submit">
</form>