<div class="container" ng-app="ProfilePage" id="profile-page" ng-controller="ProfileCtrl">
	<h1 id="name-lastname">
		<span class="arrow-back big glyphicon glyphicon-chevron-left pull-left path-icon clickable left" onclick="goBack()"></span>
	<?=$User->first_name." ".$User->last_name?></h1>
	<hr>
	
	<div class="row effect2">
		<div class="col-md-4 center-content">
			<div class="ava-wrap" ng-mouseenter="ehover = true" ng-mouseleave="ehover = false">
				<div class="ava-blink edit-profile animate-fade" ng-hide="!ehover">
					<button class="btn btn-success btn-large" onclick="goTo('profile/edit')"><span class="glyphicon glyphicon-pencil"></span>Редактировать профиль</button>
				</div>
			<?php globalPartial("ava", array("User" => $User)) ?>
			</div>
			<div class="row sociallinks">
					<div class="circle" ng-mouseenter="friends = true" ng-mouseleave="friends = false" ng-click="getFriends()"><span class="glyphicon glyphicon-user"></span></div>
					<div class="circle" ng-mouseenter="followers = true" ng-mouseleave="followers = false" onclick="goTo('profile/subscribers')"><span><?= $User->subscribers ?></span></div>
<!-- 					<div class="circle" ng-mouseenter="following = true" ng-mouseleave="following = false"><span>23</span></div>	 -->
			
					<h3 ng-show="followers" class="foll center-content text-white badge-primary animate-show"><span class="glyphicon glyphicon-ok"></span>Подписчики</h3>
					
					<h3 ng-show="friends" class="foll center-content text-white badge-primary animate-show"><span class="glyphicon glyphicon-user"></span>Друзья на Ratie</h3>
			</div>				
		</div>
		
		<div class="col-md-6">
			
			<!-- ЗАГОЛОВКИ -->
			<div class="row">
				<div class="col-md-6">
					<h2 class="trans text-white news" 
						ng-click="subs = false"
						ng-class="{'selected' : !subs}">Ваши подписчики <span class="badge news badge-success"><?= $User->subscribers ?></span></h2>		
				</div>
			</div>
			<!-- КОНЕЦ ЗАГОЛОВКИ -->
			
			<!-- КОНТЕНТ -->
			<div class="row">
				<div class="col-md-12 news-div">
				<?php
				
					// Получаем подписчиков пользователя
					$Subscribers = $User->Subscribers();
					
					// Если подписок нет, выводим сообщение об этом
					if (!$Subscribers) {
						echo "<h3 class='trans center-content text-white badge-success animate-show mg-top'>"
							 ."<span class='glyphicon glyphicon-user'></span>На вас никто не подписан</h3>";
					} else {

						foreach ($Subscribers as $Subscriber) {
							// Получаем пользователя, на которого подписаны
							$SubscriberUser = User::findById($Subscriber->id_user);
							
							echo "<div class='subscription-row'>";
							
							// Выводим аву
							createUrl(array(
								"controller"=> $SubscriberUser->login,
								"text"		=> '<div style="background-image: url('.$SubscriberUser->avatar.')" class="news-ava '.($SubscriberUser->stretch ? "stretch" : "").'"></div>',
							));
							
							createUrl(array(
								"controller"	=> $SubscriberUser->login,
								"text"			=> $SubscriberUser->login,
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
<!-- ДРУЗЬЯ НА САЙТЕ -->
<div id="friends" class="col-md-6 news-div" style="width: 50%; display: none">
    <div style="max-height: 150px; overflow-y: auto; text-align: center">
        <div ng-repeat="friend in friends" style="display: inline-block">
            <a target="_blank" href="{{friend.login}}">
            	<div style="background-image: url({{friend.avatar}})" class="ava-60" ng-class="{stretch : friend.stretch}"></div>
            </a>
        </div>
    </div>
</div>
<!-- КОНЕЦ ДРУЗЬЯ НА САЙТЕ -->
	</div>
</div>

