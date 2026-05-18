<?php 

// @TODO alphabetize methods or group related; use @see in docblock
// @TODO create tabindex in order of field creation? set honeypot = -1
// @TODO create honeypot form element automatically?
// @TODO fill in docblocks; use old version for help
// @TODO create method for using floating forms or they basic BS5 format


namespace App\Controllers;

class Formbuilder {

    /**
     * master array of form details
     * @access protected;
     * @var array
     */
    protected $form;

    /**
     * master array of form attributes
     * @access protected;
     * @var array
     * @see formAttr()
     */
    protected $formattr;

    /**
     * currently selected key/field to validate data
     * @access protected
     * @var string
     */
    protected $currentfield;

    /**
     * 
     */
    protected $editdata;

    /**
     * dummy data to capture unchecked status from 
     * @TODO create method to set this value? set in construct()
     */
    protected $dummyvalue;

    /**
     * dummy data for elements requiring options (SELECT, RADIO)
     */
    protected $dummychoices;

    /**
     * create new instance of validator class
     * @param array $data data to validate
     * @return object Validator
     */
    public function __CONSTRUCT() {

        $this->form = array();
        $this->formattr = array(
            'action' => NULL,
            'method' => 'post',
            'accept-charset' => 'utf-8'
        );
        $this->currentfield = NULL;
        $this->editdata = array();
        $this->dummyvalue = 'dummy';
        $this->dummychoices = array('one' => 'One', 'two' => 'Two', 'three' => 'Three'); // @TODO change to yes/no or something useful by default?
    }

    /*
    @TODO
    form attributes: action, method, autocomplete, novalidate, target, enctype
    enctype = application/x-www-form-urlencoded
    markup = html 
    novalidate = false
    */


    /**
     * set the field name to start the validation
     * @param string $name name of the field/key as on data to validate
     * @param string $alias optional alias to use on error messages instead of field name
     * @see determineValue()
     */
	// @TODO review input types
    // @TODO add switch type (like checkbox)
    // @TODO input groups?
    // @TODO button addons?

    public function field($type, $name, $label = NULL) {
		// set the master key
		$this->currentfield = $name;

        // @TODO create default dummy choices for select, radio?
		// set default values
		$this->form[$this->currentfield] = array(
			'label' => $label,
			'labelattr' => array(
                'for' => $this->currentfield
            ),
			'attr' => array(
				'type' => $type,
				'id' => $name,
				'name' => $name,
				'class' => array(),
				'value' => NULL
			)
		);

        // @TODO switch for default settings for various form elements
        // @TODO fix setLabelClass for radio, checkbox, etc
        // @TODO apply bootstrap5 classes to labels as needed
        // @TODO create datalists with LIST, ID, CLASS attributes?
        // @TODO make select label the first option, but without value attr?
		switch($type) {
			case 'button':
                $this->attr('class', 'btn');
				break;

			case 'checkbox':
                $this->labelAttr('class', 'form-check-label')->attr('class', 'form-check-input')->attr('value', 'yes')->attr('checked', false);
				break;

			// case 'date':
			// 	$this->attr('type', 'text')->attr('class', 'form-control')->attr('placeholder', 'Select a date')->attr('autocomplete', 'off');
			// 	break;

			case 'radio':
                $this->labelAttr('class', 'form-check-label')->attr('class', 'form-check-input')->choices($this->dummychoices);
				break;

			case 'reset':
                $this->attr('class', 'btn');
				break;

			case 'select':
                $this->labelAttr('class', 'form-label')->attr('class', 'form-select')->choices($this->dummychoices)->attr('placeholder', $label);
				break;

			case 'state':
                // @TODO maybe make this a select with state abbreviations?
                $this->labelAttr('class', 'form-label')->attr('type', 'text')->attr('class', 'form-control')->attr('placeholder', '2 letter abbreviation');
				break;

			case 'submit':
                // @TODO should submit button have a value? to test for submission? in html5?
                $this->attr('class', 'btn');
				break;

			case 'textarea':
				$this->labelAttr('class', 'form-label')->attr('class', 'form-control')->attr('placeholder', $label);
				break;

            // @TODO needed?
			case 'zip':
				$this->labelAttr('class', 'form-label')->attr('type', 'text')->attr('class', 'form-control')->attr('placeholder', 'Standard or zip+4 format');
				break;

            // basic text input
			default:
				$this->labelAttr('class', 'form-label')->attr('class', 'form-control')->attr('placeholder', $label);
		}

        // set editdata if present       
        $this->determineValue();

        return $this;
    }



    /**
     * set label attributes 
     */
    public function labelAttr($key, $val) {
        if($key == 'class') {
            $this->form[$this->currentfield]['labelattr'][$key][] = $val;
        }
        else {
            $this->form[$this->currentfield]['labelattr'][$key] = $val;
        }

        return $this;
    }


    /**
     * set form field attribute
     * @see determineValue()
     */
    public function attr($key, $val) {
        // if class attribute, add to an array
        if($key == 'class') {
            $this->form[$this->currentfield]['attr'][$key][] = $val;
        }
        else {
            $this->form[$this->currentfield]['attr'][$key] = $val;
        }

        $this->determineValue();

        return $this;
    }


