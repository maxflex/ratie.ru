<?php

	// Контроллер
	class ApiController extends Controller
	{
		// Ключ API, проверяется при каждом запросе к API
		const API_KEY = "qfJTcVaMsUm278qHUfEm";
		
		// Перед выполнением любого действия, устанавливаем заголовок для JSON данных API
		public function beforeAction()
		{
			// Тип данных - JSON
			header('Content-Type: application/json');
			
			// Первым делом проверяем API_KEY
			if (trim($_POST["API_KEY"]) != self::API_KEY) {
				self::errorMessage("Invalid API_KEY");
			}
		}
		
		/*
		 * Получить пользователя по ID ВКонтакте
		 * $_POST["id_vk"] - передается по посту ID_VK, по нему находится пользователь
		 */
		public static function actionGetUser()
		{
			// Находим пользователя по ID_VK
			$User = User::find(array(
				"condition"	=> "id_vk=".$_POST["id_vk"],
			));
			
			// Если пользователь найден
			if ($User) {
				// Возвращаем  JSON объекта пользователя
				self::retrunJSON($User);
			} else {
				// Ошибка
				self::errorMessage("User not found");
			}
			
		}
		
		/*
		 * JSON-сообщение с ошибкой
		 * $error_message – сообщение ошибки
		 */
		public static function errorMessage($error_message)
		{
			exit(json_encode(array(
					"error_message"	=> $error_message,
					"post_data"		=> $_POST,
				)));
		}
		
		/*
		 * Возвратить JSON
		 */
		public static function retrunJSON($Object)
		{
			echo json_encode($Object); 
		}
		
	}