<div class="container" ng-app="UserPage" id="user-page" ng-controller="UserCtrl" ng-init="subscribed = <?= $subscribed ?>">
	<h1 id="name-lastname" style="text-align: center; color: white"><?=$User->first_name." ".$User->last_name?></h1>
	<hr>
	
	<div class="row effect2">
		<div class="col-md-4 center-content">
			<?php globalPartial("ava", array("User" => $User)) ?>
			<!-- СОЦИАЛЬНЫЕ КНОПКИ -->
			<?php partial("social", array("social" => $User->social, "own_page" => $own_page, "id_user" => $User->id)) ?>
				
		</div>
		
		<div class="col-md-6">
		<?php
			// Если пользователь просматривает чужую страницу – отобразить голосование
			if (!$own_page) {
		?>
			<div class="row">
				<div class="clearfix">
					<h2 class="trans text-white animate-show" ng-hide="pseudoForm.adjective.$error.pattern || pseudoForm.adjective.$error.maxlength || many_words">
						Я думаю, что <?=$User->first_name?> {{adjective}}</h2>
					<h2 class="trans text-error animate-show" ng-show="pseudoForm.adjective.$error.pattern">Вводить можно только буквы</h2>
					<h2 class="trans text-error animate-show" ng-show="pseudoForm.adjective.$error.maxlength">Слишком длинная мысль</h2>
					<h2 class="trans text-error animate-show" ng-show="many_words">Два слова – максимум</h2>

				</div>
				
				<!-- Псевдо-форма для работы валидаторов AngularJS -->
				<form name="pseudoForm" novalidate>
				<div class="adjective-div pull-left">
					<input type="text" placeholder="<?= $default_adjective ?>" name="adjective" id="adjective" ng-model="adjective" ng-keyup="updateHello()" ng-maxlength="15" autocomplete="off" ng-pattern="/^[a-zA-Zа-яА-Я\-\ ]+$/">
				</div>
				
				<button class="btn btn-success btn-xxl" ng-click="think()" ng-disabled="pseudoForm.$invalid || many_words">ОК!</button>				
				</form>
			</div>	
			
		<?php
			} else {
				// Если просматривается своя же страница
				?>
					<h2 class="trans text-white">Мнения обо мне:</h2>
				<?php
			}
		?>
		<!-- СПИСОК ПРИЛАГАТЕЛЬНЫХ -->	
		<?php
				// Если нет прилагательных
				if (!$Adjectives) {
					echo '<h3 class="trans center-content text-white badge-success animate-show mg-top" ng-show="!adjectives">';
					echo ($own_page ? "<span class='glyphicon glyphicon-file'></span>Вас пока никто не оценивал" 
									: "<span class='glyphicon glyphicon-pencil'></span>Ваше мнение будет первым!");
					echo '</h3>';
				}
		?>
		
		<div class="row" style="margin-top: 30px" id="adjective-list-angular" ng-init="adjectives = <?=htmlspecialchars(json_encode($Adjectives, JSON_NUMERIC_CHECK))?>">
		
		<?php
			/*******  ЕСЛИ ПОЛЬЗОВАТЕЛЬ ПРОСМАТРИВАЕТ СВОЮ ЖЕ СТРАНИЦУ *********/
			if ($own_page) {
				partial("own", array("hidden_count" => $hidden_count));
			} else {
			/*******  ЕСЛИ СТРАНИЦУ ПОЛЬЗОВАТЕЛЯ ПРОСМАТРИВАЕТ ГОСТЬ *********/
				partial("guest");
			}
		?>
		
		</div>
		
		<!-- КОНЕЦ СПИСОК ПРИЛАГАТЕЛЬНЫХ -->
		
	</div>
</div>
