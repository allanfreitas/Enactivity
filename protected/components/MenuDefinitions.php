<?php
/**
 * Class file for MenuDefinitions
 * @author Ajay Sharma
 */

/**
 * Used to define the menus in Poncla
 * @see CMenu
 * @author Ajay Sharma
 */
class MenuDefinitions extends CComponent {

	public static function site() {
		return null;
	}
	
	/**
	 * @return array of menu items for admins
	 */
	public static function adminMenu() {
		return array(
			array(
				'label'=>'Create Group', 
				'url'=>array('group/create'),
				'linkOptions'=>array('id'=>'group_create_menu_item'), 
				'visible'=>Yii::app()->user->isAdmin
			),
			array(
				'label'=>'Manage Groups', 
				'url'=>array('group/admin'), 
				'linkOptions'=>array('id'=>'group_manage_menu_item'),
				'visible'=>Yii::app()->user->isAdmin,
			),
			array(
				'label'=>'Manage Goals', 
				'url'=>array('goal/admin'), 
				'linkOptions'=>array('id'=>'goal_manage_menu_item'),
				'visible'=>Yii::app()->user->isAdmin,
			),
			array(
				'label'=>'Manage Users', 
				'url'=>array('user/admin'),
				'linkOptions'=>array('id'=>'user_admin_menu_item'), 
				'visible'=>Yii::app()->user->isAdmin
			),
			array(
				'label'=>'Manage Events', 
				'url'=>array('event/admin'), 
				'linkOptions'=>array('id'=>'event_admin_menu_item'),
				'visible'=>Yii::app()->user->isAdmin,
			),
			array(
				'label'=>'Manage EventBanter', 
				'url'=>array('eventbanter/admin'), 
				'linkOptions'=>array('id'=>'event_manage_menu_item'),
				'visible'=>Yii::app()->user->isAdmin,
			),
			array(
				'label'=>'Manage GroupBanter', 
				'url'=>array('groupbanter/admin'), 
				'linkOptions'=>array('id'=>'group_manage_menu_item'),
				'visible'=>Yii::app()->user->isAdmin,
			),
		);
	} 
	
	/**
	 * @param Event $model Event currently under scrutiny
	 * @return array of menu items for events
	 */
	public static function event() {
		return array(
			array(
				'label'=>'Create an Event', 
				'url'=>array('event/create'),
				'linkOptions'=>array('id'=>'event_create_menu_item'),
			),
			array(
				'label'=>'Calendar',
				'url'=>array('event/calendar', 
					'month' => date('m'), 
					'year' => date('Y'),
				),
				'linkOptions'=>array('id'=>'event_calendar_menu_item'),
			),	
		);
	}
	
	/**
	 * @return array of menu items for EventBanters
	 */
	public static function eventBanter() {
		return null;
	}
	
	/**
	 * @param Event $model Event currently under scrutiny
	 * @return array of menu items for events
	 */
	public static function eventMenu($model = null) {
		if(isset($model)) {
			$menu[] = array(
				'label'=>'Update This Event', 
				'url'=>array('event/update', 'id'=>$model->id),
				'linkOptions'=>array('id'=>'event_update_menu_item'),
			);
			$menu[] = array(
				'label'=>'Delete This Event', 
				'url'=>'#', 
				'linkOptions'=>array('submit'=>array(
					'event/delete',
					'id'=>$model->id,
				),
				'confirm'=>'Are you sure you want to delete this item?',
					'csrf' => true,
					'id'=>'event_delete_menu_item',
				),
			);
		}
		
		return $menu;
	}
	
	public static function feed() {
		return null;
	}
	
	public static function goal() {
		return array(
			array(
				'label'=>'Create a Goal', 
				'url'=>array('goal/create'),
				'linkOptions'=>array('id'=>'goal_create_menu_item'),
			),
		);
	}
	
