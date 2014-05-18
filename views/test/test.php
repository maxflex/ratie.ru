<div class="row" ng-app="TestPage" ng-controller="TestCtrl">
	<h2 class="text-white">
		<div class="col-md-12 news-div">
			<button ng-click="getFriends()" class="btn btn-success">Замалымный</button>
			
			<div class='subscription-row' ng-repeat="friend in friends">
			 {{friend.id_vk}}
			</div>
		</div>
	</h2>
</div>
