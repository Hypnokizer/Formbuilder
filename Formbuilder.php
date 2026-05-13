<?php 

// @TODO alphabetize
// @TODO create tabindex in order of field creation? set honeypot = -1
// @TODO create honeypot form element automatically?
// @TODO fill in docblocks; use old version for help
// @TODO simple foreach() to show all form elements quickly?


namespace App\Controllers;

// use DateTime;
// use Exception;

class Formbuilder {

    /**
     * master array of form details
     * @access protected;
     * @var array
     */
    protected $form;

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
     * create new instance of validator class
     * @param array $data data to validate
     * @return object Validator
     */
    public function __CONSTRUCT() {

        $this->form = array();
        $this->currentfield = NULL;
        $this->editdata = array();
    }





    /**
     * set the field name to start the validation
     * @param string $name name of the field/key as on data to validate
     * @param string $alias optional alias to use on error messages instead of field name
     * @see determineValue()
     */
	// @TODO review input types
	// https://developer.mozilla.org/en-US/docs/Learn_web_development/Extensions/Forms/HTML5_input_types
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
			'labelclass' => NULL,
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
		switch($type) {
			case 'button':
                $this->attr('class', 'btn');
				break;

			case 'checkbox':
                $this->attr('class', 'form-check-input')->attr('value', 'yes')->attr('checked', false);
				break;

			// case 'date':
			// 	$this->attr('type', 'text')->attr('class', 'form-control')->attr('placeholder', 'Select a date')->attr('autocomplete', 'off');
			// 	break;

			case 'radio':
                $this->attr('class', 'form-check-input');
				break;

			case 'reset':
                $this->attr('class', 'btn');
				break;

			case 'select':
                $this->attr('class', 'form-select');
				break;

			case 'state':
                // @TODO maybe make this a select with state abbreviations?
                $this->attr('type', 'text')->attr('class', 'form-control')->attr('placeholder', '2 letter abbreviation');
				break;

			case 'submit':
                $this->attr('class', 'btn');
				break;

			case 'textarea':
				$this->attr('class', 'form-control');
				break;

            // @TODO needed?
			case 'zip':
				$this->attr('type', 'text')->attr('class', 'form-control')->attr('placeholder', 'Standard or zip+4 format');
				break;

            // basic text input
			default:
				$this->attr('class', 'form-control');
		}

        // set editdata if present
        // @TODO replace with determineValue()
        // if(isset($this->editdata[$this->currentfield])) {
        //     $this->form[$this->currentfield]['attr']['value'] = $this->editdata[$this->currentfield];
        // }
        

