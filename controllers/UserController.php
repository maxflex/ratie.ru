<?php	// Контроллер пользователей	class UserController extends Controller	{		public $defaultAction = "UserPage";				// Папка вьюх		protected $_viewsFolder	= "user";				// Перед выполнением любого действия, проверяем не был ли изменен пароль		public function beforeAction()		{			// Проверяем, залогинен ли пользователь			if (User::loggedIn()) {				// Проверяем не изменился ли пароль (если сменился на одном девайсе, то на всех других выходим)				if (User::fromSession(false, true)->token != mb_strimwidth($_COOKIE["ratie_token"], 0, 32)) {					$this->redirect("logout");				}			} else {				// Пытаемся войти по token в КУКАХ				User::rememberMeLogin(false);			}		}				// Отображение страницы пользователя		public function actionUserPage()		{				// Получаем залогиненного пользователя			$LoggedUser = User::fromSession(false);						// Получаем пользователя, страница которого просматривается!!! (если не установлено в $_GET – берем залогиненногоп пользователя)			$ViewedUser = User::findByLogin( (isset($_GET["user"]) ? $_GET["user"] : $LoggedUser->login) );																	// Своя страница?			$own_page = $LoggedUser->id == $ViewedUser->id;									/******* CТРАНИЦА КОММЕНТАРИЕВ ********/						if (isset($_GET["comment"])) {								// Получаем ID прилагательного				$id_adjective = (int)$_GET["comment"];								// Если установлен ID прилагательного, ищем его и получаем комментарии				if ($id_adjective) {										$this->addJs("user_comments"); // Включаем js										// Находим комментируемое прилагательное					$Adjective = Adjective::findById($id_adjective);										// Устанавливаем заголовок					$this->htmlTitle("Комментарии к «{$Adjective->adjective}» | ".$ViewedUser->getName());										// Получаем комментарии					$Comments = $Adjective->getComments();				} else {					// Если ID прилагательного - ноль, то не это личные сообщения, не ищем прилагательные					$Adjective = false;										// Добавляем JS для автоапдейта сообщений					$this->addJs("https://cdn.firebase.com/js/client/1.0.15/firebase.js", true);					$this->addJs("https://cdn.firebase.com/libs/angularfire/0.7.1/angularfire.min.js", true);					// Подключаем сторонний JS					$this->addJs("//vk.com/js/api/openapi.js", true);										$this->addJs("user_messages"); // Включаем JS										// Устанавливаем заголовок					$this->htmlTitle("Открытая беседа | ".$ViewedUser->getName());										$Comments = Comment::findAll(array(						"condition" => "id_adjective=0",					));										// Если комментарии найдены					if ($Comments) {						$have_messages = true;					}				}																			// Если пользователь залогинен				if (User::loggedIn()) {										// Сообщения после регистрации о публичности (код сообщения для ангуляра - 1)					if (!$LoggedUser->intro["comment_public"] && !$LoggedUser->anonymous) {						$_intro_for_anonymous_or_public = 1;					//	$LoggedUser->intro["comment_public"] = 1;					//	$LoggedUser->save();					}													// Сообщения после регистрации об анонимности (код сообщения для ангуляра - 2)					if (!$LoggedUser->intro["comment_anonymous"] && $LoggedUser->anonymous) {						$_intro_for_anonymous_or_public = 2;					//	$LoggedUser->intro["comment_anonymous"] = 1;					//	$LoggedUser->save();					}				}								// Если пользователь залогинен и не анонимен + не своя же страница (на своей же странице нельзя быть анонимным)				if (User::loggedIn() && (!User::fromSession(false)->anonymous ||  (User::fromSession(false)->anonymous && $own_page))) {					// Пользователь, который будет передаваться на страницу рендера комментариев					$PassUser = (object)[						"id"	  => $LoggedUser->id,						"login"	  => $LoggedUser->login,						"avatar"  => $LoggedUser->avatar,						"stretch" => $LoggedUser->stretch,					];				} else {					$PassUser = (object)[						"id"	  => 0,						"login"	  => "",						"avatar"  => "img/profile/noava.png",						"stretch" => false,					];									}								// Если пользователь залогинен и не совя страница -- проверяем подписку				if (User::loggedIn() && !$own_page) {					// Проверяем подписан ли залогиненынй пользователь на пользователя, чья страница просматривается					$subscribed = User::fromSession()->subscribedTo($ViewedUser->id);				}								$this->render("comments", array(					"own_page"		=> $own_page,					"User"			=> $ViewedUser,					"LoggedUser"	=> $PassUser,					"Adjective"		=> $Adjective,					"Comments"		=> $Comments,					"subscribed"	=> $subscribed,					"id_viewing"	=> $ViewedUser->id,					"id_adjective"	=> $id_adjective,					"_intro_for_anonymous_or_public" => $_intro_for_anonymous_or_public,					"have_messages"	=> $have_messages,				));								// Если это личные сообщения, то обновляем количество просмотренных после рендера				if (!$id_adjective && $LoggedUser) {					// Просматриваем сообщения					$LoggedUser->updateNewMessagesCount();				}			} else {						/******* CТРАНИЦА ПОЛЬЗОВАТЕЛЯ ********/							$this->addJs("fancyInput, user");	// Включаем JS				$this->addCss("fancyInput");		// Включаем CSS								// Получаем случайное прилагательное в placeholder							$default_adjective = DefaultAdjective::randomAdjective($ViewedUser->gender);								// Если просматривает сам себя				if ($own_page) {					// Устанавливаем заголовок					$this->htmlTitle("Мои мнения | ".$LoggedUser->getName());										// Подключаем сторонний JS					$this->addJs("//vk.com/js/api/openapi.js", true);										// Подключаем дополнительный JS					$this->addJs("user_own");										// Если пользователю еще не было предложено подписаться на друзей из ВК					if (!$LoggedUser->intro["friends"]) {						// Добавлеям JS просмотра друзей						$this->addJs("friends");												// Показать окно друзей на Ratie						$show_friends = true;												// Пользователю уже были предложены друзья из ВК, больше не отображать						$LoggedUser->intro["friends"] = 1;						$LoggedUser->save();					}										// То отображаем все прилагательные					$Adjectives = Adjective::findAll();										// Если есть прилагательные и пользователь не видел ИНТРО о том, что их можно скрывать					if ($Adjectives && !$LoggedUser->intro["adj_hide"]) {						$_intro_adj_hide = 1;						$LoggedUser->intro["adj_hide"] = 1;						$LoggedUser->save();					}										// Подсчитываем кол-во скрытых прилагательных					$hidden_count = Adjective::findAll(array(						"condition"	=> "hidden=1"					), true); 				} else {					// Устанавливаем заголовок					$this->htmlTitle($ViewedUser->getName(), true);										// Иначе не отображаем скрытые					$Adjectives = Adjective::findAll(array(						"condition" => "hidden=0"					));										// Если пользователь залогинен					if (User::loggedIn()) {						// Проверяем подписан ли залогиненынй пользователь на пользователя, чья страница просматривается						$subscribed = User::fromSession()->subscribedTo($ViewedUser->id);												// Сообщения после регистрации о публичности (код сообщения для ангуляра - 1)						if (!$LoggedUser->intro["public"] && !$LoggedUser->anonymous) {							$_intro_for_anonymous_or_public = 1;						//	$LoggedUser->intro["public"] = 1;						//	$LoggedUser->save();						}															// Сообщения после регистрации об анонимности (код сообщения для ангуляра - 2)						if (!$LoggedUser->intro["anonymous"] && $LoggedUser->anonymous) {							$_intro_for_anonymous_or_public = 2;						//	$LoggedUser->intro["anonymous"] = 1;						//	$LoggedUser->save();						}					}				}														$this->render("userpage", array(					"User"				=> $ViewedUser,					"Adjectives"		=> $Adjectives,					"hidden_count"		=> $hidden_count,					"own_page"			=> $own_page,					"default_adjective"	=> $default_adjective,					"subscribed"		=> $subscribed,					"id_viewing"		=> $ViewedUser->id,					"show_friends"		=> $show_friends,					"_intro_for_anonymous_or_public" => $_intro_for_anonymous_or_public,					"_intro_adj_hide"	=> $_intro_adj_hide,				));			}		}				// Точно тест		public function actionTest2()		{			$User = User::findById(1);						$Vote = new Vote(array(				"id_adjective"	=> 2,				"ip"			=> "192.168.0.1",			));						$this->render("test2", array(				"Vote" => $Vote,			));		}						// Функция для добавления мысли о человеке		public function actionAjaxAddThought()		{						// Инициализируем сессию для того, чтобы получить ID текущего пользователя, чья страница просматривается			$User = User::findById($_POST["id_viewing"]);						$Adjective = $User->addAdjective($_POST["adjective"], true);												}				// Функция голосования		public function actionAjaxVote()		{			// Инициализируем сессию для того, чтобы получить ID текущего пользователя, чья страница просматривается			$User = User::findById($_POST["id_viewing"]);						// Ищем прилагательное			$Adjective = Adjective::findById($_POST["id"]);						// Добавляем голос			$Adjective->addVote(true, $_POST["type"]);		}				// Скрываем прилагательное		public function actionAjaxHide()		{			// Получаем пользователя из сессии			$User = User::fromSession();						// Залогиненный юзер должен быть равен просматриваемому (иначе кто-то другой может будет послать запрос на HIDE прилагательного)			if ($_POST["id_viewing"] == $User->id) {				$Adjective = Adjective::findById($_POST["id"]);				$Adjective->hidden = !$Adjective->hidden;				$Adjective->save();			}		}				// Подписываемся		public function actionAjaxSubscribe()		{			// Получаем пользователя из сессии			$User = User::fromSession();			// Подписываемся на пользователя			$User->subscribeTo($_POST["id_user"]);		}				// Получаем список друзей		public function actionAjaxGetFriends()		{			// Массив с ID всех друзей из ВК			/*			foreach ($_POST["ids"] as $id_friend) {				echo $id_friend."\n";			}*/						$condition = implode(",", $_POST["ids"]);						// echo $condition;						$Friends = User::findAll(array(				"condition"	=> "id_vk in ($condition)"			));						// print_r($Friends);						echo json_encode($Friends);					}				// Оставляем комментарий		public function actionAjaxLeaveComment()		{			// Проверяем есть ли комментарий			if (!empty($_POST["comment"])) {				// Инициализируем сессию для того, чтобы получить ID текущего пользователя, чья страница просматривается				$User = User::findById($_POST["id_viewing"]);								// Получаем залогиненного пользователя				$LoggedUser = User::fromSession(false);								/***** Проверяем лимит комментариев подряд от одного пользователя *****/				// Получем последние комментарии				$LastComments = Comment::findAll(array(					"condition" => "id_adjective=".(int)$_POST["id_adjective"],					"order"		=> "id DESC",					"limit"		=> settings()->COMMENT_LIMIT,				));								// Подсчитываем количество от текущего пользователя				if ($LastComments) {					$count_limit = 0;					foreach ($LastComments as $Comment) {						if ($Comment->ip != realIp()) {							break;						} else {							$count_limit++;						}					}										// Если превышен лимит (для комментариев и сообщений)					if ( ($_POST["id_adjective"]  & ($count_limit >= settings()->COMMENT_LIMIT)) || 						 (!$_POST["id_adjective"] & ($count_limit >= settings()->MESSAGE_LIMIT)) ) {						exit("Слишком много комментариев подряд!");										}				}				/************* КОНЕЦ ПРОВЕРКИ МЫСЛЕЙ ПОДРЯД ***************/								$Comment = new Comment(array(					"comment"		=> mysql_escape_string(trim($_POST["comment"])),					"id_adjective"	=> (int)$_POST["id_adjective"],					"id_user"		=> ($LoggedUser->anonymous && ($LoggedUser->id != $User->id) ? 0 : $LoggedUser->id),					"ip"			=> realIp(),					"time"			=> now(),				));																// Сохраняем комментарий				$Comment->save();								// Возвращаем ID нового комментария				echo $Comment->id;										// Увеличиваем кол-во комментариев				$User->comments++;				$User->save("comments");								// Добавляем новость, если это не комментарий самому себе				if ($LoggedUser->id != $User->id) {					// Если id прилагательного не равен нулю, то это новость о комментарии к прилагательному					if ($Comment->id_adjective) {						Feed::create(array(							"id_adjective"	=> $Comment->id_adjective,							"id_user"		=> ($LoggedUser->anonymous ? 0 : $LoggedUser->id),							"id_news_type"	=> NewsType::COMMENT,						));																								// Оставляем новость автору мнения, что его оценку прокомментили						$id_user_first_commented = $Comment->Adjective()->getFirstVote()->id_user;												echo "ID_USER_COMMENTED=".$id_user_first_commented;												// Если изначально прокомментил не аноним, то нужно оставить новость этому юзеру						if ($id_user_first_commented) {							// Подключаемся к БД пользователя, который изначально прокомментил							User::setConnection($id_user_first_commented);														// Оставляем новость							Feed::create(array(								"id_adjective"	=> $Comment->id_adjective,								"id_user"		=> $LoggedUser->id,								"id_news_type"	=> NewsType::NEW_COMMENT,								"additional"	=> $User->id,							));						}												} /* НОВОСТЬ О ЛИЧНОМ СООБЩЕНИИ БОЛЬШЕ НЕ НУЖНА  else {					// Иначе это новость о личном сообщении						Feed::create(array(							"id_adjective"	=> 0,							"id_user"		=> ($LoggedUser->anonymous ? 0 : $LoggedUser->id),							"id_news_type"	=> NewsType::NEW_MESSAGE,						));						} */															// Сообщения после регистрации о публичности комментария					if (!$LoggedUser->intro["comment_public"] && !$LoggedUser->anonymous) {						$LoggedUser->intro["comment_public"] = 1;						$LoggedUser->save();					}													// Сообщения после регистрации об анонимности комментария					if (!$LoggedUser->intro["comment_anonymous"] && $LoggedUser->anonymous) {						$LoggedUser->intro["comment_anonymous"] = 1;						$LoggedUser->save();					}				}								}		}				/*		 * Просто получает значения из сессии. Для тестов		 */		public function actionAjaxGetSession()		{			echo json_encode($_SESSION);		}				/*		 * Если пользователь нажал кнопку «Предложить оценить» и успешно поделился записью в ВК		 */		public function actionAjaxAddShare()		{			// Получаем залогиненного пользователя			$LoggedUser = User::fromSession(false);						// Увеличеваем кол-во shares			$LoggedUser->shares++;						// Сохраняем			$LoggedUser->save("shares");		}				/*		 * Обновить id последнего просмотреннго сообщения		 */		public function actionAjaxUpdateNewMessagesCount()		{			// Получаем залогиненного пользователя и обновляем сразу			User::fromSession(false)->updateNewMessagesCount();		}	}