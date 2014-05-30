<?php	
	// Если не установлен контроллер, то главная страница
	if (empty($_GET["controller"])) {
		include_once("main.php");
		exit();	
	}
	
	// Для редиректа после отправки HEADERS
	ob_start();
	
	// Подключаем файл конфигураций
	include_once("config.php");
	
	// Стартуем сессию, ебаный врот
	session_start();
	
	// Получаем названия контроллеров и экшена	
	$_controller	 = $_GET["controller"];	// Получаем название контроллера
	$_action		 = $_GET["action"];		// Получаем название экшена
	
	// Проверка на аякс-запрос
	if (strtolower(mb_strimwidth($_action, 0, 4)) == "ajax") {
		
		$_ajax_request = true;
		
		// Это аякс-запрос, к скрипту можно обращаться только через AJAX
		if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
			die("SECURITY RESTRICTION: THIS PAGE ACCEPTS AJAX REQUESTS ONLY (poshel nahuj)");	// Выводим мега-сообщение
		}
	} else {
		$_ajax_request = false;
	}
	
	
	// Если не аякс запрос – грузим лэйаут
	if (!$_ajax_request) {
		// Лэйаут
		include_once("layouts/header.php");
		include_once("layouts/menu.php");	
	}
	
	// preType($_GET);
	// DELETE echo "<h2>".$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."</h2>";
	
	/* Основные действия */	
	$_controllerName = ucfirst(strtolower($_controller))."Controller";	// Преобразуем название контроллера в NameController
	$_actionName	 = "action".ucfirst(strtolower($_action));			// Преобразуем название экшена в actionName
	
	
	$IndexController = new $_controllerName;	// Создаем объект контроллера
	
	// Запускаем BeforeAction, если существует
	if (method_exists($IndexController, "beforeAction")) {
		$IndexController->beforeAction();
	}
	
	// Если указанный _actionName существует – запускаем его
	if (method_exists($IndexController, $_actionName))
	{
		$IndexController->$_actionName();			// Запускаем нужное действие
	} // иначе запускаем метод по умолчанию
	else
	{
		$IndexController->{"action".$IndexController->defaultAction}();
	}
	
	// Когда понадобится AfterAction – раскомментировать
	/* // Запускаем afterAction, если существует
	if (method_exists($IndexController, "afterAction")) {
		$IndexController->afterAction();
	} */
	
	/*********************/
	
	
	
	// Лэйаут
	if (!$_ajax_request) {
		include_once("layouts/footer.php");	
	}
	
	// Для редиректа после HEADERS
	ob_flush();
?>