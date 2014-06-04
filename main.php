<?php
	// Проверяем залогинен ли пользователь
	if (isset($_SESSION["user"])){
		header("Location: ".$_SESSION["user"]->login);
	} else {
		// Если сессии пользователя нет, проверяем куки
		if (isset($_COOKIE["ratie_token"])) {
			// Подключаем файл конфигураций
			include_once("config.php");
						
			// Пытаемся войти по token в КУКАХ
			User::rememberMeLogin();
		}
	}
	
	/* // Если установлена КУКА с залогиненым пользователем
	if (isset($_COOKIE["logged"])) {
		// Сразу редиректим на страницу пользователя
		header("Location: ".$_COOKIE["logged"]);
	} */
?>
<!DOCTYPE html>
<html>
<head>
	<title>Ratie | Анонимные мнения о друзьях, комментарии и оценки</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="анонимная оценка характера мнения ratie рэйти анонимный чат сообщения сп история">
	<meta name="description" content="Анонимные мнения о друзьях, комментарии и оценки">
    <base href="http://localhost:8888/ratie.ru/">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
	<link rel="image_src" href="img/logo/vk_links.png">
	<link rel="shortcut icon" href="favicon_main.ico">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/bootbox.js"></script>
	<script type="text/javascript" src="js/angular.js"></script>
	<script type="text/javascript" src="js/angular-animate.js"></script>
	<script type="text/javascript" src="js/jquery-cookie.js"></script>
	<script type="text/javascript" src="//vk.com/js/api/openapi.js"></script>
	<script type="text/javascript" src="js/_settings.js"></script>
	<script type="text/javascript" src="js/engine.js"></script>
	<script type="text/javascript" src="js/auth.js"></script>
	<script type="text/javascript" src="js/main.js"></script>

</head>

<body ng-app="Auth" ng-controller="AuthCtrl">
<div class="lightbox" id="lightbox"></div>
	<div class="top-div">
		<img id="imac" class="imac animated" src="img/main/imac.png">
		<img id="iphone" class="iphone animated" src="img/main/iphone.png">
		
		<div class="left-div">
			<div class="logo">
				<img alt="Ratie – анонимные мнения и оценки" src="img/logo/white.png">
			</div>
			
			<div class="description">
			
				<div id="slide-1">
					<img src="img/main/people2.png">Узнайте, что друзья думают о Вас
				</div>

				
				<div id="slide-2">
					<img src="img/main/chat.png">Узнайте то, чего не скажут в лицо
				</div>
				
				<div id="slide-3">
					<img src="img/main/eye.png">Анонимные мнения и оценки
				</div>
				
				<div id="slide-4">
					<img src="img/main/friends.png">Следите за обновлениями друзей
				</div>
				
				<div id="slide-5">
					<img src="img/main/mega.png">Голосуйте публично или анонимно
				</div>
				
			</div>
			
			<div class="login-button">
				<button class="login" ng-click="login()">Войти через вконтакте</button>
			</div>
		</div>
	</div>
</body>

<footer>

</footer>
</html>