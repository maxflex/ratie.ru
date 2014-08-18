<?php

	// Контроллер
	class ApiController extends Controller
	{
		
		// Перед выполнением любого действия, устанавливаем заголовок для JSON данных API
		public function beforeAction()
		{
			// Тип данных - JSON
			header('Content-Type: application/json');
		}
		
		public static function actionTest()
		{
			$User = User::find(array(
				"condition"	=> "id_vk" => $_POST["id_vk"],
			));
			
			if ($User) {
				echo json_encode($User);
			} else {
				echo "error_user_not_found";
			}
			
		}
		
	}