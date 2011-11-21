<?php 
$this->pageTitle = 'Discover a better way to interact with your group'; 
?>

<header class="greetings">
	<h1><?php echo PHtml::encode($this->pageTitle);?></h1>
</header>
<?php $form=$this->beginWidget('application.components.widgets.ActiveForm', array(
	'id'=>'contact-form',
	'htmlOptions'=>array(
		'class'=>'inline-form',
	),
	//'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<h2>Sign up for news about our launch.</h2>

	<div class="field">
		<div class="formlabel"><?php echo $form->labelEx($model,'email'); ?></div>
		<div class="forminput"><?php echo $form->emailField($model,'email', 
			array(
				'placeholder'=>'@',
				'autofocus'=>'autofocus',
			)); ?></div>
		<div class="formerrors"><?php echo $form->error($model,'email'); ?></div>
	</div>

	<div class="field buttons">
		<?php echo CHtml::submitButton('Notify me'); ?>
	</div>

<?php $this->endWidget(); ?>