	/**
	 * @param Goal $model Goal currently under scrutiny
	 * @return array of menu items for goals
	 */
	public static function goalMenu($model = null) {
		if(isset($model)) {
			$menu[] = array(
				'label'=>'Update This Goal', 
				'url'=>array('goal/update', 'id'=>$model->id),
				'linkOptions'=>array('id'=>'goal_update_menu_item'),
			);
			
			// only show when there is no owner
			if($model->ownerId == null) {
				$menu[] = array(
					'label'=>'Take Ownership', 
					'url'=>array('goal/own', 'id'=>$model->id),
					'linkOptions'=>array(
						'submit'=>array(
							'goal/own',
							'id'=>$model->id,
						),
						'csrf' => true,
						'id'=>'goal_owner_menu_item' . $model->id,
					),
				);
			}
			elseif($model->ownerId == Yii::app()->user->id) {
				$menu[] = array(
					'label'=>'Give Up Ownership', 
					'url'=>array('goal/unown', 'id'=>$model->id),
					'linkOptions'=>array(
						'submit'=>array(
							'goal/unown',
							'id'=>$model->id,
						),
						'csrf' => true,
						'id'=>'goal_owner_menu_item' . $model->id,
					),
				);
			}
			
			if($model->ownerId == null 
			|| $model->ownerId == Yii::app()->user->id) {
				if($model->isCompleted) {
					$menu[] = array(
						'label'=>'Mark Not Done', 
						'url'=>array('goal/uncomplete', 'id'=>$model->id),
						'linkOptions'=>array(
							'submit'=>array(
								'goal/uncomplete',
								'id'=>$model->id,
							),
							'csrf' => true,
							'id'=>'goal_uncomplete_menu_item' . $model->id,
						),
					);
				}
				else {
					$menu[] = array(
						'label'=>'Mark Done', 
						'url'=>array('goal/complete', 'id'=>$model->id),
						'linkOptions'=>array(
							'submit'=>array(
								'goal/complete',
								'id'=>$model->id,
							),
							'csrf' => true,
							'id'=>'goal_complete_menu_item' . $model->id,
						),
					);
				}
				
				if($model->isTrash) {
					$menu[] = array(
						'label'=>'UnTrash', 
						'url'=>array('goal/untrash', 'id'=>$model->id),
						'linkOptions'=>array(
							'submit'=>array(
								'goal/untrash',
								'id'=>$model->id,
							),
							'csrf' => true,
							'id'=>'goal_untrash_menu_item' . $model->id,
						),
					);
				}
				else {
					$menu[] = array(
						'label'=>'Trash', 
						'url'=>array('goal/trash', 'id'=>$model->id),
						'linkOptions'=>array(
							'submit'=>array(
								'goal/trash',
								'id'=>$model->id,
							),
							'confirm'=>'Are you sure you want to trash this item?',
							'csrf' => true,
							'id'=>'goal_trash_menu_item' . $model->id,
						),
					);
				}	
			}

		}
		
		return $menu;
	}
	
	/**
	 * @param Group $model Group currently under scrutiny
	 * @return array of menu items for groups
	 */
	public static function group() {
		return array(
			array(
				'label'=>'Invite a User', 
				'url'=>array('group/invite'),
				'linkOptions'=>array('id'=>'group_invite_menu_item'),
			),
		);
	}
	
/**
	 * @param Group $model Group currently under scrutiny
	 * @return array of menu items for groups
	 */
	public static function groupMenu($model = null) {
		
		if(isset($model->id)) {
			$menu[] = array(
				'label'=>'Update This Group', 
				'url'=>array('group/updateprofile','id'=>$model->id),
				'linkOptions'=>array('id'=>'group_profile_menu_item'),
			);
		}
		
		return $menu;
	}
	
	/**
	 * @param GroupBanter $model GroupBanter current under scrutiny
	 * @return array of menu items for GroupBanters
	 */
	public static function groupBanter() {
		return null;
	}
	
