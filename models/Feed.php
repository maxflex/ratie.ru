<?php
	class Feed extends Model
	{
	
		/*====================================== ПЕРЕМЕННЫЕ И КОНСТАНТЫ ======================================*/
		
		public static $mysql_table	= "feed";
		
		/*====================================== СИСТЕМНЫЕ ФУНКЦИИ ======================================*/
		
		
		
		/*====================================== СТАТИЧЕСКИЕ ФУНКЦИИ ======================================*/
		
		/*
		 * Функция определяет соединение БД
		 */
		public static function dbConnection()
		{
			return dbUser();	// БД user_x
		}
		
		/*
		 * Функция создает новость
		 * $arguments – массив со значениями (id_news_type, id_user, id_adjective)
		 */
		public static function create($arguments)
		{
			// Создаем новость на основе значений
			$Feed = new Feed($arguments);
			
			// Дата новости = текущей дате
			$Feed->date = now();
			
			// Сохраняем новость и возвращаем результат сохранения
			return $Feed->save();
		}
		
		/*
		 * Функция удаляет строку от символа до символа
		 */
		public static function deleteBetween($beginning, $end, $string) {
			$beginningPos = strpos($string, $beginning);
			$endPos = strpos($string, $end);
			if (!$beginningPos || !$endPos) {
			return $string;
			}
			
			$textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
			
			return str_replace($textToDelete, '', $string);
		}
		
		/*
		 * Функция форматирует текст в зависимости от пола
		 * $gender – пол (м/ж – 0/1)
		 * $text   – текст, который будет обработан
		 * @return Поставил[a] новую оценку – (муж. «Поставил новую оценку», жен. «Поставила новую оценку»
		 * @return Подписался/подписалась на обновления – (муж. «Подписался на обновления», жен. «Подписалась на обновления»
		 */
		public static function genderFormat($gender, $text)
		{
			// Если в тексте есть слеш, то обрабатывать на (подписался/подписалась)
			if (strpos($text, "/")) {
				// Получаем два слова для м. и ж.
				preg_match("#([\S]+)/([\S]+)#", $text, $matches);
				
				// Получили м. и ж.
				$male 	= $matches[1];
				$female	= $matches[2];
				
				// Заменяем в зависимости от пола
				$text = str_replace($matches[0], ($gender ? $female : $male), $text);

			}
			
			// В зависимости от пола пишем (поставил[a] оценку)
			if ($gender) {
				// Обрабатываем дополнительную букву в квадратных скобках (Поставил[а])
				$text = str_replace(array("[", "]"), "", $text);
				
				// Обрабатываем слеш (Подписался/подписалась)
			} else {
				$text = self::deleteBetween("[", "]", $text);
			}
			
			// Возвращаем отформатированный текст
			return $text;
		}
		
		/*
		 * Функция отображает все новости
		 * $id_last_seen	– если FALSE, то отображать свои же новости (иначе указать LAST_SEEN_ID - с какого ID считаются новыми новости)
		 * $tophr 			– разделительный <hr> будет вверху, вместо отделения между новыми/старыми голосами
		 */
		public static function displayNews($User, $id_last_seen = false, $tophr = false)
		{
			// Разделитель вверху
			if ($tophr) {
				echo '<hr class="news-seperator">';
			}
			
			// Получаем просмотренные новости
			$OldNews = $User->getOldNews($id_last_seen);
			
			// Получаем новости
			$News = $User->getNews($id_last_seen);
			
			// Проверяем, есть ли вообще новости (После регистрации ничего нет. Если просмотренных новостей нет, то никаких новостей не было вообще)
			// Если совсем никаких новостей не было
			if (!$OldNews && !$News) {
				echo "<h3 class='trans center-content text-white badge-success animate-show mg-top'>"
					 ."<span class='glyphicon glyphicon-file'></span>Новостная лента пуста</h3>";
				return;
			}
			
			// Если есть новости, отображаем их
			if ($News) {
				foreach ($News as $OneNews) {
					$OneNews->display();
				}
			}
			
			//  Если вверху не было разделителя, ставим посередине
			if (!$tophr) {
				echo '<hr class="news-seperator">';
			}			
			
			// Отображаем просмотренные новости
			foreach ($OldNews as $OneNews) {
				$OneNews->display(true);
			}
		}
				
		/*====================================== ФУНКЦИИ КЛАССА ======================================*/
		
		/*
		 * Отображает одну новость из FEED
		 * $old – добавлять стили просмотренной новости
		 */
		public function display($old = false)
		{
			// Отключаем соединение с БД пользователя при создании нового объекта (см. public static $initialize_on_new в модели User)
			// Это обязательно нужно для того, чтобы после отображения логина ( например,  «пользователь userXXX проголосовал за прилагательное»
			// Сайт держал связь с основной БД, а не подключался к БД пользователя userXXX (иначе последующие новости не отобразятся правильно)
			User::$initialize_on_new = false;
			
			echo '<div class="news-row '.($old ? "old" : "").'">';
			
			// Получаем тип новости
			$NewsType	= NewsType::findById($this->id_news_type);
			
			// Если установлен ID пользователя
			if ($this->id_user) {
				// Получаем пользователя
				$User = User::findById($this->id_user);	
				
				// Выводим аву
				createUrl(array(
					"controller"=> $User->login,
					"text"		=> '<div style="background-image: url('.$User->avatar.')" class="news-ava '.($User->stretch ? "stretch" : "").'"></div>',
				));
				
				createUrl(array(
					"controller"	=> $User->login,
					"text"			=> $User->login,
					"htmlOptions"	=> array(
						"class"	=> "login-link",
					),
				));
				
				// $User->initConnection();	// Переподключаемся к пользователю
				//echo "<a href='index.php?controller=user&user=gamezo$VotedUser->login;
			} else {
				echo '<img src="img/profile/noava.png" class="news-rounded">';
				echo "Аноним";
			}

			echo " ";
			
			// В зависимости от пола пишем (поставил/поставила оценку)
			echo self::genderFormat($User->gender, $NewsType->text);
			
			// Если установлено прилагательное
			if ($this->id_adjective) {
				// Получаем прилагательное
				$Adjective	= Adjective::findById($this->id_adjective);
				
				// Если нашлось
				if ($Adjective) {
					echo '<span class="voted-adj"> '.$Adjective->adjective.'</span>';
				}
			}
			
			// Формируем ссылку
			switch ($NewsType->id) {
				case NewsType::SUBSCRIBED:
				case NewsType::UNSUBSCRIBED: {
					$link = $User->login;
					break;
				}
				
				case NewsType::COMMENT: {
					$link = User::fromSession(false)->login."/comments-".$this->id_adjective;
					break;
				}
				
				default: {
					$link = User::fromSession(false)->login."#".$this->id_adjective;
				}
			}
			
			// Иконка
			echo "<a href='$link'>";
			echo '<span class="thumb-circle glyphicon glyphicon-'.($old ? $NewsType->class_old." old " : $NewsType->class_new).' pull-right"></span>';
			
			echo '</a></div>';
		}
	}