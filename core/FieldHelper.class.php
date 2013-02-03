<?php

/**
 * Field Helper class. Provides static methods to build html input elements that
 * have data memory
 *
 * PHP Version 5.3
 *
 * @category FieldHelper
 * @package  Layout
 * @author   Alex Wyett <alex@wyett.co.uk>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     http://www.wyett.co.uk
 */

/**
 * Field Helper class. Provides static methods to build html input elements that
 * have data memory
 *
 * @category FieldHelper
 * @package  Layout
 * @author   Alex Wyett <alex@wyett.co.uk>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     http://www.wyett.co.uk
 */
class FieldHelper
{
    /**
     * Element parameter values
     *
     * @var array
     */
    protected $values = array();
    
    /**
     * Element settings
     *
     * @var array
     */
    protected $settings = array();
    
    /**
     * Created elements array
     * 
     * @var array
     */
    protected $elements = array();

    /**
     * Constructor
     *
     * @param array $settings Search form parameters to persist.  Format
     *                        should the following:
     *
     *                        eleName => array(
     *                            'type' => '',      (type, camelcase fmt)
     *                            'values' => '',    (string or array)
     *                            'attributes' => '' (key/val array)
     *                        )
     * @param array $values   Array of element parameter values
     *
     * @return void
     */
    public function __construct($settings = array(), $values = array())
    {
        $this->settings = $settings;
        $this->values = $values;
        return $this;
    }

    /**
     * Create an array of elements
     *
     * @return array
     */
    public function create()
    {
        foreach ($this->settings as $name => $attrs) {
            if (is_array($attrs)) {
                if (in_array('type', array_keys($attrs))) {
                    $this->elements[$name] = $this->createElement(
                        $name, 
                        $attrs
                    );
                }
            }
        }
        return $this;
    }
    
    /**
     * Get an element from the created elements array
     * 
     * @param string $eleName Element index name
     * 
     * @return string
     */
    public function getElement($eleName)
    {
        if (isset($this->elements[$eleName])) {
            return $this->elements[$eleName];
        }
    }
    
    /**
     * Return all created form elements
     * 
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Create a singular element
     * 
     * @param string $name  Name of element
     * @param array  $attrs Array of element attributes nincluding the type
     * 
     * @return string
     */
    public function createElement($name, $attrs = array())
    {
        // Create function string and check for function
        $func = sprintf(
            '_getElement%sBox',
            ucfirst($attrs['type'])
        );
        if (method_exists($this, $func)) {
            return $this->$func(
                $name,
                ((isset($attrs['values'])) ? $attrs['values'] : ''),
                ((isset($attrs['attributes'])) ? $attrs['attributes'] : array()),
                (in_array('xhtml', array_keys($attrs)))
            );
        }
        return '';
    }


