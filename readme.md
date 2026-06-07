# Formbuilder class

This class quickly builds from HTML. It is tailored for Bootstrap 5, but can be adapted for use anywhere. Almost all form element types are supported.


## Basic Use

Instantiate the object. The only parameter is optional and defines the type of form to be displayed.

```
$f = new form();
$f->formAttr('class', 'myFormClass');
```

If there is data which needs to be used to prepopulate the form, such as database results, it can be set using the `setEditData()` method.

```
$f->setEditData($databaseresults);
```

Once the form is defined, the form elements can be added in order. Every field must start with the `field()` method which outlines the type of element, the name, and the label. The `attr()` method can be used to add attributes when needed. For those form elements requiring choices or options, such as the `select` or `radio` elements, the `choices()` method can be called.

```
$f->field('text', 'fname', 'First Name')->attr('class', 'myClassName');
$f->field('select', 'title', 'Title')->choices($titles);
```

The `showForm()` method quickly displays the entire form. Alternatively, the `show()` method can be used to display each element individually. This allows for more complex form layouts.




## Debugging

There is one method used for debugging. It displays the entire object and its values.

```
$f->showObject();
```


## Complete Example

The example below shows the most basic usage of this class. 

```
$genders = array('male' => 'Male', 'female' => 'Female');

$f = new formbuilder();
$f->formAttr('id', 'myFormID')->formAttr('class', 'myclassname');

$f->setEditData($databaseresults);

$f->field('text', 'fname', 'First Name');
$f->field('text', 'lname', 'Last Name');
$f->field('text', 'age', 'Age');
$f->field('select', 'gender', 'Gender')->choices($genders);
$f->field('textarea', 'comments', 'Comments')->attr('rows', 5);
$f->field('submit', 'submit', 'Submit')->attr('class', 'css-style');

$f->showForm();

```
