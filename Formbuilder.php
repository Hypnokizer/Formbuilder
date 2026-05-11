<?php 

// @TODO alphabetize
// @TODO create tabindex in order of field creation? set honeypot = -1
// @TODO create honeypot form element automatically?



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
     * create new instance of validator class
     * @param array $data data to validate
     * @return object Validator
     */
    public function __CONSTRUCT() {

        $this->form = array();
        $this->currentfield = NULL;
    }





    /**
     * set the field name to start the validation
     * @param string $name name of the field/key as on data to validate
     * @param string $alias optional alias to use on error messages instead of field name
     */
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
     */
    public function attr($key, $val) {
        // if class attribute, add to an array
        if($key == 'class') {
            $this->form[$this->currentfield]['attr'][$key][] = $val;
        }
        else {
            $this->form[$this->currentfield]['attr'][$key] = $val;
        }

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
     * @TODO finish
     */
    public function show($name) {
        // display the form element
        return $this;
    }



// @TODO 

    protected function createAttr() {

    }


    protected function createLabel() {

    }


    protected function determineValue() {

    }


    // @TODO rename?
    // @TODO replace form values at run time rather than show() form element?
    public function setEditData() {

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