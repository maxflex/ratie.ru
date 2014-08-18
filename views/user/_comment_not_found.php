<?php
//	preType($_SERVER["QUERY_STRING"]);
	
?>
<div class="container" ng-app="UserCommentsPage" id="user-comments-page" ng-controller="UserCommentsCtrl" ng-init="
	subscribed		= 0; 
	own_page		= <?= (!empty($own_page) ? $own_page : 0) ?>;
	" 
	style="margin-bottom: 35px">
	<h1 id="name-lastname"><?=$User->first_name." ".$User->last_name?>
		<span ng-hide="(own_page && !id_adjective)" class="arrow-back big glyphicon glyphicon-chevron-left pull-left path-icon clickable left" onclick="goBack()"></span>
	</h1>
	<hr>
	
	<div class="row effect2">
		<div class="col-md-4 center-content">
			<a href="<?= $User->login ?>">
				<?php globalPartial("ava", array("User" => $User)) ?>
			</a>
			<!-- СОЦИАЛЬНЫЕ КНОПКИ -->
			<?php partial("social", array("social" => $User->social, "own_page" => $own_page, "id_user" => $User->id, "user_login" => $User->login)) ?>
				
		</div>
		
		<div class="col-md-7">
			<div class="badge badge-important center-t" style="margin-top: 22%; width: 100%">
				<h2 class="text-white" style="font-family: 'RaleWayRegular'">
				<span class="glyphicon glyphicon-remove" style="top: 4px"></span> Мнение не найдено
				</h2>
			</div>
		</div>
		
	</div>
</div>
