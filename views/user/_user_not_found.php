<div class="container" id="user-page">
	<h1 id="name-lastname" style="text-align: center; color: white"><?= $User->getName() ?></h1>
	<hr>
	
	<div class="row effect2">
		<div class="col-md-4 center-content">
			<?php globalPartial("ava", array("User" => $User)) ?>
			<!-- СОЦИАЛЬНЫЕ КНОПКИ -->
			<?php partial("user_not_found_social") ?>
				
		</div>
		
		<div class="col-md-6">
			<div class="row">
				<div class="clearfix">
					<div class="badge badge-important center-t" style="margin-top: 15%; width: 100%; padding-left: 0; padding-right: 0">
						<h4 class="text-white" style="font-family: 'RaleWayMedium'; font-size: 18px">
						<span class="glyphicon glyphicon-remove" style="top: 3px"></span> Скорее всего, пользователь изменил логин
						</h4>
					</div>
					
					<?php
						// Если пользователь залогинен, то ссылка на переход в профиль
						// иначе ссылка входа на ratie
						if (User::loggedIn()) {
						?>
						<a href="http://ratie.ru/profile">
							<button class="btn btn-primary btn-large br5" style="margin-top: 50px; width: 100%">Перейти в профиль</button>
						</a>
						<?php
						} else {
						?>
						<a href="http://ratie.ru/#login">
							<button class="btn btn-primary btn-large br5" style="margin-top: 50px; width: 100%">Войти на Ratie</button>
						</a>
						<?php
						}
					?>
				</div>				
			</div>	
		</div>	
				
	</div>
</div>
