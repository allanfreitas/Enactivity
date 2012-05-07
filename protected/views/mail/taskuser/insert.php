<?php 
/**
 * View for taskuser model insert scenario
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

//Woot! [user] signed up for [taskName] along with [taskCount] other people.
//echo "Woot! " . PHtml::encode($user->fullName) . " signed up for " . PHtml::encode($data->task->name) . ".";

foreach($changedAttributes as $header)
{
	echo "Woot! " . PHtml::encode($user->fullName) . " signed up for " . PHtml::encode($data->task->name) . ".";
}

echo PHtml::closeTag('article');
?>