	/**
	 * @param GroupBanter $model GroupBanter current under scrutiny
	 * @return array of menu items for GroupBanters
	 */
	public static function groupBanterMenu($model = null) {
		$menu = null;
		
		if(isset($model)
			&& Yii::app()->user->id == $model->creatorId) {
				
			$menu = array();
			$menu[] = array(
				'label'=>'Update', 
				'url'=>array('groupbanter/update', 'id'=>$model->id),
				'visible'=>Yii::app()->user->id == $model->creatorId,
			);
			
			$menu[] = array(
				'label'=>'Delete', 
				'url'=>'#', 
				'linkOptions'=>array('submit'=>array(
					'groupbanter/delete',
					'id'=>$model->id,
				),
				'confirm'=>'Are you sure you want to delete this item?',
					'csrf' => true,
					'id'=>'groupbanter_delete_menu_item',
				),
				'visible'=>Yii::app()->user->id == $model->creatorId,
			);
		}
		
		return $menu;
	}
	
	public static function task() {
		return array(
			array(
				'label'=>'Create a Task', 
				'url'=>array('task/create'),
				'linkOptions'=>array('id'=>'task_create_menu_item'),
			),
		);
	}
	
	/**
	 * @param Task $model Task currently under scrutiny
	 * @return array of menu items for tasks
	 */
	public static function taskMenu($model = null) {
		if(isset($model)) {
			$menu[] = array(
				'label'=>'Update This Task', 
				'url'=>array('task/update', 'id'=>$model->id),
				'linkOptions'=>array('id'=>'task_update_menu_item'),
			);
			
			// 'participate' button
			if($model->isUserParticipating()) {
				$menu[] = array(
					'label'=>'Stop participating', 
					'url'=>array('task/unparticipate', 'id'=>$model->id),
					'linkOptions'=>array(
						'submit'=>array(
							'task/unparticipate',
							'id'=>$model->id,
						),
						'csrf' => true,
						'id'=>'task_unparticipate_menu_item' . $model->id,
					),
				);
			}
			else {
				$menu[] = array(
					'label'=>'Participate', 
					'url'=>array('task/participate', 'id'=>$model->id),
					'linkOptions'=>array(
						'submit'=>array(
							'task/participate',
							'id'=>$model->id,
						),
						'csrf' => true,
						'id'=>'task_unparticipate_menu_item' . $model->id,
					),
				);
			}
			
//			// only show when there is no owner
//			if($model->ownerId == null) {
//				$menu[] = array(
//					'label'=>'Take Ownership', 
//					'url'=>array('task/own', 'id'=>$model->id),
//					'linkOptions'=>array(
//						'submit'=>array(
//							'task/own',
//							'id'=>$model->id,
//						),
//						'csrf' => true,
//						'id'=>'task_owner_menu_item' . $model->id,
//					),
//				);
//			}
//			elseif($model->ownerId == Yii::app()->user->id) {
//				$menu[] = array(
//					'label'=>'Give Up Ownership', 
//					'url'=>array('task/unown', 'id'=>$model->id),
//					'linkOptions'=>array(
//						'submit'=>array(
//							'task/unown',
//							'id'=>$model->id,
//						),
//						'csrf' => true,
//						'id'=>'task_owner_menu_item' . $model->id,
//					),
//				);
//			}
//			
//			if($model->ownerId == null 
//			|| $model->ownerId == Yii::app()->user->id) {
//				if($model->isCompleted) {
//					$menu[] = array(
//						'label'=>'Mark Not Done', 
//						'url'=>array('task/uncomplete', 'id'=>$model->id),
//						'linkOptions'=>array(
//							'submit'=>array(
//								'task/uncomplete',
//								'id'=>$model->id,
//							),
//							'csrf' => true,
//							'id'=>'task_uncomplete_menu_item' . $model->id,
//						),
//					);
//				}
//				else {
//					$menu[] = array(
//						'label'=>'Mark Done', 
//						'url'=>array('task/complete', 'id'=>$model->id),
//						'linkOptions'=>array(
//							'submit'=>array(
//								'task/complete',
//								'id'=>$model->id,
//							),
//							'csrf' => true,
//							'id'=>'task_complete_menu_item' . $model->id,
//						),
//					);
//				}
				
				if($model->isTrash) {
					$menu[] = array(
						'label'=>'UnTrash', 
						'url'=>array('task/untrash', 'id'=>$model->id),
						'linkOptions'=>array(
							'submit'=>array(
								'task/untrash',
								'id'=>$model->id,
							),
							'csrf' => true,
							'id'=>'task_untrash_menu_item' . $model->id,
						),
					);
				}
				else {
					$menu[] = array(
						'label'=>'Trash', 
						'url'=>array('task/trash', 'id'=>$model->id),
						'linkOptions'=>array(
							'submit'=>array(
								'task/trash',
								'id'=>$model->id,
							),
							'confirm'=>'Are you sure you want to trash this item?',
							'csrf' => true,
							'id'=>'task_trash_menu_item' . $model->id,
						),
					);
				}	
			}

//		}
		
		return $menu;
	}
	
