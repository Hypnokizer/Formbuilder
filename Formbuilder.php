<?php 

// @TODO alphabetize methods or group related; use @see in docblock
// @TODO fill in docblocks; use old version for help


namespace App\Controllers;

class Formbuilder {

    /**
     * master array of form details
     * @access protected
     * @var array
     */
    protected $form;

    /**
     * master array of form attributes
     * @access protected
     * @var array
     * @see formAttr()
     */
    protected $formattr;

    /**
     * determines type of form to be displayed: normal/floating
     * @access protected
     * @var string
     */
    protected $formtype;

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
     * dummy data to capture unchecked status from checkboxes
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
    public function __CONSTRUCT($formtype = 'normal') {

        $this->form = array();
        $this->formattr = array(
            'action' => NULL,
            'method' => 'post',
            'accept-charset' => 'utf-8'
        );
        $this->formtype = $formtype; 
        $this->currentfield = NULL;
        $this->editdata = array();
        $this->dummyvalue = 'dummy';
        $this->dummychoices = array('one' => 'One', 'two' => 'Two', 'three' => 'Three'); // @TODO change to yes/no or something useful by default?
    }


    /**
     * set the field name to start the validation
     * @param string $name name of the field/key as on data to validate
     * @param string $alias optional alias to use on error messages instead of field name
     * @see determineValue()
     */
    // @TODO add switch type (like checkbox)
    // @TODO input groups?
    // @TODO button addons?

    public function field($type, $name, $label = NULL) {
		// set the master key
		$this->currentfield = $name;

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

        // @TODO add special elements: date, period
		switch($type) {
			case 'button':
                $this->attr('class', 'btn');
				break;

			case 'checkbox':
                $this->labelAttr('class', 'form-check-label')->attr('class', 'form-check-input')->attr('value', 'yes')->attr('checked', false);
				break;

			case 'radio':
                $this->labelAttr('class', 'form-check-label')->attr('class', 'form-check-input')->choices($this->dummychoices);
				break;

			case 'reset':
                $this->attr('class', 'btn');
				break;

			case 'select':
                $this->labelAttr('class', 'form-label')->attr('class', 'form-select')->choices($this->dummychoices);
				break;

			case 'state':
                $states = array(
                    'AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL',
                    'GA','HI','ID','IL','IN','IA','KS','KY','LA','ME',
                    'MD','MA','MI','MN','MS','MO','MT','NE','NV','NH',
                    'NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI',
                    'SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY');
                
                $choices = array();
                foreach($states as $state) {
                    $choices[$state] = $state;
                }

                $this->labelAttr('class', 'form-label')->attr('type', 'select')->attr('class', 'form-select')->choices($choices);
				break;

			case 'submit':
                $this->attr('class', 'btn');
				break;

			case 'textarea':
				$this->labelAttr('class', 'form-label')->attr('class', 'form-control');
				break;

			case 'zip':
				$this->labelAttr('class', 'form-label')->attr('type', 'text')->attr('class', 'form-control')->attr('placeholder', 'Standard or zip+4 format');
				break;

            // basic text input
			default:
				$this->labelAttr('class', 'form-label')->attr('class', 'form-control');
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
     * create a datalist
     * @see 
     */
    public function datalist($name, $choices) {
        $this->field('datalist', $name)->choices($choices);
    }


    /**
     * add choices for select, radio elements
     * @TODO determine if associative array or not? create if not? use array_is_list()?
     */
    public function choices($array) {
        $this->form[$this->currentfield]['choices'] = $array;
        return $this;
    }


    /**
     * display the form element
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
			case 'checkbox':
                $string = '<input type="hidden" name="' . $name . '" value="' . $this->dummyvalue . '" />';
				$string .= '<div class="form-check">';
				$string .= '<input' . $this->createAttributes($this->form[$name]) . ' />';
				$string .= $this->createLabel($this->form[$name]);
				$string .= '</div>';
				break;

            case 'datalist':
                $string = '<datalist id="' . $this->form[$name]['attr']['id']. '">';
                foreach($this->form[$name]['choices'] as $choice) {
                    $string .= '<option value="' . $choice . '">';
                }
                $string .= '</datalist>';
                break;

            // @TODO set default value?
            // @TODO set "checked"
            // @TODO determineValue()
			case 'radio':
                $string = NULL;
                $counter = 1;
                $firstkey = array_key_first($this->form[$name]['choices']);

                foreach($this->form[$name]['choices'] as $key => $val) {
                    // array of input attributes
                    $array = $this->form[$name];
                    
                    $array['label'] = $val; // label for radio button 
                    $array['labelattr']['for'] = $this->form[$name]['attr']['name'] . $counter;
                    $array['attr']['value'] = $key; // define choice for each radio button 
                    $array['attr']['id'] = $this->form[$name]['attr']['name'] . $counter; // unique ID for each radio button                    

                    // set default selection
                    if(isset($this->form[$name]['attr']['value'])) {
                        if($key == $this->form[$name]['attr']['value']) {
                            $array['attr']['checked'] = true;
                        }
                        else {
                            $array['attr']['checked'] = false;
                        }
                    }
                    else {
                        if($key == $firstkey) {
                            $array['attr']['checked'] = true;
                        }
                        else {
                            $array['attr']['checked'] = false;
                        }
                    }

                    $string .= '<div class="form-check">';
                    $string .= '<input' . $this->createAttributes($array) . ' />';
                    $string .= $this->createLabel($array);
                    $string .= '</div>';

                    $counter++;
                }
				break;

			case 'reset':
				$string = '<button' . $this->createAttributes($this->form[$name]) . '>';
				$string .= $this->form[$name]['label'];
				$string .= '</button>';
				break;

            // @TODO make select label the first option, but without value attr?
			case 'select':    
                if($this->formtype == 'floating') {
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
                }
                else {
                    $string = $this->createLabel($this->form[$name]);
                    $string .= '<select' . $this->createAttributes($this->form[$name]) . '>';
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
                }
                break;

			case 'submit':
				$string = '<button' . $this->createAttributes($this->form[$name]) . '>';
				$string .= $this->form[$name]['label'];
				$string .= '</button>';
				break;

			case 'textarea':
                if($this->formtype == 'floating') {
                    $string = '<textarea' . $this->createAttributes($this->form[$name]) . '>';
                    $string .= $this->form[$name]['attr']['value'];
                    $string .= '</textarea>';
                    $string .= $this->createLabel($this->form[$name]);
                }
                else {
                    $string = $this->createLabel($this->form[$name]);
                    $string .= '<textarea' . $this->createAttributes($this->form[$name]) . '>';
                    $string .= $this->form[$name]['attr']['value'];
                    $string .= '</textarea>';
                }
				break;

			default:
                if($this->formtype == 'floating') {
                    $string = '<input' . $this->createAttributes($this->form[$name]) . ' />';
				    $string .= $this->createLabel($this->form[$name]);
                }
                else {
				    $string = $this->createLabel($this->form[$name]);
                    $string .= '<input' . $this->createAttributes($this->form[$name]) . ' />';
                }
                

		}

		echo $string;
    }


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
     * create label HTML
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
     * render form HTML
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
            if($this->formtype == 'floating') {
                echo '<div class="form-floating mb-3">';
            }
            else {
                echo '<div class="mb-3">';
            }

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