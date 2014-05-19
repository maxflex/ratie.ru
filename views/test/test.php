<div class="row" ng-app="TestPage" ng-controller="TestCtrl">
	
		<div class="col-md-6">
			<button ng-click="getFriends()" class="btn btn-success">Замалымный</button>
		</div>
		
		
		<div id="friends" class="col-md-6 news-div" style="width: 50%">
			<div class='subscription-row' ng-repeat="friend in friends">
				<a href="{{friend.login}}"><div style="background-image: url({{friend.avatar}})" class="news-ava" ng-class="{stretch : friend.stretch}"></div></a><a class="login-link" href="{{friend.login}}">{{friend.first_name}} {{friend.last_name}}</a>
				<span class="subscription-arrow glyphicon glyphicon-chevron-right pull-right"></span>
			</div>
		</div>
</div>
