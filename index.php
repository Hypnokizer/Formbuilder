<?php

require('Formbuilder.php');

use App\Controllers\Formbuilder;

$f = new formbuilder();

$editdata = array(
    'secret' => 'lakd',
    'pickone' => 'two',
    'zip' => '79407',
    'phone' => '(806) 441-8282'
);

$f->setEditData($editdata);





$f->hidden('secret', 'mysecret');

$f->field('date', 'mydate', 'My Date');

// $f->field('text', 'fname', 'First Name')
//     ->attr('data-id', 3)
//     ->attr('data-title', 'my data attr')
//     ->attr('class', 'btn')
//     ->attr('class', 'btn-success')->attr('value', 'wrongname');


$f->field('textarea', 'comments', 'Comments')->attr('data-id', 3)->attr('placeholder', 'this is placeholder text');


$choices = array('one' => 'One', 'two' => 'Two');
$f->field('select', 'pickone', 'Pick One')->choices($choices)->attr('placeholder', 'mytext');


$f->field('zip', 'zip', 'Zip Code');

$f->field('submit', 'submit', 'Enter');




?>


<div><?php $f->show('secret'); ?></div>

<div><?php $f->show('mydate'); ?></div>

<div><?php $f->show('comments'); ?></div>

<div><?php $f->show('pickone'); ?></div>

<div><?php $f->show('zip'); ?></div>

<div><?php $f->show('submit'); ?></div>




<?php

$f->showObject();


?>