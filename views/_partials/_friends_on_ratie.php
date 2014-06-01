<div ng-app="FriendsPage" ng-controller="FriendsCtrl" id="friends" class="col-md-6 news-div" style="width: 50%; display: none">
    <div style="max-height: 150px; overflow-y: auto; text-align: center">
        <div ng-repeat="friend in friends" style="display: inline-block">
            <a target="_blank" href="{{friend.login}}">
            	<div style="background-image: url({{friend.avatar}})" class="ava-60" ng-class="{stretch : (friend.stretch == 1)}"></div>
            </a>
        </div>
    </div>
</div>
