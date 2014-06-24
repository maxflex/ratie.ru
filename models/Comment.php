<?php
	class Comment extends Model
	{
	
		/*====================================== ПЕРЕМЕННЫЕ И КОНСТАНТЫ ======================================*/

		public static $mysql_table	= "comments";
				
		/*====================================== СИСТЕМНЫЕ ФУНКЦИИ ======================================*/
		
		public function __construct($array)
		{
			parent::__construct($array);
			
			// Создаем переменные для ANGULAR
			$this->_constructAngular();
		}
		
		/*====================================== СТАТИЧЕСКИЕ ФУНКЦИИ ======================================*/
		
		/*
		 * Функция определяет соединение БД
		 */
		public static function dbConnection()
		{
			return dbUser();	// БД user_x
		}
				
		/*====================================== ФУНКЦИИ КЛАССА ======================================*/

		/*
		 * Устанавливает переменные для Angular
		 */
		private function _constructAngular()
		{
			// Не устанавливать соединение с БД пользователя
			User::$initialize_on_new = false;
			
			// Получаем пользователя, который оставил комментарий
			$User = User::findById($this->id_user);
			
			// Устанавливаем переменные
			$this->_ang_ava		= $User->avatar;
			$this->_ang_stretch = $User->stretch;
			$this->_ang_login	= $User->login; 
			$this->order		= 0;
		}
		
		/*
		 * Получаем прилагательное комментария
		 */
		public function Adjective()
		{
			return Adjective::findById($this->id_adjective);
		}
	}