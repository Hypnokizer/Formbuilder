<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


<div class="container">

<p><a href="/">RELOAD PAGE</a></p>


<?php

require('Formbuilder.php');

use App\Controllers\Formbuilder;

$f = new formbuilder();


$editvalue = array();
$editdata['name'] = 'Nathan Kizer';
$editdata['mycheck1'] = 'actualvalue';
$editdata['myradio'] = 'two';
$editdata['mytextarea'] = 'this is some text';

$f->setEditData($editdata);


// $choices = array('one' => 'One', 'two' => 'Two');
// $f->field('select', 'pickone', 'Pick One')->choices($choices)->attr('placeholder', 'My placeholder text');

$f->field('text', 'name', 'Name')->labelAttr('data-id', 3)->labelAttr('class', 'form-control-label');

// $f->field('textarea', 'mytextarea', 'My Textarea');

// $f->field('checkbox', 'mycheck1', 'My Check 1')->attr('value', 'actualvalue');


// $radio = array(
//     'one' => 'One',
//     'two' => 'Two',
//     'three' => 'Three'
// );

// $f->field('radio', 'myradio', 'Radio 1')->choices($radio);



$f->field('submit', 'submit', 'Submit')->attr('class', 'btn-success');

?>

<form method="post" action="" accept-charset="utf-8">

<?php $f->show('name'); ?>


<?php $f->show('submit'); ?>

</form>



<?php

if($_POST) {
    echo '<pre>POST: ';
    print_r($_POST);
    echo '</pre>';
}

if($_GET) {
    echo '<pre>GET: ';
    print_r($_GET);
    echo '</pre>';
}

$f->showObject();


?>

</div>