        return $this;
    }



    /**
     * set label attributes 
     * @TODO finish
     */
    public function labelAttr($key, $val) {
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


        // else {
		// 	// if editdata present, set it
		// 	if($key == 'value' && isset($this->editdata[$this->currentfield])) {
		// 		$this->form[$this->currentfield]['attr'][$key] = $this->editdata[$this->currentfield];

        //         // @TODO if() for checkbox and radio; fix this!
        //         if($this->form[$this->currentfield]['attr']['type'] == 'checkbox') {
        //             $this->form[$this->currentfield]['attr']['checked'] = true;
        //         }
		// 	}
		// 	else {
		// 		
		// 	}
        // }

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
		// put the form element into a local variable
		$info = $this->form[$name];

		// echo form element based on type 
		switch($info['attr']['type']) {
			case 'button':
				$string = '<button' . $this->createAttributes($info) . '>';
				$string .= $info['label'];
				$string .= '</button>';
				break;

			case 'checkbox':
				$string = '<div class="form-check">';
				$string .= '<input' . $this->createAttributes($info) . ' />';
				$string .= $this->createLabel($info);
				$string .= '</div>';
				break;

			case 'radio':
				$string = 'radio';
				break;

			case 'reset':
				$string = '<button' . $this->createAttributes($info) . '>';
				$string .= $info['label'];
				$string .= '</button>';
				break;

			case 'select':
				$string = $this->createLabel($info);
				$string .= '<select' . $this->createAttributes($info) . '>';
				// create placeholder if present, else show empty option
				if(isset($info['attr']['placeholder'])) {
					$string .= '<option disabled selected>' . $info['attr']['placeholder'] . '</option>';
				}
				else {
					$string .= '<option value=""></option>';
				}
				foreach($info['choices'] as $key => $val) {
					$string .= '<option value="' . $key . '"';

					if($key == $info['attr']['value']) {
						$string .= ' selected';
					}

					$string .= '>' . $val . '</option>';
				}
				$string .= '</select>';
				break;

            case 'state':
                $string = 'state dropdown';
                break;

			case 'submit':
				$string = '<button' . $this->createAttributes($info) . '>';
				$string .= $info['label'];
				$string .= '</button>';
				break;

			case 'textarea':
				$string = $this->createLabel($info);
				$string .= '<textarea' . $this->createAttributes($info) . '>';
				$string .= $info['attr']['value'];
				$string .= '</textarea>';
				break;

			default:
				$string = $this->createLabel($info);
				$string .= '<input' . $this->createAttributes($info) . ' />';

		}

		echo $string;



        // display the form element
		// put the form element into a local variable
		// $info = $this->elements[$name];


		// use switch statement to echo form element based on type
		// switch($info['attr']['type']) {
		// 	case 'button':
		// 		$string = '<button' . $this->createAttributes($info) . '>';
		// 		$string .= $info['label'];
		// 		$string .= '</button>';
		// 		break;

			// case 'captcha':
			// 	echo 'captcha';
			// 	break;

			// case 'checkbox':
			// 	$string = '<div class="form-check">';
			// 	$string .= '<input' . $this->createAttributes($info) . ' />';
			// 	$string .= $this->createLabel($info);
			// 	$string .= '</div>';
			// 	break;

			// case 'hidden':
			// 	$string = '<input' . $this->createAttributes($info) . ' />';
			// 	break;

			// case 'radio':
			// 	echo 'radio';
			// 	break;

			// case 'reset':
			// 	$string = '<button' . $this->createAttributes($info) . '>';
			// 	$string .= $info['label'];
			// 	$string .= '</button>';
			// 	break;

		// 	case 'search':
		// 		echo 'search';
		// 		break;


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

			case 'radio':
				$notused = array('id', 'disabled', 'checked', 'value', 'placeholder', 'autofocus');
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
	 * @TODO refine and finish
	 * @TODO "label" uses array?
	 */
    protected function createLabel($array) {
		if(!empty($array['label'])) {
			// start label string
			$string = '<label for="' . $array['attr']['id'] . '"';

			if(!empty($array['labelclass'])) {
				$string .= ' class="' . $array['labelclass'] . '"';
			}

			$string .= '>' . $array['label'] . '</label>';

			return $string;
		}
		else {
			return NULL;
		}
    }


    // @TODO test radio, checkbox, etc
    /**
     * @see field()
     * @see attr()
     */
    protected function determineValue() {
		// if EDITDATA given, use it
		if(isset($this->editdata[$this->currentfield])) {
			if($this->form[$this->currentfield]['attr']['type'] == 'checkbox') {
                // @TODO fix; include radio?
				// $array['attr']['checked'] = true;
			}
			else {
				$this->form[$this->currentfield]['attr']['value'] = $this->editdata[$this->currentfield];
			}
		}

		// if POST given, use it
		if(isset($_POST[$this->currentfield])) {
			if($this->form[$this->currentfield]['attr']['type'] == 'checkbox') {
                // @TODO fix; include radio
				// $array['attr']['checked'] = true;
			}
			else {
				$this->form[$this->currentfield]['attr']['value'] = $_POST[$this->currentfield];
			}
		}

		// if GET given, use it
		if(isset($_GET[$this->currentfield])) {
			if($this->form[$this->currentfield]['attr']['type'] == 'checkbox') {
                // @TODO fix; include radio
				// $array['attr']['checked'] = true;
			}
			else {
				$this->form[$this->currentfield]['attr']['value'] = $_GET[$this->currentfield];
			}
		}
    }


    // @TODO rename?
    // @TODO do I set editData array? or just form data values? I have to use the editdata in the show() method so it does not get overwritten! this is done in the determineValue() method
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