    // --------------------- Private Functions -------------------------- //
    
    
    /**
     * Create a checkbox element based on a current field
     *
     * @param string  $cbName     The Name of the checkbox element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $cbCmpValue Checkbox element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getElementCheckBox(
        $cbName,
        $cbCmpValue = 'Y',
        $attributes = array(),
        $xhtml = false
    ) {
        $ele = '';
        $ele .= sprintf(
            '<input type="checkbox" name="%s" value="%s" ',
            $cbName,
            $cbCmpValue
        );

        // Add in attributes
        $this->_addElementAttributes($ele, $attributes);

        // Add Checked attribute if search var equals attribute value
        if ($this->_hasValue($cbName, $cbCmpValue)) {
            $ele .= ' checked="checked" ';
        }

        if ($xhtml) {
            // Self close tag
            $ele .= '/';
        }

        // Close tag
        $ele .= '>';

        return $ele;
    }
    
    
    /**
     * Create a textarea element based on a current field
     *
     * @param string  $taName     The Name of the textarea element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $taCmpValue Textarea element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getElementTextareaBox(
        $taName,
        $taCmpValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        $ele = '';
        $ele .= sprintf(
            '<textarea name="%s" ',
            $taName
        );

        // Add in attributes
        $this->_addElementAttributes($ele, $attributes);
        
        // Close first tag
        $ele .= '>';
        
        // Add value
        $ele .= $this->_getValue($taName);

        // Close tag
        $ele .= '</textarea>';

        return $ele;
    }

    /**
     * Create a selectbox element based on a current field
     *
     * @param string  $sbName     The Name of the selectbox element.  Also is
     *                            the filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $sbValues   Select box values (key/val array)
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getElementSelectBox(
        $sbName,
        $sbValues = array(),
        $attributes = array(),
        $xhtml = false
    ) {
        // Return nothing if values are no array
        if (!is_array($sbValues)) {
            return '';
        }
        
        // Build element
        $ele = $this->_buildSelectBox($sbName, $attributes);

        // Loop through values an add options
        $options = '';
        foreach ($sbValues as $opVal => $displayKey) {
            $options .= sprintf(
                '<option value="%s"',
                $opVal
            );
            if ($this->_hasValue($sbName, $opVal)) {
                $options .= ' selected="selected" ';
            }
            $options .= sprintf(
                '>%s</option>',
                $displayKey
            );
        }
        
        // Add in options and return
        return sprintf($ele, $options);
    }
    
    /**
     * Special element, create three select boxes to help
     * 
     * @param string $sbName     Name of element
     * @param string $sbValues   Value setting
     * @param array  $attributes Array of attribute values
     * 
     * @return array
     */
    private function _getElementDobBox(
        $sbName,
        $sbValues = array(),
        $attributes = array()
    ) {
        $dobArray = array();
        
        // Create day select box
        $day = $this->_buildSelectBox('day', $attributes);
        $options = $this->_buildSelectBoxOptions(
            array_combine(
                array_merge(
                    array(''),
                    range(1, 31)
                ), 
                array_merge(
                    array('Day'),
                    range(1, 31)
                )
            ), 
            $this->_getValue('day')
        );
        $dobArray['day'] = sprintf($day, $options);
        
        // Create month select box
        $month = $this->_buildSelectBox('month', $attributes);
        $options = $this->_buildSelectBoxOptions(
            array(
                '' => 'Month',
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ), 
            $this->_getValue('month')
        );
        $dobArray['month'] = sprintf($month, $options);
        
        // Create Year select box
        $year = $this->_buildSelectBox('year', $attributes);
        $options = $this->_buildSelectBoxOptions(
            array_combine(
                array_merge(
                    array(''),
                    range(date('Y'), (date('Y')-100))
                ), 
                array_merge(
                    array('Year'),
                    range(date('Y'), (date('Y')-100))
                )
            ),
            $this->_getValue('year')
        );
        $dobArray['year'] = sprintf($year, $options);
        
        return $dobArray;
    }

    /**
     * Create a date input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbValue    element value.  in this case, the format is
     *                            used as a phpdate format
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getElementDateInputBox(
        $tbName,
        $tbValue = 'd-m-Y',
        $attributes = array(),
        $xhtml = false
    ) {
        return $this->_getBaseElementInputBox(
            $tbName,
            'date',
            $tbValue,
            $attributes,
            $xhtml
        );
    }

    /**
     * Create a date input element based on a current search field
     *
     * @param string  $sbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $sbValue    element value.  in this case, the format is
     *                            used as a phpdate format
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getElementDateSelectBox(
        $sbName,
        $sbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        $dsb = $this->_buildSelectBox($sbName, $attributes);
        $dates = array('' => 'Any');
        for ($i = strtotime('today'); 
            $i <= mktime(0, 0, 0, 12, 31, date('Y') + 1); 
            $i = $i + $this->secondsInADay
        ) {
            $dates[date('d-m-Y', $i)] = date('d F Y', $i);
        }
        
        $options = $this->_buildSelectBoxOptions(
            $dates, 
            $this->_getValue($sbName)
        );
        
        return sprintf($dsb, $options);
    }
    
    /**
     * Build a select box element
     *
     * @param string $name       The Name of the selectbox element. 
     * @param array  $attributes Key/Val array of element attributes which can
     *                           be applied.
     * 
     * @return string
     */
    private function _buildSelectBox($name, $attributes = array())
    {
        $ele = '';
        $ele .= sprintf(
            '<select name="%s" ',
            $name
        );

        // Add in attributes
        $this->_addElementAttributes($ele, $attributes);

        // Close tag
        $ele .= '>%s';
        
        // Close element
        $ele .= '</select>';

        return $ele;
    }
    
