<div class="row" ng-app="TestPage" ng-controller="TestCtrl">
	
		<div class="col-md-6">
			<button ng-click="getFriends()" class="btn btn-success">Замалымный</button>
		</div>
		
		
		<div id="friends" class="col-md-6 news-div" style="width: 50%; display: none">
		 	<div style="max-height: 150px; overflow-y: auto; text-align: center">
			<div ng-repeat="friend in friends" style="display: inline-block">
				<a target="_blank" href="{{friend.login}}"><div style="background-image: url({{friend.avatar}})" class="ava-60" ng-class="{stretch : friend.stretch}"></div></a><!-- <a class="login-link" href="{{friend.login}}">{{friend.first_name}} {{friend.last_name}}</a> -->
			</div>
		 	</div>
		</div>
</div>
