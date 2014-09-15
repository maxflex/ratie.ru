<?php
		// Если не своя страница --  то выводим кнопку «Анонимное сообщение»
		if (!$own_page && !$no_message_button) {
	?>
<button class="btn btn-primary btn-large br5" style="margin-top: 15px; width: 68%; padding: 17px 20px" onclick="goTo('<?= $User->login ?>/messages')">
	<span class="glyphicon glyphicon-send"></span>Анонимное сообщение
</button>
<?php
	}
?>

<div class="row sociallinks" style="margin-top: 15px">

<?php
		// Если не своя страница --  то выводим кнопку «Подписаться»
		if (!$own_page) {
	?>
			<a class="sociallink"
				
				ng-click="<?= User::loggedIn() ? "subscribe({$User->id})" : "notLoggedIn()"?>" rel="me"  ng-mouseenter="show = true" ng-mouseleave="show = false">
			<svg ng-hide="subscribed" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="200px" height="200px" viewBox="0 0 11.809 11.807" style="enable-background:new 0 0 11.809 11.807;" xml:space="preserve" xmlns:xml="http://www.w3.org/XML/1998/namespace">
<path class="SocialIconFill" d="  M5.904,0.108c3.2,0,5.796,2.596,5.796,5.796c0,3.2-2.596,5.795-5.796,5.795S0.108,9.104,0.108,5.904  C0.108,2.704,2.704,0.108,5.904,0.108L5.904,0.108z M2.518,4.839v2.194h2.256V9.29h2.194V7.033h2.256V4.839H6.968V2.583H4.773v2.256  H2.518z"/></svg><svg ng-show="subscribed" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="56px" height="56px" viewBox="45 15 126 115" xml:space="preserve" xmlns:xml="http://www.w3.org/XML/1998/namespace">
<path class="SocialIconFill" d="M162.18,41.592c-5.595-9.586-13.185-17.176-22.771-22.771c-9.588-5.595-20.055-8.392-31.408-8.392  c-11.352,0-21.822,2.797-31.408,8.392c-9.587,5.594-17.177,13.184-22.772,22.771C48.225,51.179,45.428,61.649,45.428,73  c0,11.352,2.798,21.82,8.392,31.408c5.595,9.585,13.185,17.176,22.772,22.771c9.587,5.595,20.056,8.392,31.408,8.392  c11.352,0,21.822-2.797,31.408-8.392c9.586-5.594,17.176-13.185,22.771-22.771c5.594-9.587,8.391-20.057,8.391-31.408  C170.57,61.648,167.773,51.178,162.18,41.592z M148.572,63.468l-44.239,44.239c-1.032,1.032-2.281,1.549-3.748,1.549  c-1.412,0-2.634-0.517-3.666-1.549L67.425,78.215c-0.977-0.979-1.466-2.199-1.466-3.666c0-1.521,0.488-2.771,1.466-3.749  l7.414-7.332c1.033-1.032,2.254-1.548,3.667-1.548s2.635,0.516,3.667,1.548l18.413,18.413l33.241-33.16  c1.032-1.032,2.254-1.548,3.666-1.548c1.411,0,2.635,0.516,3.666,1.548l7.414,7.333c0.979,0.977,1.467,2.226,1.467,3.747  C150.04,61.268,149.552,62.49,148.572,63.468z"/>
</svg></a><?php
	}
	?><div class="circle <?= ($page == "friends" ? "active" : "") ?>" ng-mouseenter="vfriends = true" ng-mouseleave="vfriends = false" onclick="goTo('<?= $User->login ?>/friends')"></span>
		<span><?= $User->friends ?></span>
	</div>
					<div class="circle <?= ($page == "subscribers" ? "active" : "") ?>" ng-mouseenter="followers = true" ng-mouseleave="followers = false" onclick="goTo('<?= $User->login ?>/subscribers')"><span>
					<?php
						// Анимация цифры подписки
						// Если не был подписан, то делаем +1
						// Если уже был подписан, то делаем -1 к цифре
						if ($subscribed) {
							echo '<span ng-hide="subscribed" class="animate-show-bounce">'.($User->subscribers - 1).'</span>';
							echo '<span ng-show="subscribed" class="animate-show-bounce">'.$User->subscribers.'</span>';
						} else {
							echo '<span ng-hide="subscribed" class="animate-show-bounce">'.$User->subscribers.'</span>';
							echo '<span ng-show="subscribed" class="animate-show-bounce">'.($User->subscribers + 1).'</span>';
						}
					?>					
				</span></div>
					
					<div class="circle <?= ($page == "subscriptions" ? "active" : "") ?>" ng-mouseenter="following = true" ng-mouseleave="following = false" onclick="goTo('<?= $User->login ?>/subscriptions')"><span><?= $User->subscriptions ?></span></div><?php
		// Если своя страница --  то выводим кнопку «Настройки» (редактировать профиль)
		if ($own_page) {
	?><a style="margin-left: 3px;" class="sociallink" href="profile/edit" rel="me"  ng-mouseenter="show = true" ng-mouseleave="show = false"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" > <path class="SocialIconFill" d="M 24.00,0.00C 10.746,0.00,0.00,10.746,0.00,24.00s 10.746,24.00, 24.00,24.00s 24.00-10.746, 24.00-24.00S 37.254,0.00, 24.00,0.00z M 39.00,27.00l-3.426,0.00 c-0.282,1.089-0.714,2.112-1.272,3.063l 2.427,2.424c 1.17,1.173, 1.17,3.072,0.00,4.245c-1.173,1.17-3.072,1.17-4.245,0.00l-2.424-2.427 C 29.112,34.86, 28.089,35.295, 27.00,35.574L27.00,39.00 c0.00,1.659-1.341,3.00-3.00,3.00s-3.00-1.341-3.00-3.00l0.00,-3.426 c-1.089-0.282-2.112-0.714-3.063-1.272 l-2.424,2.427c-1.173,1.17-3.072,1.17-4.242,0.00c-1.173-1.173-1.173-3.072,0.00-4.245l 2.424-2.424C 13.14,29.112, 12.705,28.089, 12.426,27.00L9.00,27.00 C 7.341,27.00, 6.00,25.659, 6.00,24.00s 1.341-3.00, 3.00-3.00l3.426,0.00 C 12.705,19.911, 13.14,18.888, 13.695,17.937L 11.274,15.516c-1.173-1.173-1.173-3.072,0.00-4.242 c 1.17-1.173, 3.069-1.173, 4.242,0.00l 2.424,2.424C 18.888,13.14, 19.911,12.705, 21.00,12.426L21.00,9.00 c0.00-1.659, 1.341-3.00, 3.00-3.00s 3.00,1.341, 3.00,3.00l0.00,3.426 c 1.089,0.282, 2.112,0.714, 3.063,1.272l 2.424-2.424c 1.173-1.173, 3.072-1.173, 4.245,0.00c 1.17,1.17, 1.17,3.069,0.00,4.242l-2.427,2.424 C 34.86,18.888, 35.295,19.911, 35.574,21.00L39.00,21.00 c 1.659,0.00, 3.00,1.341, 3.00,3.00S 40.659,27.00, 39.00,27.00z M 24.00,18.00C 20.688,18.00, 18.00,20.688, 18.00,24.00s 2.688,6.00, 6.00,6.00s 6.00-2.688, 6.00-6.00 S 27.312,18.00, 24.00,18.00z"/></svg></a>	
	<?php
		}
	?>

					<span ng-show="followers" class="chat center-content text-white animate-show-down"><span class="glyphicon glyphicon-refresh"></span>Подписчики</span>
					
					<span ng-show="following" class="chat center-content text-white animate-show-down"><span class="glyphicon glyphicon-repeat"></span>Подписки</span>
					
					<span ng-show="vfriends" class="chat center-content text-white animate-show-down"><span class="glyphicon glyphicon-user"></span>Друзья</span>
<!-- 					<h3 ng-show="following" class="foll center-content text-white badge-primary animate-show-down">Подписки</h3>						 -->
			</div>	
			
			
	<?php
		// Если не своя страница --  то выводим кнопку «Подписаться»
		if (!$own_page) {
	?>
			<span ng-show="show" class="chat center-content text-white animate-show-down">
				<span ng-hide="subscribed" class="animate-show-down"><span class="glyphicon glyphicon-plus"></span>Подписаться</span>
				<span ng-show="subscribed" class="animate-show"><span class="glyphicon glyphicon-ok"></span>Подписка оформлена</span>
			</span>
			
	<?php
		} else {
	?>
			<h3 ng-show="show" class="chat center-content text-white animate-show-down"><span class="glyphicon glyphicon-cog"></span>Настройки</h3>
	<?php
		}
	?>