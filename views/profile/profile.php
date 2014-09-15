<div class="container" ng-app="ProfilePage" id="profile-page" ng-controller="ProfileCtrl">
	
	<img src="img/icon/browser.png" class="path-icon">
	<h1 id="name-lastname"><?= $User->getName() ?></h1>
	<hr>
	
	<div class="row effect2">
		<div class="col-md-4 center-content">
			<div class="ava-wrap" ng-mouseenter="ehover = true" ng-mouseleave="ehover = false">
				<div class="ava-blink edit-profile animate-fade" ng-hide="!ehover">
					<button class="btn btn-success btn-large" onclick="goTo('profile/edit')"><span class="glyphicon glyphicon-pencil"></span>Редактировать профиль</button>
				</div>
			<?php globalPartial("ava", array("User" => $User)) ?>
			</div>
			
			<?php globalPartial("buttons", array("User" => $User, "own_page" => true)) ?>		
		</div>
		
		<div class="col-md-6">
			
			<!-- ЗАГОЛОВКИ -->
			<div class="row">
				<div class="col-md-6">
					<h2 class="trans text-white news" 
						ng-click="subs = false"
						ng-class="{'selected' : !subs}">Новости <span class="badge news badge-success"><?= $User->newsCount() ?></span></h2>		
				</div>
				
				<div class="col-md-4">
					<h2 class="trans text-white news" 
						ng-click="subs = true"
						ng-class="{'selected' : subs}">Подписки <span class="badge news badge-success"><?= $User->subscriptions ?></span></h2>
				</div>
			</div>
			<!-- КОНЕЦ ЗАГОЛОВКИ -->
			
			<!-- КОНТЕНТ -->
			<div class="row">
			
				<!-- Новости -->
					<?php
						partial("profile_news", ["User" => $User]);
					?>
				<!-- Конец Новостей -->
				
				
				<!-- ПОДПИСКИ -->
					<?php
						partial("profile_subscriptions", ["User" => $User]);
					?>
				<!-- КОНЕЦ ПОДПИСКИ -->
			</div>
			<!-- КОНЕЦ КОНТЕНТ -->	
						
		</div>
		
<!-- ДРУЗЬЯ НА САЙТЕ -->
<div id="friends" class="col-md-6 news-div" style="width: 50%; display: none">
    <div style="max-height: 150px; overflow-y: auto; text-align: center">
        <div ng-repeat="friend in friends" style="display: inline-block">
            <a target="_blank" href="{{friend.login}}">
            	<div style="background-image: url({{friend.avatar}})" class="ava-60" ng-class="{stretch : (friend.stretch == 1)}"></div>
            </a>
        </div>
    </div>
</div>
<!-- КОНЕЦ ДРУЗЬЯ НА САЙТЕ -->
		
	</div>
</div>

