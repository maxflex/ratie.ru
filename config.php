<?php
	/* Файл конфигурации */
	
	// Настройки
	$GLOBALS["settings"] = (object)[
		"version" 			=> "1.1",				// Версия сайта (нужная для обновления кэша JS и CSS)
		"ADJECTIVE_LIMIT"	=> 999,					// Максимальное кол-во прилагательных подряд для одного IP
		"COMMENT_LIMIT"		=> 999,					// Максимальное кол-во комментариев подряд для одного IP
		"MESSAGE_LIMIT"		=> 999,					// Лимит сообщений
	];
	
	// Константы
	$_constants = array(
		"DB_LOGIN"		=> "root",
		"DB_PASSWORD"	=> "root",
		"DB_HOST"		=> "localhost",
		"DB_PREFIX"		=> "",
		"BASE_ROOT"		=> $_SERVER["DOCUMENT_ROOT"]."/ratie.ru",
	);

	// Контроллеры и модели 
	$_controllers	= array(
		"", "User", "Index", "Profile", "Test", 
	);
	
	$_models		= array(
	 	"Model", "User", "Adjective", "Vote", "DefaultAdjective", "Subscription", "Subscriber", "NewsType",
	 	"Feed", "Comment",
	 );
	
	/********************************************************************/
	
	
	// Объявляем константы
	foreach ($_constants as $key => $val)
	{
		define($key, $val);
	}
		
	// Конфигурация ошибок (error_reporing(0) - отключить вывод ошибок)
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
		
	// Открываем соединение с основной БД
	$db_settings = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_PREFIX."settings");
	
	// Установлено ли соединение
	if (mysqli_connect_errno($db_settings))
	{
		die("Failed to connect to MySQL: " . mysqli_connect_error());
	}
	
	// Устанавливаем кодировку
	$db_settings->set_charset("utf8");
	
	include_once("functions.php");				// Подключаем основные функции
	
	// Подключаем модели
	foreach($_models as $val)
	{
		require_once("models/{$val}.php");					// Подключаем модель
	}
	
	// Подключаем контроллеры
	foreach($_controllers as $val)
	{
		require_once("controllers/{$val}Controller.php");	// Подключаем контроллер
	}
?>