<?php	// Контроллер пользователей	class UserController extends Controller	{		public $defaultAction = "UserPage";				// Папка вьюх		protected $_viewsFolder	= "user";				// Отображение страницы пользователя		public function actionUserPage()		{			addJs("fancyInput, user");	// Включаем JS			addCss("fancyInput");	// Включаем CSS									// Получаем пользователя			$User = User::findByLogin($_GET["user"]);									// Сохраняем ID пользователя, страница которого просматривается, в сессию			$_SESSION["viewing_id"] = $User->id;						// Получаем случайное прилагательное в placeholder						$default_adjective = DefaultAdjective::randomAdjective($User->gender);						// Своя страница			$own_page = User::fromSession(false)->id == $User->id;									// Если просматривает сам себя			if ($own_page) {				// То отображаем все прилагательные				$Adjectives = Adjective::findAll();								// Подсчитываем кол-во скрытых прилагательных				$hidden_count = Adjective::findAll(array(					"condition"	=> "hidden=1"				), true); 			} else {				// Иначе не отображаем скрытые				$Adjectives = Adjective::findAll(array(					"condition" => "hidden=0"				));								// Если пользователь залогинен, то проверяем подписан ли залогиненынй пользователь на пользователя, чья страница просматривается				if (User::loggedIn()) {					$subscribed = User::fromSession()->subscribedTo($User->id);				}			}						$this->render("userpage", array(				"User"				=> $User,				"Adjectives"		=> $Adjectives,				"hidden_count"		=> $hidden_count,				"own_page"			=> $own_page,				"default_adjective"	=> $default_adjective,				"subscribed"		=> $subscribed,			));		}				// Точно тест		public function actionTest2()		{			$User = User::findById(1);						$Vote = new Vote(array(				"id_adjective"	=> 2,				"ip"			=> "192.168.0.1",			));						$this->render("test2", array(				"Vote" => $Vote,			));		}				// Функция для добавления мысли о человеке		public function actionAjaxAddThought()		{			// Инициализируем сессию для того, чтобы получить ID текущего пользователя, чья страница просматривается			$User = User::findById($_SESSION["viewing_id"]);						$Adjective = $User->addAdjective($_POST["adjective"], true);														}				// Функция голосования		public function actionAjaxVote()		{			// Инициализируем сессию для того, чтобы получить ID текущего пользователя, чья страница просматривается			$User = User::findById($_SESSION["viewing_id"]);						// Ищем прилагательное			$Adjective = Adjective::findById($_POST["id"]);						// Добавляем голос			$Adjective->addVote(true, $_POST["type"]);		}				// Скрываем прилагательное		public function actionAjaxHide()		{			// Получаем пользователя из сессии			$User = User::fromSession();						// Залогиненный юзер должен быть равен просматриваемому (иначе кто-то другой может будет послать запрос на HIDE прилагательного)			if ($_SESSION["viewing_id"] == $User->id) {				$Adjective = Adjective::findById($_POST["id"]);				$Adjective->hidden = !$Adjective->hidden;				$Adjective->save();			}		}				// Подписываемся		public function actionAjaxSubscribe()		{			// Получаем пользователя из сессии			$User = User::fromSession();			// Подписываемся на пользователя			$User->subscribeTo($_POST["id_user"]);		}				// Получаем список друзей		public function actionAjaxGetFriends()		{			// Массив с ID всех друзей из ВК			/*			foreach ($_POST["ids"] as $id_friend) {				echo $id_friend."\n";			}*/						$condition = implode(",", $_POST["ids"]);						// echo $condition;						$Friends = User::findAll(array(				"condition"	=> "id_vk in ($condition)"			));						// print_r($Friends);						echo json_encode($Friends);					}	}