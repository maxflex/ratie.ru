<?php
	// Глобальные функции сайта
	
	/*
	 * Пре-тайп
	 */
	function preType($anything, $exit = false)
	{
		echo "<pre>";
		print_r($anything);
		echo "</pre>";
		
		if ($exit)
		{
			exit();
		}
	}
	
	/*
	 * Возвращает соединение DB_SETTINGS
	 */
	function dbSettings()
	{
		global $db_settings;
		return $db_settings;
	}
	
	/*
	 * Создает и возвращает соединение пользователя
	 */
	function dbUser()
	{
		global $db_user;
		
		return $db_user;
	}
	
	/*
	 * Создаем подключение к БД user_x
	 */
	function initUserConnection($id_user)
	{
		global $db_user; 

		// Открываем соединение с основной БД		
		$db_user = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_PREFIX."user_{$id_user}");
		
		// Установлено ли соединение
		if (mysqli_connect_errno($db_user))
		{
			die("Failed to connect to USER {$id_user} MySQL: " . mysqli_connect_error());
		}
		
		// Устанавливаем кодировку
		$db_user->set_charset("utf8");		
	}
	
	/*
	 * Показываем к какой таблице пользователя подключены (бд user_x)
	 */
	function showDbUser()
	{
		global $db_user;
		echo $db_user->query("SELECT DATABASE()")->fetch_array()[0];
	}
	
	/*
	 * Получает текущее время
	 */
	function now()
	{
		return date("Y-m-d H:i:s");
	}
	
	/*
	 * Обрезает пробелы и извлекает теги
	 */
	function secureString($string)
	{
		return trim(strip_tags($string));
	}
	
	/*
	 * Настоящий IP пользователя
	 */
	function realIp()
	{
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];
	
	    if(filter_var($client, FILTER_VALIDATE_IP))
	    {
	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP))
	    {
	        $ip = $forward;
	    }
	    else
	    {
	        $ip = $remote;
	    }
	
	    return $ip;
	}
	
	/*
	 * Включить PARTIAL
	 * $string	– название включаемого файла
	 * $vars	– переменные, которые будут доступны в файле
	 */
	function partial($string, $vars = array())
	{
		// Если передаем переменные в инклуд, то объявляем их здесь (иначе будут недоступны)
		if (!empty($vars)) {
			// Объявляем переменные, соответсвующие элементам массива
			foreach ($vars as $key => $value) {
				$$key = $value;
			}
		}
			
		$called_dir = dirname(debug_backtrace()[0]["file"]);	// Получаем путь к директории, откуда была вызвана функция
		
		include_once($called_dir."/_".$string.".php");
	}
	
	
	/*
	 * Включить глобальный PARTIAL
	 * $string	– название включаемого файла
	 * $vars	– переменные, которые будут доступны в файле
	 */
	function globalPartial($string, $vars = array())
	{
		// Если передаем переменные в инклуд, то объявляем их здесь (иначе будут недоступны)
		if (!empty($vars)) {
			// Объявляем переменные, соответсвующие элементам массива
			foreach ($vars as $key => $value) {
				$$key = $value;
			}
		}
					
		include_once(BASE_ROOT."/views/_partials/_".$string.".php");
	}
	
	/*
	 * В формат ангуляра
	 */
	function angInit($name, $Object)
	{
		return $name." = ".htmlspecialchars(json_encode($Object, JSON_NUMERIC_CHECK));
	}
	
	/*
	 * Преобразование true/false в 1/0 для сохранения в БД
	 */
	function trueFalseConvert(&$array)
	{
		foreach ($array as $key => $val)
		{
			if ($val === "true") {
				$array[$key] = true;
			} elseif ($val === "false") {
				$array[$key] = false;
			}
		}
	}
	
	/*
	 * Возвратить значение, если оно установлено
	 * $value 	- проверяемое значение
	 * $pre		- если значение установлено, добавить при выводе
	 */ 
	function ifSet($value, $pre = "")
	{
		return (isset($value) ? $pre.$value : "");
	}
	
	/*
	 * Создать URL
	 * $params = array (controller, action, text, 
	 * params - массив, дополнительные параметры, будут переданы в GET 
	 * htmlOptions - массив, аттрибуты HTML элемента)
	 */
	function createUrl($params)
	{
		// Если есть опции HTML (атрибуты)
		if (isset($params["htmlOptions"])) {
			foreach ($params["htmlOptions"] as $option => $value) {
				$htmlOptions .= $option."='$value' ";
			}
		}
		
		echo "<a $htmlOptions href='".$params['controller']
			 	.ifSet($params["action"])
			 	.(isset($params["params"]) ? "&".http_build_query($params["params"]) : "")."'>"
			 	.$params["text"]."</a>";
	}
	
	/*
	 * Проверяет активен ли пункт меню
	 * $controller	– контроллер, при котором пункт меню активен
	 * $action		– экшн, при котором пункт меню становится активен
	 * $params (array) - дополнительные параметры для сравнения
	 */
	function menuActive($controller, $action = null, $params = array())
	{
		// Проверяем контроллер
		if ($_GET["controller"] != $controller) {
			return;
		}
		
		// Проверяем экшн
		if (isset($action) && $_GET["action"] != $action) {
			return;
		}
		
		// Проверяем дополнительные параметры
		foreach ($params as $param_name => $param_val) {
			if ($param_val != $_GET[$param_name]) {
				return;
			}
		}
		
		// Если все проверки пройдены, возвращаем активный класс
		return "class='active'";
	}
	
	/*
	 * Удаляем куку
	 * $cookie_name – какую куку удаляем
	 */
	function removeCookie($cookie_name) 
	{
		unset($_COOKIE[$cookie_name]);
		setcookie($cookie_name, '', time() - 3600);
	}
	
	/*
	 * Функция просто отображает через H1 (для тестирования)
	 */
	function h1($text)
	{
		echo "<h1 class='text-white'>$text</h1><br>";
	}
?>