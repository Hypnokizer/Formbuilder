<?php 

// @TODO alphabetize methods or group related; use @see in docblock


/**
 * Quickly build form HTML
 * 
 * Quickly create and define form elements. Expressly used for rendering the HTML for a form element. It is tailored for Bootstrap 5, but can be adapted for use anywhere.
 * 
 * @author Nathan Kizer <hypnokizer@gmail.com>
 * @version 7.0
 * @revision 2026-05-22 Added ability to chain methods
 * @todo change default choices array in construct() to something inherently useful
 */

namespace App\Controllers;

class Formbuilder {

    /**
     * Master array of form details.
     * @access protected
     * @var array
     */
    protected $form;

    /**
     * Master array of form attributes.
     * @access protected
     * @var array
     * @see formAttr()
     */
    protected $formattr;

    /**
     * Determines type of form to be displayed: normal/floating.
     * @access protected
     * @var string
     */
    protected $formtype;

    /**
     * Currently selected key/field to create form element.
     * @access protected
     * @var string
     */
    protected $currentfield;

    /**
     * Array of values for prepopulating forms, usually from a database.
     * @access protected
     * @var array
     */
    protected $editdata;

    /**
     * Dummy data to capture unchecked status from checkboxes and switches.
     * @access protected
     * @var string
     */
    protected $dummyvalue;

    /**
     * Dummy data for elements requiring options (SELECT, RADIO).
     * @access protected
     * @var array
     */
    protected $dummychoices;

    /**
     * Create new instance of validator class.
     * 
     * @param array $formtype Type of form to display. Default is normal.
     * @return object 
     */
    public function __CONSTRUCT(string $formtype = 'normal') {

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
        $this->dummychoices = array('one' => 'One', 'two' => 'Two', 'three' => 'Three');
    }


    /**
     * Set the field name to start the form element.
     * 
     * Adds a new form element to the master array and defines default values.
     * 
     * @param string $type Type of form element.
     * @param string $name Unique name of form element.
     * @param string $label Optional label text for form element.
     * @return object 
     * @see determineValue()
     */

    public function field(string $type, string $name, string $label = NULL) {
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

            case 'switch':
                $this->labelAttr('class', 'form-check-label')->attr('class', 'form-check-input')->attr('type', 'switch')->attr('value', 'yes');
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
     * Set label attributes.
     * 
     * Set the label attributes for a form element. Class attributes are added as an array.
     * 
     * @param string $key The type of label attribute.
     * @param string $val The value of the label attribute.
     * @return object
     */
    public function labelAttr(string $key, string $val) {
        if($key == 'class') {
            $this->form[$this->currentfield]['labelattr'][$key][] = $val;
        }
        else {
            $this->form[$this->currentfield]['labelattr'][$key] = $val;
        }

        return $this;
    }


    /**
     * Set form field attributes.
     * 
     * Set the attributes for a form element. Class attributes are added as an array.
     * 
     * @param string $key The type of attribute.
     * @param string $val The value of the attribute.
     * @return object
     * @see determineValue()
     */
    public function attr(string $key, string $val) {
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
     * Add a hidden field.
     * 
     * This is a shortcut to add a hidden form element. It utilizes the field() method.
     * 
     * @param string $name The name of the hidden field.
     * @param string $value The value of the hidden field.
     * @return object
     * @see field()
     */
    public function hidden(string $name, string $value) {
        $this->field('hidden', $name)->attr('value', $value);
        return $this;
    }


    /**
     * Create a datalist.
     * 
     * This is a shortcut to add a datalist to be used in conjunction with a text input. It utilizes the field()  and choices() methods. It creates a predefined list of options for a text field. The text field should reference the name of the datalist using a list attribute.
     * 
     * @param string $name The name of the datalist.
     * @param array $choices The indexed array of choices for the datalist.
     * @return object
     * @see field()
     */
    public function datalist(string $name, array $choices) {
        $this->field('datalist', $name)->choices($choices);
        return $this;
    }


    /**
     * Add choices for select and radio elements.
     * 
     * @param array $array An associative array of choices to be used in select and radio elements.
     * @return object
     * @see construct()
     */
    public function choices(array $array) {
        $this->form[$this->currentfield]['choices'] = $array;
        return $this;
    }


    /**
     * Display the form element.
     * 
     * Display the various form elements defined in the master array. Echoes an HTML string for the form element.
     * 
     * @param string $name The field name to render.
     * @return string
     * @see createAttributes()
     * @see createLabel()
     * @see show()
     * @see showForm()
     */
    public function show(string $name) {
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

            case 'switch':
                $this->form[$name]['attr']['type'] = 'checkbox'; // change type after "switch" is selected
                $string = '<input type="hidden" name="' . $name . '" value="' . $this->dummyvalue . '" />';
				$string .= '<div class="form-check form-switch">';
				$string .= '<input' . $this->createAttributes($this->form[$name]) . ' />';
				$string .= $this->createLabel($this->form[$name]);
				$string .= '</div>';
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



    /**
     * Creates an attribute string.
     * 
     * Creates an attribute string based on the master form array definitions.
     * 
     * @param array $array Array of form element definitions.
     * @return string
     * @see createLabel()
     * @see show()
     * @see showForm()
     */
    protected function createAttributes(array $array) {
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
				$notused = array('autofocus', 'placeholder');
				break;

			case 'select':
				$notused = array('type', 'checked', 'value');
				break;

            case 'switch':
                $notused = array('placeholder'); 
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
     * Create label HTML.
     * 
     * Creates the label for a form element based on the master form array definitions.
     * 
     * @param array $array Array of form label definitions.
     * @return mixed
     * @see createAttributes()
     * @see show()
     * @see showForm()
	 */
    protected function createLabel(array $array) {
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



    /**
     * Determine value of form element.
     * 
     * Determines the value of the current form element based on editdata, POST, and GET values. Special cases made for checkbox and switch elements.
     * 
     * @return void
     * @see editdata
     * @see field()
     * @see attr()
     */
    protected function determineValue() {
        // if EDITDATA exists use it
        if(isset($this->editdata[$this->currentfield])) {
            if($this->form[$this->currentfield]['attr']['type'] == 'checkbox' || $this->form[$this->currentfield]['attr']['type'] == 'switch') {
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
            if($this->form[$this->currentfield]['attr']['type'] == 'checkbox' || $this->form[$this->currentfield]['attr']['type'] == 'switch') {
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
            if($this->form[$this->currentfield]['attr']['type'] == 'checkbox' || $this->form[$this->currentfield]['attr']['type'] == 'switch') {
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
     * Renders form HTML.
     * 
     * Renders form HTML including defined form attributes. Wraps each form element in a div based on the form type defined in the construct().
     * 
     * @return string
     * @see construct()
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




    /**
     * Add attributes to the form.
     * 
     * @param string $key The type of attribute.
     * @param string $val The value of the attribute.
     * @return object
     * @see showForm()
     */
    public function formAttr(string $key, string $val) {
        // if class attribute, add to an array
        if($key == 'class') {
            $this->formattr[$key][] = $val;
        }
        else {
            $this->formattr[$key] = $val;
        }

        return $this;
    }



    /**
     * Define data used to prepopulate the form.
     * 
     * This data typically comes from a database record. It is an associative array of name and values, where the array keys are form field names.
     * 
     * @param array $data Associative array of form values.
     * @return void
     */
    public function setEditData(array $data) {
        $this->editdata = $data;
    }





    /**
     * Show the object for debugging.
     * 
     * @return string
     */
    public function showObject() {
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }


} // end class


?>