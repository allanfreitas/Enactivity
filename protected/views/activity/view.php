<?
/**
 * @uses $model 
 * @uses $comment
 * @uses $commentsDataProvider
 */
?>

<header class="content-header">
	<nav class="content-header-nav">
		<ul>
			<li>
				<?=
				PHtml::link(
					PHtml::encode('Edit'), 
					array('activity/update', 'id'=>$model->id),
					array(
						'id'=>'activity-update-menu-item-' . $model->id,
						'class'=>'neutral activity-update-menu-item',
						'title'=>'Edit this activity',
					)
				);
				?>
			</li>
			<li>
				<?=
				PHtml::link(
					PHtml::encode('Timeline'), 
					array('activity/feed', 'id'=>$model->id),
					array(
						'id'=>'activity-feed-menu-item',
						'class'=>'neutral activity-feed-menu-item',
						'title'=>'View recent history of this activity',
					)
				);
				?>
			</li>
		</ul>
	</nav>
</header>

<? if($model->isDraft): ?>
<section id="publish" class="publish content">
	<p class="blurb">Publish this activity to allow group members to participate.</p>
	<?= PHtml::htmlButton("Publish", array( //html
			'data-ajax-url'=>$model->publishUrl,
			'data-csrf-token'=>Yii::app()->request->csrfToken,
			'id'=>'activity-publish-menu-item-' . $model->id,
			'class'=>'positive activity-publish-menu-item',
			'title'=>'Show this activity to the rest of the team',
		)
	); ?>
</section>
<? endif; ?>

<? if($model->description): ?>
<section id="details" class="details content">
	<?= Yii::app()->format->formatStyledText($model->description); ?>
</section>
<? endif; ?>

<section id="tasks" class="tasks content">
	<?= $this->renderPartial('/task/_agenda', array(
		'calendar'=>$calendar,
	)); ?>
</section>

<section id="comments" class="content">
	<h1>Comments</h1>
	
	<? if($comments): ?>
	<? foreach($comments as $activityComment): ?>
	<?= $this->renderPartial('/comment/_view', array(
		'data'=>$activityComment,
	)); ?>
	<? endforeach; ?>
	<? elseif($model->isCommentable): ?>
	
	<? else: ?>
	<p class="blurb">Sorry, comments have been disabled for this activity</p>
	<? endif; ?>
	
	<? if($model->isCommentable): ?>
	<?= $this->renderPartial('/comment/_form', array('model'=>$comment)); ?>
	<? endif; ?>
</section>