	/**
	 * @param User $model User current under scrutiny
	 * @return array of menu items for user controller
	 */
	public static function user() {
		return null;
	}
	
	/**
	 * @param User $model User current under scrutiny
	 * @return array of menu items for user controller
	 */
	public static function userMenu($model = null) {
		$menu = null;
		
		if(isset($model)
			&& Yii::app()->user->id == $model->id) {
				
			$menu = array();
			$menu[] = array('label'=>'Update Profile', 
				'url'=>array('user/update', 'id'=>$model->id),
				'linkOptions'=>array('id'=>'user_update_menu_item'), 
				'visible'=>Yii::app()->user->id == $model->id,
			);
			$menu[] = array('label'=>'Update Password', 
				'url'=>array('user/updatepassword', 'id'=>$model->id),
				'linkOptions'=>array('id'=>'user_update_menu_item'), 
				'visible'=>Yii::app()->user->id == $model->id,
			);
		}
		
		return $menu;
	}
	
	/**
	 * @return array main menu items
	 */
	public static function globalMenu() {
		return array(
			array(
				'label'=>'Home:Beta', 
				'url'=>array('/site/index'),
				'visible'=>Yii::app()->user->isGuest
			),
			array(
				'label'=>'Home:Beta', 
				'url'=>array('/feed/index'),
				'visible'=>!Yii::app()->user->isGuest
			),
			array(
				'label'=>'What\'s Next', 
				'url'=>array('/goal/whatsnext'),
				'visible'=>!Yii::app()->user->isGuest
			),
			array(
				'label'=>'Goals', 
				'url'=>array('/goal/index'), 
				'visible'=>!Yii::app()->user->isGuest
			),
			array(
				'label'=>'Events', 
				'url'=>array('/event/index'), 
				'visible'=>!Yii::app()->user->isGuest
			),
			array(
				'label'=>'Banter', 
				'url'=>array('/groupbanter/index'), 
				'visible'=>!Yii::app()->user->isGuest
			),
			array(
				'label'=>'Groups', 
				'url'=>array('/group/index'), 
				'visible'=>!Yii::app()->user->isGuest
			),			
			array(
				'label'=>'Settings', 
				'url'=>array('/site/settings'), 
				'visible'=>!Yii::app()->user->isGuest
			),
			array(
				'label'=>'Login', 
				'url'=>array('/site/login'), 
				'visible'=>Yii::app()->user->isGuest
			),
			array(
				'label'=>'Logout ('.Yii::app()->user->model->firstName.')', 
				'url'=>array('/site/logout'), 
				'visible'=>!Yii::app()->user->isGuest
			),
			array(
				'label'=>'Admin', 
				'url'=>array('/group/create'), 
				'visible'=>Yii::app()->user->isAdmin
			),
		);
	}
}