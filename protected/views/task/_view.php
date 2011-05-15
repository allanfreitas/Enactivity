<?php 
/**
 * View for individual task models
 * 
 * @param Task $data model
 */

// calculate article class
$articleClass = "view";
$articleClass .= " task";
$articleClass .= " task-" . $data->id;
$articleClass .= $data->isCompleted ? " completed" : " not-completed";
$articleClass .= $data->isTrash ? " trash" : " not-trash";

// start article
echo PHtml::openTag('article', array(
	'class' => $articleClass,		
));

// start headers
echo PHtml::openTag('header');
echo PHtml::openTag('hgroup');

// event date
if(isset($data->starts)) {
	echo PHtml::openTag('h2');
	echo 'Starts ';
	echo PHtml::openTag('time');
	echo Yii::app()->dateFormatter->formatDateTime(PHtml::encode($data->starts), 'full');
	echo PHtml::closeTag('time');
	echo PHtml::closeTag('h2');
}

if(isset($data->ends)) {
	echo PHtml::openTag('h2');
	echo 'Ends ';
	echo PHtml::openTag('time');
	echo Yii::app()->dateFormatter->formatDateTime(PHtml::encode($data->ends), 'full');
	echo PHtml::closeTag('time');
	echo PHtml::closeTag('h2');
}

// task name
echo PHtml::openTag('h1');

echo PHtml::link(
	PHtml::encode($data->name), 
	array('task/view', 'id'=>$data->id)
); 
echo PHtml::closeTag('h1');

// close headers
echo PHtml::closeTag('hgroup');
echo PHtml::closeTag('header');

// body

// list participants
echo PHtml::openTag('ol', array('class' => 'participants'));
foreach($data->participants as $user) {
	echo PHtml::openTag('li');	
	$this->widget('ext.widgets.UserLink', array(
		'userModel' => $user,
	));
	echo PHtml::closeTag('li');
}
echo PHtml::closeTag('ol');

// end body

//	tasks toolbar
echo PHtml::openTag('menu');

$this->widget('zii.widgets.CMenu', array(
	'items'=>MenuDefinitions::taskMenu($data),
));
echo PHtml::closeTag('menu');
// end of toolbar

// close article
echo PHtml::closeTag('article');