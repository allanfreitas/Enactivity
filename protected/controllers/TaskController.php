<?php

Yii::import("application.components.web.Controller");
Yii::import("application.components.introduction.TutorialActivityGenerator");

class TaskController extends Controller
{
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('calendar','someday'),
				'users'=>array('@'),
			),
			array('allow', 
				'actions'=>array(
					'view','update','trash','untrash',
					'signup','start','resume',
					'complete','quit','ignore','feed',
				),
				'expression'=>'Yii::app()->controller->isParticipantOrGroupMember',
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin'),
				'expression'=>'$user->isAdmin',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function getIsParticipantOrGroupMember() {
		// get the group assigned to the event
		if(!empty($_GET['id'])) {
			$task = $this->loadTaskModel($_GET['id']);

			if(StringUtils::isNotBlank($task->groupId)) {
				return Yii::app()->user->isGroupMember($task->groupId);
			}

			$response = Response::model()->findByAttributes(array(
				'taskId' => $task->id,
				'userId' => Yii::app()->user->id,
			));

			return !is_null($response);
		}
		return false;
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		// load model
		$model = $this->loadTaskModel($id);
		$response = Response::loadResponse($model->id, Yii::app()->user->id);

		// Comments
		$comment = $this->handleNewComment($model);
		$comments = $model->comments;

		$this->pageTitle = $model->name;
		
		$this->render(
			'view',
			array(
				'model' => $model,
				'response' => $response,
				'comment' => $comment,
				'comments' => $comments,
			)
		);
	}

	public function actionFeed($id) {
		// load model
		$model = $this->loadTaskModel($id);

		// Feed
		$feedDataProvider = new CArrayDataProvider($model->feed);

		$this->pageTitle = 'Timeline for ' . $model->name;

		$this->render(
			'feed', 
			array(
				'model' => $model,
				'feedDataProvider' => $feedDataProvider,
			)
		);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadTaskModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Task']))
		{
			if($model->updateTask($_POST['Task'])) {
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->pageTitle = 'Edit Task';

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Trashes a particular model.
	 * If trash is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionTrash($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow trashing via POST request
			$task = $this->loadTaskModel($id);
			$task->trash();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(Yii::app()->request->isAjaxRequest) {
				$this->renderAjaxResponse('/task/_view', array('data'=>$task));
			}
			$this->redirectReturnUrlOrView($task);
		}
		else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Untrashes a particular model.
	 * If untrash is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionUntrash($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow untrashing via POST request
			$task = $this->loadTaskModel($id);
			$task->untrash();
				
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(Yii::app()->request->isAjaxRequest) {
				$this->renderAjaxResponse('/task/_view', array('data'=>$task));
			}
			$this->redirectReturnUrlOrView($task);
		}
		else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Adds the current user to task
	 * If add is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionSignUp($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow participating via POST request
			Response::signUp($id, Yii::app()->user->id);
			$task = $this->loadTaskModel($id);

			Yii::app()->metrics->record('response/signup', array(
				'taskId' => $task->id,
			));

			// if AJAX request
			if(Yii::app()->request->isAjaxRequest) {
				$this->renderAjaxResponse('/task/_view', array('data'=>$task));
			}
			$this->redirectReturnUrlOrView($task);
		}
		else {
			throw new CHttpException(405,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Starts the current user on the task
	 * If add is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionStart($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$response = Response::loadResponse($id, Yii::app()->user->id);

			// we only allow participating via POST request
			if($response->start()) {
				$task = $response->task;

				ResponseNotification::start($response, $task, Yii::app()->user->model);

				Yii::app()->metrics->record('response/start', array(
					'taskId' => $task->id,
				));

				// if AJAX request
				if(Yii::app()->request->isAjaxRequest) {
					$this->renderAjaxResponse('/task/_view', array('data'=>$task));
				}
				
				$this->redirectReturnUrlOrView($task);	
			}
		}
		else {
			throw new CHttpException(405,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Removes current user from task
	 * If remove is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionQuit($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow unparticipating via POST request
			Response::quit($id, Yii::app()->user->id);
			$task = $this->loadTaskModel($id);

			Yii::app()->metrics->record('response/quit', array(
				'taskId' => $task->id,
			));
				
			// if AJAX request
			if(Yii::app()->request->isAjaxRequest) {
				$this->renderAjaxResponse('/task/_view', array('data'=>$task));
			}
			$this->redirectReturnUrlOrView($task);
		}
		else {
			throw new CHttpException(405,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Removes current user from task
	 * If remove is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionIgnore($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow unparticipating via POST request
			Response::ignore($id, Yii::app()->user->id);
			$task = $this->loadTaskModel($id);

			Yii::app()->metrics->record('response/ignore', array(
				'taskId' => $task->id,
			));
				
			// if AJAX request
			if(Yii::app()->request->isAjaxRequest) {
				$this->renderAjaxResponse('/task/_view', array('data'=>$task));
			}
			$this->redirectReturnUrlOrView($task);
		}
		else {
			throw new CHttpException(405,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Marks the user as having completed the task
	 * If add is successful, the browser will be redirected to the parent's 'view' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionComplete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow completion via POST request
			Response::complete($id, Yii::app()->user->id);
			$task = $this->loadTaskModel($id);

			Yii::app()->metrics->record('response/complete', array(
				'taskId' => $task->id,
			));

			// if AJAX request
			if(Yii::app()->request->isAjaxRequest) {
				$this->renderAjaxResponse('/task/_view', array('data'=>$task));
			}
			$this->redirectReturnUrlOrView($task);
		}
		else {
			throw new CHttpException(405,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Marks the user as having uncompleted the task
	 * If add is successful, the browser will be redirected to the parent's 'view' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionResume($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow uncomplete via POST request
			Response::resume($id, Yii::app()->user->id);
			$task = $this->loadTaskModel($id);

			Yii::app()->metrics->record('response/resume', array(
				'taskId' => $task->id,
			));
				
			// if AJAX request
			if(Yii::app()->request->isAjaxRequest) {
				$this->renderAjaxResponse('/task/_view', array('data'=>$task));
			}
			$this->redirectReturnUrlOrView($task);
		}
		else {
			throw new CHttpException(405,'Invalid request. Please do not repeat this request again.');
		}
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow trashing via POST request
			$task = $this->loadTaskModel($id);
			$parentId = null;
			if(!$task->isRoot) {
				$parentId = $task->getParent()->id; // so we can use it for redirect
			}
				
			if($task->deleteNode()) {
				if(!is_null($parentId)) {
					$this->redirect(array('task/view', 'id'=>$parentId));
				}
				else {
					$this->redirect(array('task/index'));
				}
			}
			else {
				// Something went wrong
				Yii::app()->user->setFlash('error', 'There was an error deleting the Task, please try again later.');
				$this->redirect(array('task/view', 'id'=>$task->id));
			}
		}
		else {
			throw new CHttpException(405,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Task('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Task'])) {
			$model->attributes=$_GET['Task'];
		}

		$this->pageTitle = 'Manage Tasks';

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Return a new task based on POST data
	 * @param int $activity the id of the new task's parent
	 * @param array $attributes attributes used to set default values
	 * @return Task if not saved, directs otherwise
	 */
	public function handleNewTaskForm($model = null) {
		if(is_null($model)) {
			$model = new Task(Task::SCENARIO_INSERT);
		}
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		return $model;
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='task-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * Redirect the current view to the return url value or
	 * to the task/view page if no return url is specified.
	 *
	 * If task is null, redirect to 'task/index'
	 *
	 * @param Task $task
	 */
	private function redirectReturnUrlOrView($task) {
		if(is_null($task)) {
			$this->redirect(Yii::app()->homeUrl);
		}

		if(Yii::app()->request->urlReferrer) {
			$this->redirect(Yii::app()->request->urlReferrer);
		}
		else {
			$returnURL = isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('task/view', 'id'=>$task->id,);
			$this->redirect($returnURL);
		}
	}
}
