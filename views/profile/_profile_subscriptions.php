<div class="col-md-12 news-div animate-show-right" ng-show="subs">
	<div class="animate-show" ng-hide="sub_watch">
	<?php
		// Получаем подписки пользователя
		$Subscriptions = $User->Subscriptions();
		
		// Если подписок нет, выводим сообщение об этом
		if (!$Subscriptions) {
			echo "<h3 class='trans center-content text-white badge-success animate-show mg-top'>"
				 ."<span class='glyphicon glyphicon-user'></span>Вы ни на кого не подписаны</h3>"
				 ."<fieldset class='hidden-thoughts'>"
				 	."<legend class='text-white' ng-click='getFriends()'>ПОДПИСАТЬСЯ НА СВОИХ ДРУЗЕЙ</legend></fieldset>";
		} else {
		// Иначе отображаем подписки
			
			// echo '<h2 class="center-t text-white" style="margin-bottom: 20px">Мои подписки: </h2>';
			
			foreach ($Subscriptions as $Subscription) {
				// Получаем пользователя, на которого подписаны
				$SubscriptionUser = User::findById($Subscription->id_user);
				
				echo "<div class='subscription-row' ng-click='showNews({$Subscription->id_user}, {$Subscription->id_last_seen_news})'>";
				
				// Выводим аву
				/*createUrl(array(
					"controller"=> $SubscriptionUser->login,
					"text"		=> '<div style="background-image: url('.$SubscriptionUser->avatar.')" class="news-ava '.($SubscriptionUser->stretch ? "stretch" : "").'"></div>',
				)); 
				/*
				createUrl(array(
					"controller"	=> $SubscriptionUser->login,
					"text"			=> $SubscriptionUser->login,
					"htmlOptions"	=> array(
							"class"	=> "login-link",
					),
				));*/
				
				echo '<div style="background-image: url('.$SubscriptionUser->avatar.')" class="news-ava '.($SubscriptionUser->stretch ? "stretch" : "").'"></div>';
				echo '<span>'.$SubscriptionUser->getName().'</span>';
				echo '<span class="subscription-arrow glyphicon glyphicon-chevron-right pull-right"></span>';
				echo "</div>";
			}
			
			echo "<div class='voteme-hint' style='margin-top: 20px'>Нажмите на пользователя, чтобы просмотреть его новости</div>";
			
			// Переподключаемся к основному пользователю
			$User->initConnection();
		}
		
		
	?>
	</div>
	
	<span class="sub-back arrow-back glyphicon glyphicon-chevron-left pull-left animate-show-right" ng-click="sub_watch = false" ng-show="sub_watch"></span>
	<div class="sub-news-container animate-show-right" ng-show="sub_watch">
		
	</div>
</div>