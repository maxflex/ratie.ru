<div class="container" ng-app="UserAdditional" id="user-additional" ng-controller="UserAdditionalCtrl" ng-init="
	<?= (!$own_page ?  ( isset($subscribed) ? "subscribed = $subscribed;" : "" ) : "") ?>
">
	<h1 id="name-lastname" style="text-align: center; color: white">
		<span class="arrow-back big glyphicon glyphicon-chevron-left pull-left path-icon clickable left" onclick="goBack()"></span>
		<?= $User->getName() ?>
	</h1>
	<hr>
	
	<div class="row effect2">
		<div class="col-md-4 center-content">
			<a href="<?= $User->login ?>">
				<?php globalPartial("ava", array("User" => $User)) ?>
			</a>
			
			<!-- ДРУЗЬЯ/ПОДПИСЧИКИ/ПОДПИСКИ -->
			<?php
				globalPartial("buttons", array(
					"User" 		=> $User, 
					"own_page"	=> $own_page,
					"subscribed"=> $subscribed,
					"page"		=> $page,
				));
			?>
		</div>
		
		<div class="col-md-6">
			<div class="row">
				<div class="clearfix">
				<!-- ЗАГОЛОВКИ -->
			<div class="row">
				<div class="col-md-6">
					<h2 class="trans text-white news selected"><?= ($own_page ? "Мои ".mb_strtolower($labels[0], 'UTF-8') : $labels[0]) ?> <span class="badge news badge-success"><?= count($UsersList) ?></span></h2>		
				</div>
			</div>
			<!-- КОНЕЦ ЗАГОЛОВКИ -->
			
			<!-- КОНТЕНТ -->
			<div class="row">
				<div class="col-md-12 news-div">
				<?php
					// Если подписок нет, выводим сообщение об этом
					if (!$UsersList) {
						echo "<h3 class='trans center-content text-white badge-success animate-show mg-top'>"
							 ."<span class='glyphicon glyphicon-user'></span>Нет {$labels[1]}</h3>";
					} else {

						foreach ($UsersList as $UserList) {
							// Получаем пользователя, на которого подписаны
							$OneUser = User::findById($UserList->id_user);
							
							echo "<div class='subscription-row'>";
							
							// Выводим аву
							createUrl(array(
								"controller"=> $OneUser->login,
								"text"		=> '<div style="background-image: url('.$OneUser->avatar.')" class="news-ava '.($OneUser->stretch ? "stretch" : "").'"></div>',
							));
							
							createUrl(array(
								"controller"	=> $OneUser->login,
								"text"			=> $OneUser->getName(),
								"htmlOptions"	=> array(
										"class"	=> "login-link",
								),
							));
							
							echo '<span class="subscription-arrow glyphicon glyphicon-chevron-right pull-right"></span>';
							echo "</div>";
						}
					
						// Переподключаемся к основному пользователю
						$User->initConnection();
					}
				?>
				</div>
			</div>
			<!-- КОНЕЦ КОНТЕНТ -->	

				</div>
			</div>
		</div>
		
	</div>
</div>
