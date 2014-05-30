<?php
	class User extends Model
	{
	
		/*====================================== ПЕРЕМЕННЫЕ И КОНСТАНТЫ ======================================*/

		protected $_serialized = array("social", "intro"); // поле social в БД сериализовано

		public static $mysql_table	= "users";
		
		// При создании нового пользователя устанавливать соединение с его базой данных (C БД USER_[id_user], подключение к старому теряется)
		public static $initialize_on_new = true;
		
		// Переменная с прилагательными
		public $Adjectives = NULL;
		
		
		/*====================================== СИСТЕМНЫЕ ФУНКЦИИ ======================================*/

		
		public function __construct($array)
		{
			parent::__construct($array);
			
			// Инициализируем подключение к БД пользователя
			if (!$this->isNewRecord) {
				// см. public static $initialize_on_new
				if (static::$initialize_on_new) {
					initUserConnection($this->id);
				}
				
				// Находим все прилагательные пользователя
				$this->Adjectives = Adjective::findAll();
			}
		}
		
		/*====================================== СТАТИЧЕСКИЕ ФУНКЦИИ ======================================*/
		
		/* 
		 * Находим пользователя по логину
		 * $login – логин для поиска
		 * $default_id - подставлять ли пользователя по умолчанию, если не нашелся по логину
		 */
		public static function findByLogin($login, $default_id = 1)
		{
			// Обрезаем пробелы
			$login = trim($login);
			
			// Находим пользователя
			$User = self::find(array(
				"condition"	=> "login='{$login}'",
			));
			
			// Если пользователь не нашелся (и нужно подставлять по умолчанию
			if (!$User && $default_id) {
				// То пользователя по умолчанию (самого первого)
				$User = self::findById($default_id);
			}
			
			return $User;
		}

		
		/*
		 * Установить соединение с БД пользователя
		 * $id_user – ID пользователя, с чей бд устанавливаем соединение
		 */
		public static function setConnection($id_user)
		{
			initUserConnection($id_user);
		}
		
		/*
		 * Пользователь из сессии
		 * @boolean $init – инициализировать ли соединение с БД пользователя
		 * @boolean $update – обновлять данные из БД
		 */
		public static function fromSession($init = true, $upadte = false)
		{
			// Если обновить данные из БД, то загружаем пользователя
			if ($upadte) {
				$User = User::findById($_SESSION["user"]->id);
				$User->toSession();
			} else {
				// Получаем пользователя из СЕССИИ
				$User = $_SESSION["user"];
			}
			
			
			// Устанавливаем соединение с БД юзера
			if ($init) {
				initUserConnection($User->id);
			}
						
			// Возвращаем пользователя
			return $User;
		}
		
		/*
		 * Проверяем, залогинен ли пользователь
		 */
		public static function loggedIn()
		{
			return isset($_SESSION["user"]);
		}
		
		/*
		 *  Проверка логина на занятость
		 * @string $login – логин, который проверяется на занятость
		 * return – true,  когда логин уже занят
		 */
		public static function loginAvailability($login)
		{
			// Пытаемся найти пользователя с таким логином и ID не равен текущему пользователю
			$User = User::find(array(
				"condition" => "login='{$login}' AND id!=".User::fromSession()->id,
			));
						
			// Если пользователь найден, то логин уже занят
			return ($User ? true : false);
		}
				
		/*====================================== ФУНКЦИИ КЛАССА ======================================*/
		
		/*
		 * Если логин уже существует, то логин приводится к форме user3213123
		 * (например, если пользователь ID: 1, LOGIN: MAXFLEX существует и регистрируется новый пользователь ID: n
		 * с таким же логином, то логин нового пользователя приводится к форме user{n}
		 * ПРОВЕРЯТЬ МОЖНО ТОЛЬКО ПОСЛЕ СОХРАНЕНИЯ (когда есть $this->id)
		 */
		public function loginCheck()
		{			
			// Пытаемся найти пользователя с установленным в $this логином
			$User = User::find(array(
				"condition" => "login='{$this->login}' AND id!={$this->id}",
			));
			
			// Если пользователь с таким логином нашелся
			if ($User) {
				// Меняем логин на форму user{id}
				$this->login = "user".$this->id;
				return true;
			} else {
				return false;
			}
		}
		
		/*
		 * Добавить прилагательное
		 * $adjective	– прилагательное
		 * $ajax		- echo-ответы аяксу
		 */
		public function addAdjective($adjective, $ajax = false)
		{
			// Проверяем есть ли че-нить
			if (strlen($adjective) < 3 || mb_strlen($adjective) > Adjective::MAX_LENGTH) {
				if ($ajax) {
					echo "Слишком короткая/длинная мысль";					
				}
				// throw new Exception("Пустое или слишком длинное прилагательное");	
				exit();
			}
			

			// Обрезаем пробелы и теги в прилагательном
			$adjective = strtolower(secureString($adjective));
			
			// Смотрим было есть ли уже такое прилагательное
			$Adjective = Adjective::find(array(
				"condition" => "adjective = '$adjective'",
			));
			
			// Если прилагательное не нашлось, создаем новое
			if (!$Adjective) {
			 	
			 	$isNewRecord = true;
			 	
				// Создаем новое прилагательное пользователю
				$Adjective = new Adjective(array(
					"adjective"	=> $adjective,	
				));
				
				// Сохраняем прилагательное (обязательно ДО $Adjective->addVote, потому что нужен ID для добавления голоса)
				$Adjective->save();
			} else {
				// Иначе, если прилагательное уже нашлои И ОНО СКРЫТО
				if ($Adjective->hidden) {
					if ($ajax) {
						echo $this->first_name." запретил".($this->gender ? "а" : "")." голосование за «{$Adjective->adjective}»";
					}
					exit();
				}
			}
			
			// Голосуем и получаем ID добавленного голоса (true – это первый_голос, нужно для FEED, чтобы отображалось «Оставил новую оценку»,
			// а не «Проголосовал за»)
			$id_vote = $Adjective->addVote($ajax, true, true); // $ajax, положительный_голос, первый_голос
			
			// Если голос был успешно добавлен
			if ($id_vote)
			{
				// Если прилагательное новое, то сохраняем ID первого голоса
				if ($isNewRecord) {
					$Adjective->id_first_vote = $id_vote;
					$Adjective->save();
				}
				
				// Добавляем новое прилагательное в Adjectives объекта
				$this->Adjectives[] = $Adjective;
				
				// Посылаем ответ аяксу
				if ($ajax) {
					// Возвращаем ID только что добавленного прилагательного
					echo $Adjective->id;
				}	
			}
			
			// Возвращаем только что добавленное прилагательное
			return $Adjective;	
		}
		
		/*
		 * Проверка кол-ва новостей
		 */
		public function newsCount()
		{			
			// Кол-во новостей 
			$NewsCount = Feed::findAll(array(
				"condition"	=> "id > {$this->id_last_seen_news}",
			), true);
			
			// Если новостей не нашлось, выводим ноль
			return ($NewsCount ? $NewsCount : 0);
		}
		
		/*
		 * Обновить значения последних просмотренных новостей
		 */
		public function updateNewsCount()
		{	
			// Получаем новые значения
			$id_last_news = Feed::lastId();			
			
			// Если нужно обновлять – обновляем
			if ($this->id_last_seen_news < $id_last_news) {
				$this->id_last_seen_news = $id_last_news;
				$this->save();
			}
		}
		

		/*
		 * Получаем новые голоса
		 * $id_last_seen_vote – id последнего просмотренного голоса, если не указан, то $this 
		 * COMMENT: если из $this, то пользователь просматривает свои же новости, если передается переменная 
		 * - то просмативает кто-то другой (с какой новости отображать новые/старые)
		 */
		 /*
		public function newVotes($id_last_seen_vote = false)
		{
			// Если установлен $id_last_seen_vote, то просматриваем новые с него, если нет, берем из $this
			$id_last_seen_vote = ($id_last_seen_vote ? $id_last_seen_vote : $this->id_last_seen_vote);

			if ($id_last_seen_vote) {
				// Находим новые голоса
				$NewVotes = Vote::findAll(array(
					"condition"	=> "id > ".$id_last_seen_vote,
					"order"		=> "id DESC",
				));
			} else {
				// Находим все голоса
				$NewVotes = Vote::findAll(array(
					"order"		=> "id DESC",
				));
			}
			
			return ($NewVotes ? $NewVotes : array());	// Чтобы если нет новых голосов, не было WARNING: Invalid argument supplied for foreach
		}
		
		/*
		 * Получаем старые голоса
		 * см. описание newVotes()
		 */
		 /*
		public function oldVotes($id_last_seen_vote = false)
		{
			// Если установлен $id_last_seen_vote, то просматриваем новые с него, если нет, берем из $this
			$id_last_seen_vote = ($id_last_seen_vote ? $id_last_seen_vote : $this->id_last_seen_vote);

			if ($id_last_seen_vote) {
				// Находим новые голоса
				$OldVotes = Vote::findAll(array(
					"condition"	=> "id <= ".$id_last_seen_vote,
					"order"		=> "id DESC",
					"limit"		=> 20,
				));
			}
			
			return ($OldVotes ? $OldVotes : array());	// Чтобы если нет новых голосов, не было WARNING: Invalid argument supplied for foreach
		}
*/
		
		
		/*
		 * Получаем новости
		 * $id_last_seen_news – id последнего просмотренной новости, если не указан, то $this 
		 * COMMENT: если из $this, то пользователь просматривает свои же новости, если передается переменная 
		 * - то просмативает кто-то другой (с какой новости отображать новые/старые)
		 */
		public function getNews($id_last_seen_news = false)
		{
			// Если установлен $id_last_seen_vote, то просматриваем новые с него, если нет, берем из $this
			$id_last_seen_news = ($id_last_seen_news === false ? $this->id_last_seen_news : $id_last_seen_news);

			if ($id_last_seen_news) {
				// Находим новые голоса
				$News = Feed::findAll(array(
					"condition"	=> "id > ".$id_last_seen_news,
					"order"		=> "id DESC",
				));
			} else {
				// Находим все голоса
				$News = Feed::findAll(array(
					"order"		=> "id DESC",
				));
			}

			return ($News ? $News : array());	// Чтобы если нет новых голосов, не было WARNING: Invalid argument supplied for foreach
		}
		
		/*
		 * Получаем просмотренные новости
		 * см. описание newVotes()
		 */
		public function getOldNews($id_last_seen_news = false)
		{
			// Если установлен $id_last_seen_vote, то просматриваем новые с него, если нет, берем из $this
			$id_last_seen_news = ($id_last_seen_news === false ? $this->id_last_seen_news : $id_last_seen_news);

			if ($id_last_seen_news) {
				// Находим новые голоса
				$OldNews = Feed::findAll(array(
					"condition"	=> "id <= ".$id_last_seen_news,
					"order"		=> "id DESC",
					"limit"		=> 20,				// Получаем последние 20 новостей
				));
			}
		
			return ($OldNews ? $OldNews : array());	// Чтобы если нет новых голосов, не было WARNING: Invalid argument supplied for foreach
		}
		
		/*
		 * Вход/запись пользователя в сессию
		 */
		public function toSession()
		{
			$_SESSION["user"] = $this;
		}
		
		/*
		 * Возвращает массив с сохраняемой в БД информацией 
		 */
		public function dbData()
		{
			// Возвращаем только те данные, которые берутся из БД
			foreach ($this->mysql_vars as $var) {
				$return[$var] = $this->{$var};
			}
			
			// Если в БД еще есть сериализованные данные
			if (count($_serialized)) {
				foreach ($_serialized as $var) {
					$return[$var] = $this->{$var};
				}
			}
			
			return $return;
		}
		
		/*
		 * Сохраняет данные из БД
		 */
		 public function dbSave($array)
		 {
			 // Присваеваем только те данные, которые берутся из БД
			foreach ($this->mysql_vars as $var) {
				$this->{$var} = $array[$var];
			}
			
			// Если в БД еще есть сериализованные данные
			if (count($_serialized)) {
				foreach ($_serialized as $var) {
					$this->{$var} = $array[$var];
				}
			}
			
			// Сюда входит сохранение
			$this->save();
		 }
		 
		 /*
		  * Установить соединение с БД текущего пользователя
		  */
		 public function initConnection()
		 {
			 initUserConnection($this->id);
		 }
		 
		 /*
		  * Подписаться на пользователя
		  * $id_user – ID пользователя, на которого подписываемся
		  */
		 public function subscribeTo($id_user)
		 {
			 // Пытаемся найти подписку на этого пользователя, если уже существует, то отписываемся
			 $Sub = Subscription::find(array(
			 	"condition"	=> "id_user={$id_user}",
			 ));
			 
			 // Если подписка найдена, удаляем ее
			 if ($Sub) {
			 	// Устанавливаем соединение с БД пользователя, на которого подписываемся 
			 	 $SubUser = User::findById($id_user);
			 	 
			 	 // Уменьшаем кол-во подписчиков у пользователя
			 	 $SubUser->subscribers--;
			 	 $SubUser->save();
			 	 
			 	 // Удаляем подписчика
			 	 $Subr = Subscriber::find(array(
			 	 	"id_user"	=> User::fromSession(false)->id,
			 	 ));
			 	 // Если подписчик найден, то удаляем
			 	 if ($Subr) {
				 	 $Subr->delete();
			 	 }
			 	 
			 	 // Добавляем новость об отписке
			 	 Feed::create(array(
			 	 	"id_user" 		=> User::fromSession(false)->id,
			 	 	"id_news_type"	=> 7
			 	 ));
			 	 			 	 
			 	 // Переподключаемся к БД основного пользователя
			 	 $User = User::fromSession();
			 	 
			 	 // Уменьшаем кол-во подписок у пользователя
			 	 $User->subscriptions--;
			 	 $User->save();
			 	 
			 	 // Удаляем подписку
				 $Sub->delete();				 
			 } else {
			 	 // Устанавливаем соединение с БД пользователя, на которого подписываемся (чтобы получить ID последнего голоса)
			 	 $SubUser = User::findById($id_user);
			 	 
			 	 // Увеличиваем кол-во подписчиков у пользователя
			 	 $SubUser->subscribers++;
			 	 $SubUser->save();
			 	 
			 	 // Добавляем подписчика
			 	 $Subr = new Subscriber(array(
			 	 	"id_user"	=> User::fromSession(false)->id,
			 	 ));
			 	 $Subr->save();
			 	 
			 	 // Добавляем новость об подписке
			 	 Feed::create(array(
			 	 	"id_user" 		=> User::fromSession(false)->id,
			 	 	"id_news_type"	=> 6
			 	 ));
			 	 
			 	 // Получаем ID последней новости у пользователя, на которого подписываемся (после подписки новостей должно быть 0)
			 	 $id_last_seen_news = Feed::lastId();
			 	 
			 	 // Переподключаемся к БД основного пользователя
			 	 $User = User::fromSession();
			 	 
			 	 // Увеличиваем кол-во подписок у пользователя
			 	 $User->subscriptions++;
			 	 $User->save();

				 // Иначе добавляем подписку
				 $Sub = new Subscription(array(
				 	"id_user"		=> $id_user,
				 	"id_last_seen_news"	=> $id_last_seen_news,
				 ));
				 
				 // Сохраняем подписку
				 $Sub->save();
			 }
		 }
		 
		 /*
		  * Проверяем подписку
		  * $id_user – проверяем подписан ли текущий пользователь на ID_USER'а
		  */
		 public function subscribedTo($id_user)
		 {
			 return (Subscription::find(array(
			 	"condition"	=> "id_user={$id_user}",
			 )) ? true : false);
		 }
		 
		 /*
		  * Получаем подписки
		  */
		 public function Subscriptions()
		 {
			 $Subscriptions = Subscription::findAll(array(
			 	"order"	=> "id DESC",
			 ));
			 
			 return ($Subscriptions ? $Subscriptions : array()); // Чтобы не было WARNING: Invalid argument supplied for foreach
		 }
		 
		 /*
		  * Получаем подписчиков
		  */
		 public function Subscribers()
		 {
			 $Subscribers = Subscriber::findAll(array(
			 	"order"	=> "id DESC",
			 ));
			 
			 return ($Subscribers ? $Subscribers : array()); // Чтобы не было WARNING: Invalid argument supplied for foreach
		 }
		 
		 /*
		  * Устанавливаем значения по умолчанию
		  */
		 public function setDefaults()
		 {
		 	
			$this->subscribers		= isset($this->subscribers) ? $this->subscribers : 0;
			$this->subscriptions	= isset($this->subscriptions) ? $this->subscriptions : 0;
			$this->anonymous		= 1;
		 }
		 
		 /*
		 * Создаем таблицу пользователя
		 * @return true в случае успешного создания БД пользователя и таблиц, FALSE в случае ошибки
		 */
		public function createTable()
		{
			// Открываем соединение с БД пользователя	
			$new_db = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD);
			
			// Установлено ли соединение
			if (mysqli_connect_errno($new_db))
			{
				die("Failed to create a new DB: " . mysqli_connect_error());
			}
			
			// Устанавливаем кодировку
			$new_db->set_charset("utf8");	
			
			// Запрос на создание новой БД
			$result = $new_db->query("CREATE DATABASE ".DB_PREFIX."user_{$this->id}");
			
			
			// echo "RESULT=".$result;
			
			// Если запрос выполнен успешно
			if ($result) {
			//	echo "DB ".DB_PREFIX."user_{$this->id} created!";
				// Подключаемся к только что созданной БД
				$new_db->select_db(DB_PREFIX."user_{$this->id}");
				
				// Создаем таблицы по умолчанию
				$result = $new_db->multi_query("CREATE TABLE `adjectives` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `adjective` varchar(20) NOT NULL COMMENT 'Прилагательное',
					  `id_first_vote` int(10) unsigned DEFAULT NULL COMMENT 'ID первого голоса за прилагательное (Он добавил, остальные голосуют)',
					  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Видимость прилагательного',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Отзывы о пользователе (прилагательные)' AUTO_INCREMENT=1 ;
					

					CREATE TABLE `subscribers` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_user` int(10) unsigned DEFAULT NULL COMMENT 'ID подписчика',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Подписчики' AUTO_INCREMENT=1 ;
					
					CREATE TABLE `subscriptions` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `id_user` int(10) unsigned DEFAULT NULL COMMENT 'ID пользователя, на которого подписан',
					  `id_last_seen_news` int(10) unsigned NOT NULL COMMENT 'ID последнего просмотренного голоса (для новостей)',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
					
					CREATE TABLE `votes` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `id_adjective` int(10) unsigned NOT NULL COMMENT 'ID прилагательного',
					  `id_user` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID пользователя, который проголосовал. 0 - аноним',
					  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - положительный голос, 0 - отрицательный',
					  `ip` varchar(15) DEFAULT NULL COMMENT ' IP проголосовавшего',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='IP проголосовавших' AUTO_INCREMENT=1 ;
					
					CREATE TABLE IF NOT EXISTS `feed` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `id_news_type` int(11) DEFAULT NULL COMMENT 'ID типа новости из SETTINGS - news_types',
					  `id_user` int(11) NOT NULL DEFAULT '0' COMMENT 'ID пользователя (Если не указан, в новостной ленте будет указан Аноним)',
					  `id_adjective` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID прилагательного (само прилагательное вставляется вместо {1})',
					  `date` datetime NOT NULL COMMENT 'Дата новости',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Новостная лента' AUTO_INCREMENT=1 ;
					
					CREATE TABLE `comments` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `id_adjective` int(10) unsigned DEFAULT NULL COMMENT 'ID прилагательного, которое комментируется',
					  `id_user` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID проголосовавшего пользователя (0 - аноним)',
					  `ip` varchar(15) DEFAULT NULL COMMENT 'IP адрес комментатора',
					  `comment` varchar(255) DEFAULT NULL COMMENT 'Комментарий',
					  `time` datetime NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Комментарии прилагательных' AUTO_INCREMENT=1 ;
				");
				
				// echo "ERROR=".$new_db->error."\n";
				
				// Устанавливаем соединение с текущей новой БД
				global $db_user;
				$db_user = $new_db;
				
				// Возвращаем TRUE/FALSE функции
				return $result;
			} else {
			//	echo "COULD'T CREATE DB";
				return false;
			} 
		}	
		 
		 

	}
