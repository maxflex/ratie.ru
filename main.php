<!DOCTYPE html>
<html>
<head>
	<title>Ratie – анонимные мнения, сообщения и оценка характера</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <base href="http://localhost:8888/ratie.ru/">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/bootbox.js"></script>
	<script type="text/javascript" src="js/angular.js"></script>
	<script type="text/javascript" src="js/angular-animate.js"></script>
	<script type="text/javascript" src="//vk.com/js/api/openapi.js"></script>
	<script type="text/javascript" src="js/_settings.js"></script>
	<script type="text/javascript" src="js/engine.js"></script>
	<script type="text/javascript" src="js/auth.js"></script>
	<script type="text/javascript" src="js/main.js"></script>

</head>

<body ng-app="Auth" ng-controller="AuthCtrl">
	<div class="top-div">
		<img id="imac" class="imac animated" src="img/main/imac.png">
		<img id="iphone" class="iphone animated" src="img/main/iphone.png">
		
		<div class="left-div">
			<div class="logo">
				<img alt="Shots Selfies and Self Expressions" src="http://shots.me/img/landing/logo.png">
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