    /**
     * add a hidden field
     */
    public function hidden($name, $value) {
        $this->field('hidden', $name)->attr('value', $value);
        return $this;
    }


    /**
     * add choices for select, radio elements
     * @TODO determine if associative array or not? create if not?
     */
    public function choices($array) {
        $this->form[$this->currentfield]['choices'] = $array;
        return $this;
    }


    /**
     * display the form element
     * @TODO revise and finish
     */
    public function show($name) {
		// echo form element based on type 
		switch($this->form[$name]['attr']['type']) { 
			case 'button':
				$string = '<button' . $this->createAttributes($this->form[$name]) . '>';
				$string .= $this->form[$name]['label'];
				$string .= '</button>';
				break;

            // add hidden input with same name to capture unchecked form submissions
            // @TODO add "checkbox" prefix to dummyvalue to differentiate from radio buttons?
			case 'checkbox':
                $string = '<input type="hidden" name="' . $name . '" value="' . $this->dummyvalue . '" />';
				$string .= '<div class="form-check">';
				$string .= '<input' . $this->createAttributes($this->form[$name]) . ' />';
				$string .= $this->createLabel($this->form[$name]);
				$string .= '</div>';
				break;

            // @TODO set default value?
            // @TODO set "checked"
            // @TODO determineValue()
			case 'radio':
                $string = NULL;
                $counter = 1;
                $firstkey = array_key_first($this->form[$name]['choices']);

                foreach($this->form[$name]['choices'] as $key => $val) {
                    // set default selection
                    if($key == $firstkey) {
                        $this->form[$name]['attr']['checked'] = true;
                    }
                    else {
                        $this->form[$name]['attr']['checked'] = false;
                    }

                    $this->form[$name]['attr']['value'] = $key; // define choice for each radio button 
                    $this->form[$name]['attr']['id'] = $this->form[$name]['attr']['name'] . $counter; // unique ID to each radio button
                    $this->form[$name]['label'] = $val; // define label for each radio button
                    $this->form[$name]['labelattr']['for'] = $this->form[$name]['attr']['name'] . $counter;

                    $string .= '<div class="form-check">';
                    $string .= '<input' . $this->createAttributes($this->form[$name]) . ' />';
                    $string .= $this->createLabel($this->form[$name]);
                    $string .= '</div>';

                    $counter++;
                }
				break;

			case 'reset':
				$string = '<button' . $this->createAttributes($this->form[$name]) . '>';
				$string .= $this->form[$name]['label'];
				$string .= '</button>';
				break;

			case 'select':
                
				$string = '<select' . $this->createAttributes($this->form[$name]) . '>';
				// create placeholder if present, else show empty option
				if(isset($this->form[$name]['attr']['placeholder'])) {
                    $string .= '<option disabled selected>' . $this->form[$name]['attr']['placeholder'] . '</option>';
                }
                else {
                        $string .= '<option value=""></option>';
                }

                foreach($this->form[$name]['choices'] as $key => $val) {
                    $string .= '<option value="' . $key . '"';
                    
                    if($key == $this->form[$name]['attr']['value']) {
                        $string .= ' selected';
                    }
                        
                    $string .= '>' . $val . '</option>';
                }
                $string .= '</select>';
                $string .= $this->createLabel($this->form[$name]);
                break;
                                
			// @TODO finish
            case 'state':
                $string = 'state dropdown';
                break;

			case 'submit':
				$string = '<button' . $this->createAttributes($this->form[$name]) . '>';
				$string .= $this->form[$name]['label'];
				$string .= '</button>';
				break;

			case 'textarea':
				$string = '<textarea' . $this->createAttributes($this->form[$name]) . '>';
				$string .= $this->form[$name]['attr']['value'];
				$string .= '</textarea>';
				$string .= $this->createLabel($this->form[$name]);
				break;

			default:
                $string = '<input' . $this->createAttributes($this->form[$name]) . ' />';
				$string .= $this->createLabel($this->form[$name]);

		}

		echo $string;
    }



// @TODO 

    // @TODO buttons can use attr: form, formaction, formmethod, formtarget, formenctype, formnovalidate
    // @TODO button also: autofocus, command, commandfor, disabled, value
    // @TODO button type: submit, reset, button
    protected function createAttributes($array) {
		// make class array into a string
		if(!empty($array['attr']['class'])) {
			$array['attr']['class'] = implode(' ', $array['attr']['class']);
		}

		// string for attributes
		$string = NULL;

		// define array of non-used attributes
		$notused = array();

		// each element has an array of attributes to NOT USE
		switch($array['attr']['type']) {
			case 'button':
				$notused = array('checked', 'placeholder');
				break;

			case 'checkbox':
				$notused = array('placeholder');
				break;

            // @TODO allow "checked" attribute?
            // @TODO set default checked? how?
            // @TODO allow "disabled"?
            // @TODO review NOTUSED values...
			case 'radio':
				$notused = array('autofocus', 'placeholder');
				break;

			case 'select':
				$notused = array('type', 'checked', 'value');
				break;

			case 'textarea':
				$notused = array('type', 'checked', 'value');
				break;

			// inputs and undefined elements
			default:
				$notused = array('checked');
		}

		// step thru the array adding attributes
		foreach($array['attr'] as $key => $val) {
			if(!in_array($key, $notused)) {
				if(is_bool($val)) {
					if($val == true) {
						$string .= ' ' . $key;
					}
				}
				else {
					// allows for VALUE to be set to zero!
					if(!empty($val) || $val == 0) {
						$string .= ' ' . $key . '="' . htmlentities((string)$val, ENT_QUOTES) . '"';
					}
				}
			}
		}

		// return the string
		return $string;
    }



