<?php

require('Formbuilder.php');

use App\Controllers\Formbuilder;

$f = new formbuilder();

$editdata = array(
    'secret' => 'lakd',
    'fname' => 'Steven',
    'pickone' => 'two'
);

$f->setEditData($editdata);





$f->hidden('secret', 'mysecret');

$f->field('text', 'fname', 'First Name')
    ->attr('data-id', 3)
    ->attr('data-title', 'my data attr')
    ->attr('class', 'btn')
    ->attr('class', 'btn-success');


$choices = array('one' => 'One', 'two' => 'Two');
$f->field('select', 'pickone')->choices($choices);


$f->showObject();


?>