    /**
     * Build select box options
     * 
     * @param array $options Options
     * @param mixed $value   Selected value
     * 
     * @return string
     */
    private function _buildSelectBoxOptions($options = array(), $value = '')
    {
        $ele = '';
        // Loop through values an add options
        foreach ($options as $opVal => $displayKey) {
            $ele .= sprintf(
                '<option value="%s"',
                $opVal
            );
            if ($value == $opVal) {
                $ele .= ' selected="selected" ';
            }
            $ele .= sprintf(
                '>%s</option>',
                $displayKey
            );
        }
        return $ele;
    }

    /**
     * Create a general input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbValue    element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getElementTextBox(
        $tbName,
        $tbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        return $this->_getBaseElementInputBox(
            $tbName,
            'text',
            $tbValue,
            $attributes,
            $xhtml
        );
    }

    /**
     * Create a hidden input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbValue    element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getElementHiddenBox(
        $tbName,
        $tbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        return $this->_getBaseElementInputBox(
            $tbName,
            'hidden',
            $tbValue,
            $attributes,
            $xhtml
        );
    }

    /**
     * Create a general input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbType     Type of the element.
     * @param string  $tbValue    element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getBaseElementInputBox(
        $tbName,
        $tbType = 'text',
        $tbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        $ele = '';
        $ele .= sprintf(
            '<input type="%s" name="%s" ',
            $tbType,
            $tbName
        );

        // Add in attributes
        $this->_addElementAttributes($ele, $attributes);
        
        // Add value
        if ($tbType != 'hidden') {
            if ($this->_isValue($tbName)) {
                $ele .= sprintf(
                    ' value="%s" ',
                    strip_tags($this->_getValue($tbName))
                );
            }
        }

        if ($xhtml) {
            // Self close tag
            $ele .= '/';
        }

        // Close tag
        $ele .= '>';

        return $ele;
    }

    /**
     * Add attributes to an element string
     *
     * @param string &$element   The element reference
     * @param array  $attributes Attributes array
     *
     * @return void
     */
    private function _addElementAttributes(&$element, $attributes)
    {
        // Add in attributes
        if (is_array($attributes)) {
            foreach ($attributes as $aKey => $aVal) {
                $element .= sprintf(
                    '%s="%s" ',
                    $aKey,
                    $aVal
                );
            }
        }
    }

    /**
     * Test whether a search parameter exists and has a certain value
     *
     * @param string $key   Parameter name string
     * @param string $value Comparison value
     *
     * @return boolean
     */
    private function _hasValue($key, $value)
    {
        if ($this->_isValue($key)) {
            return ($this->values[$key] == $value);
        }
        return false;
    }

    /**
     * Get a parameter value
     *
     * @param string $key Parameter name string
     *
     * @return mixed
     */
    private function _getValue($key)
    {
        if ($this->_isValue($key)) {
            return $this->values[$key];
        }
        return false;
    }

    /**
     * Check for a parameter value
     *
     * @param string $key Parameter string
     *
     * @return mixed
     */
    private function _isValue($key)
    {
        return (in_array($key, array_keys($this->values)));
    }
}