	/**
     * @TODO research form label attribute options
     * @TODO test labels for radio, checkboxes, etc
	 */
    protected function createLabel($array) {
        if(!empty($array['label'])) {
            $string = '<label ';

            if(!empty($array['labelattr'])) {
                foreach($array['labelattr'] as $key => $val) {
                    if($key == 'class') {
                        $classes = implode(' ', $val);
                        $string .= 'class="' . $classes . '"';
                    }
                    else {
                        $string .= $key . '="' . $val . '"';
                    }
                }
            }

            $string .= '>' . $array['label'] . '</label>';
            return $string;
        }
        else {
            return NULL;
        }
    }


    // @TODO test radio
    // @TODO add logic for radio buttons
    /**
     * @see field()
     * @see attr()
     */
    protected function determineValue() {
        // if EDITDATA exists use it
        if(isset($this->editdata[$this->currentfield])) {
            if($this->form[$this->currentfield]['attr']['type'] == 'checkbox') {
                if($this->form[$this->currentfield]['attr']['value'] == $this->editdata[$this->currentfield]) {
                    $this->form[$this->currentfield]['attr']['checked'] = true;
                }
                else {
                    $this->form[$this->currentfield]['attr']['checked'] = false;
                }
            }
            // elseif($this->form[$this->currentfield]['attr']['type'] == 'radio') {
            //     if($this->form[$this->currentfield]['attr']['value'] == $this->editdata[$this->currentfield]) {
            //         $this->form[$this->currentfield]['attr']['checked'] = true;
            //     }
            //     else {
            //         $this->form[$this->currentfield]['attr']['checked'] = false;
            //     }
            // }
            else {
                $this->form[$this->currentfield]['attr']['value'] = $this->editdata[$this->currentfield];
            }
        }

        // if POST exists use it
        if(isset($_POST[$this->currentfield])) {
            if($this->form[$this->currentfield]['attr']['type'] == 'checkbox') {
                if($this->form[$this->currentfield]['attr']['value'] == $_POST[$this->currentfield]) {
                    $this->form[$this->currentfield]['attr']['checked'] = true;
                }
                else {
                    $this->form[$this->currentfield]['attr']['checked'] = false;
                }
            }
            else {
                $this->form[$this->currentfield]['attr']['value'] = $_POST[$this->currentfield];
            }
        }

        // if GET exists use it
        if(isset($_GET[$this->currentfield])) {
            if($this->form[$this->currentfield]['attr']['type'] == 'checkbox') {
                if($this->form[$this->currentfield]['attr']['value'] == $_GET[$this->currentfield]) {
                    $this->form[$this->currentfield]['attr']['checked'] = true;
                }
                else {
                    $this->form[$this->currentfield]['attr']['checked'] = false;
                }
            }
            else {
                $this->form[$this->currentfield]['attr']['value'] = $_GET[$this->currentfield];
            }
        }
    }







    /**
     * render form
     * @TODO review
     */

    public function showForm() {
        // create form attributes from array
        $attributes = NULL;

        // create string from classes
        $this->formattr['class'] = implode(' ', $this->formattr['class']);

        // create attribute string
        foreach($this->formattr as $key => $val) {
            if(is_bool($val) && $val == true) {
                $attributes .= ' ' . $key;
            }
            else {
                $attributes .= ' ' . $key . '="' . htmlentities((string)$val, ENT_QUOTES) . '"';
            }
        }
        
        echo '<form ' . $attributes . '>';

        foreach($this->form as $key => $val) {
            echo '<div class="form-floating mb-3">';
            $this->show($key);
            echo '</div>';
        }

        echo '</form>';
    }


    /*
    @TODO
    attributes: action, method, autocomplete, novalidate, target, enctype

    enctype = application/x-www-form-urlencoded
    novalidate = false
    */


    /**
     * add attributes to the form 
     * @TODO review
     */
    public function formAttr($key, $val) {
        // if class attribute, add to an array
        if($key == 'class') {
            $this->formattr[$key][] = $val;
        }
        else {
            $this->formattr[$key] = $val;
        }

        return $this;
    }






    // @TODO docblock
    // uses associative array from database, etc to populate form
    public function setEditData($data) {
        $this->editdata = $data;
    }





    /**
     * show the object for debugging
     */
    public function showObject() {
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }


} // end class


?>