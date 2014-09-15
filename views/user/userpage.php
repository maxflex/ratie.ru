<div class="container" ng-app="UserPage" id="user-page" ng-controller="UserCtrl" ng-init="
	id_viewing = <?= $id_viewing ?>;
	<?= (!$own_page ?  ( isset($subscribed) ? "subscribed = $subscribed;" : "" ) : "") ?>
	<?= ($own_page  ?  ( isset($_intro_adj_hide) ? "_intro_adj_hide = $_intro_adj_hide;" : "" ) : "") ?>
">
	<h1 id="name-lastname" style="text-align: center; color: white"><?= $User->getName() ?></h1>
	<hr>
	
	<div class="row effect2">
		<div class="col-md-4 center-content">
			<?php globalPartial("ava", array("User" => $User)) ?>
			<!-- ДРУЗЬЯ/ПОДПИСЧИКИ/ПОДПИСКИ -->
			<?php
				globalPartial("buttons", array(
					"User" 			=> $User,
					"own_page"		=> $own_page,
					"subscribed"	=> $subscribed,
				));
			?>
			<?php// partial("social", array("social" => $User->social, "own_page" => $own_page, "id_user" => $User->id, "user_login" => $User->login)) ?>
				
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
					<h2 class="trans text-error animate-show" ng-show="pseudoForm.adjective.$error.maxlength">Слишком длинное мнение</h2>
					<h2 class="trans text-error animate-show" ng-show="many_words">Два слова – максимум</h2>

				</div>
				
				<!-- Псевдо-форма для работы валидаторов AngularJS -->
				<form name="pseudoForm" novalidate>
				<div class="adjective-div pull-left">
					<input type="text" placeholder="<?= $default_adjective ?>" name="adjective" id="adjective" ng-model="adjective" ng-keyup="updateHello()" ng-maxlength="<?= Adjective::MAX_LENGTH ?>" autocomplete="off" ng-pattern="/^[a-zA-Zа-яА-ЯёЁ\-\ ]+$/">
				</div>
				
				<button class="btn btn-success btn-xxl" ng-click="think()" ng-disabled="pseudoForm.$invalid || many_words">ОК!</button>				
				</form>
			</div>	
			
		<?php
			} else {
				// Если просматривается своя же страница
				?>
					<h2 class="trans text-white">Мнения обо мне:
						<button class="btn br5 btn-success pull-right" onclick="offerVote()" style="margin-top: 4px"><span class="glyphicon glyphicon-plus" style="margin: 0"></span></button>
					</h2>
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
					
					// Если своя страница и нет прилагательных -- выводим кнопку «Предложить оценить»
					if ($own_page) {
						echo "<button onclick='offerVote()' class='btn btn-primary mg-top btn-large br5' style='margin-top: 75px; width: 100%'>
						<span class='glyphicon glyphicon-edit'></span>
						Предложить друзьям оставить первое мнение о Вас анонимно</button>";
						
						echo "<div class='voteme-hint'>По этой ссылке друзья могут анонимно высказаться о тебе: 
						<span onclick='showVoteLink(this)'>http://ratie.ru/".$User->login."</span></div>";
					}
				}
		?>
		
		<div class="row" style="margin-top: 10px" id="adjective-list-angular" ng-init="<?= angInit("adjectives", $Adjectives) ?>">
		
		<?php
			// Если есть мнения, выводить тулбар с сортировкой
			if ($Adjectives) {
		?>
			<!-- ТУЛБАР СОРТИРОВКА -->	
			<div class="tools-top-div">
				<span ng-show='order_type && order_hint' class="animate-show-fadeIn">Популярные</span>
				<span ng-show='!order_type && order_hint' class="animate-show-fadeIn">Новые</span>
				<span class="glyphicon glyphicon-sort trans-h" ng-click="changeOrder()" 
					ng-mouseenter="order_hint = true" 
					ng-mouseleave="order_hint = false">
				</span>
			</div>
		<?php
			}
		?>
		
		<?php
			/*******  ЕСЛИ ПОЛЬЗОВАТЕЛЬ ПРОСМАТРИВАЕТ СВОЮ ЖЕ СТРАНИЦУ *********/
			if ($own_page) {
				partial("own", array(
					"hidden_count"	=> $hidden_count,
					"show_friends"	=> $show_friends,
				));
			} else {
			/*******  ЕСЛИ СТРАНИЦУ ПОЛЬЗОВАТЕЛЯ ПРОСМАТРИВАЕТ ГОСТЬ *********/
				partial("guest", array(
					"_intro_for_anonymous_or_public" => $_intro_for_anonymous_or_public,
					"user_name" => $User->first_name,
				));
			}
		?>
		
		</div>
		
		<!-- КОНЕЦ СПИСОК ПРИЛАГАТЕЛЬНЫХ -->
		
	</div>
</div>
