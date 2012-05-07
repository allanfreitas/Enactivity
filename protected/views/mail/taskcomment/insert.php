<?php 
/**
 * View for taskcomment model insert scenario
 * 
 */

// calculate article class
$articleClass = "view";
$articleClass .= " email";

// start article
echo PHtml::openTag('article', array(
	'class' => 'email'
));

// created date
echo PHtml::openTag('p');
echo PHtml::openTag('strong');
echo PHtml::openTag('time');
echo PHtml::encode(
	//FIXME: use actual event time
	Yii::app()->format->formatDateTime(time())
);
echo PHtml::closeTag('time');
echo PHtml::closeTag('strong');
echo PHtml::closeTag('p');
echo PHtml::openTag('p');

//Yo! [user] just left a comment for [taskName]
//echo "Yo! " . PHtml::encode($user->fullName) . " just left a comment for " . PHtml::encode($data->task->name) . "." ;

foreach($changedAttributes as $header)
{
	//todo: taskcomment getTask
	echo "Yo! " . PHtml::encode($user->fullName) . " just left a comment for " . PHtml::encode($data->getModelObject()->name) . "." ;
}

echo PHtml::closeTag('article');
?>