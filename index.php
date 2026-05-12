<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


<div class="container">

<?php

require('Formbuilder.php');

use App\Controllers\Formbuilder;

$f = new formbuilder();

// $editdata = array(
//     'secret' => 'lakd',
//     'pickone' => 'two',
//     'zip' => '79407',
//     'phone' => '(806) 441-8282'
// );


$editdata = array(
    'mycheck1' => 'yes'
);

$f->setEditData($editdata);





// $f->hidden('secret', 'mysecret');

// $f->field('date', 'mydate', 'My Date');

// $f->field('text', 'fname', 'First Name')
//     ->attr('data-id', 3)
//     ->attr('data-title', 'my data attr')
//     ->attr('class', 'btn')
//     ->attr('class', 'btn-success')->attr('value', 'wrongname');


// $f->field('textarea', 'comments', 'Comments')->attr('data-id', 3)->attr('placeholder', 'this is placeholder text');


// $choices = array('one' => 'One', 'two' => 'Two');
// $f->field('select', 'pickone', 'Pick One')->choices($choices)->attr('placeholder', 'mytext');

// $f->field('email', 'myemail', 'Email');

$f->field('checkbox', 'mycheck1', 'My Check 1');


?>

<form method="post" action="" accept-charset="utf-8">

<?php $f->show('mycheck1'); ?>

</form>



<?php

$f->showObject();


?>

</div>