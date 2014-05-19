<?php	// Контроллер	class ProfileController extends Controller	{				// Папка вьюх		protected $_viewsFolder	= "profile";						// Экшн по умолчанию		public $defaultAction = "Profile"; 				// Перед выполнением любого действия, проверяем залогинен ли пользователь		public function beforeAction()		{			// Проверяем, залогинен ли пользователь			if (!User::loggedIn()) {				// Если не залогинен - редирект на вход				$this->redirect(array(					"controller" => "index",				));			}		}				public function actionProfile()		{			addJs("profile");			// Включаем js						// Получаем пользователя (и обновляем данные в БД)			$User = User::fromSession(true, true);									$this->render("profile", array(				"User" 			=> $User,			));						// Просмотр новостей (обновление ID последней просмотренной новости в FEED – $User->id_last_seen_news)			$User->updateNewsCount();		}						public function actionEdit()		{			addJs("profile_edit, switchery, ng-switchery");			// Включаем js						// Подключаем сторонний JS			addJs("//vk.com/js/api/openapi.js", true);						// Получаем пользователя			$User = User::fromSession();									$this->render("edit", array(				"user_data"	=> $User->dbData(),			));		}				public function actionSubscribers()		{			addJs("profile_subr");						// Получаем пользователя			$User = User::fromSession();						$this->render("subscribers", array(				"User"	=> $User,			));		}						// Сохранение		public function actionAjaxSave()		{			// Конвертируем из строк "true"/"false"			trueFalseConvert($_POST);						// Получаем пользователя			$User = User::fromSession();						$User->dbSave($_POST);						$User->toSession();				}				// Проверка логина		public function actionAjaxCheckLogin()		{			// Если пользователь найден, то логин уже занят			echo User::loginAvailability($_POST['login']);		}				// Показываем новости подписки		public function actionAjaxSubNews()		{			// Находим пользователя			$User = User::findById($_POST["id_user"]);						// Получаем ID последней новости и обновляем last_seen			$id_last_seen_news = Feed::lastId();						echo '<h2 class="center-t text-white" style="margin-bottom: 20px"><div style="background-image: url('.$User->avatar.')" class="ava-60 '.($User->stretch ? "stretch" : "").'"></div>';			echo "Новости ".$User->login.": </h2>";			Feed::displayNews($User, $_POST["id_last_seen"], true);						// Обновляем значения LAST_SEEN			User::setConnection(User::fromSession(false)->id);						// Получаем подписку			$Subscription = Subscription::find(array(				"condition"	=> "id_user = '".$_POST['id_user']."'",			));						// Обновляем ее			$Subscription->id_last_seen_news = $id_last_seen_news;						// И сохраняем			$Subscription->save();		}			}