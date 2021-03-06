<?
/**
 * @param $calendar TaskCalendar 
 * @param $showParent boolean defaults to true
 */

?>
<div class="agenda">
	<? foreach($calendar->datedTasks as $date => $times) : ?>
	<article class="day">
		<?= PHtml::openTag('h1', array(
			'id' => PHtml::dateTimeId($date),
			'class' => 'agenda-date',
		)); ?>
			<i></i>
			<?= PHtml::encode(Yii::app()->format->formatDate($date)); ?>
		</h1>

		<? foreach($times as $time => $activities): ?>
		<? foreach($activities as $activityEntry): ?>
		<article class="activity">
			<a class="activity-name" href="<?= PHtml::encode($activityEntry['activity']->viewUrl); ?>">
				<h1>
					<i></i> <?= PHtml::encode($activityEntry['activity']->name); ?>
				</h1>
			</a>
			<ol>
			<? foreach($activityEntry['tasks'] as $task): ?>
				<li>
					<?= $this->renderPartial('/task/_view', array(
						'data'=>$task,
					)); ?>
				</li>
			<? endforeach; ?>
			</ol>
		</article>
		<? endforeach; ?>
		<? endforeach; ?>
	</article>
	<? endforeach; ?>

	<? if($calendar->hasSomedayTasks): ?>
	<article class="someday">
		<?= PHtml::openTag('h1', array(
			'id' => 'someday-tasks',
			'class' => 'agenda-date',
		)); ?>
			<i></i>
			Someday
		</h1>
		<? foreach($calendar->somedayTasks as $activityEntry): ?>
		<article class="activity">
			<a class="activity-name" href="<?= PHtml::encode($activityEntry['activity']->viewUrl); ?>">
				<h1>
					<i></i> <?= PHtml::encode($activityEntry['activity']->name); ?>
				</h1>
			</a>
			<ol>
				<? foreach($activityEntry['tasks'] as $task): ?>
				<li>
					<?= $this->renderPartial('/task/_view', array(
						'data'=>$task,
						'showParent'=>$showParent,
					)); ?>
				</li>
				<? endforeach; ?>
			</ol>
		</article>
		<? endforeach; ?>
	</article>
	<? endif; ?>
</div>