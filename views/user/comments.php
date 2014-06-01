<?php
//	preType($_SERVER["QUERY_STRING"]);
	
?>
<div class="container" ng-app="UserCommentsPage" id="user-comments-page" ng-controller="UserCommentsCtrl" ng-init="subscribed = <?= (!empty($subscribed) ? $subscribed : 0) ?>; id_adjective = <?= $id_adjective ?>; <?= angInit("commentator", $LoggedUser )?>" style="margin-bottom: 35px">
	<h1 id="name-lastname"><?=$User->first_name." ".$User->last_name?>
		<span class="arrow-back big glyphicon glyphicon-chevron-left pull-left path-icon clickable left" onclick="goBack()"></span>
	</h1>
	<hr>
	
	<div class="row effect2">
		<div class="col-md-4 center-content">
			<a href="<?= $User->login ?>">
				<?php globalPartial("ava", array("User" => $User)) ?>
			</a>
			<!-- СОЦИАЛЬНЫЕ КНОПКИ -->
			<?php partial("social", array("social" => $User->social, "own_page" => $own_page, "id_user" => $User->id)) ?>
				
		</div>
		
		<div class="col-md-7">
			<div class="row">
				<div class="clearfix">
					<h2 class="trans text-white">
						Комментарии к «<?= $Adjective->adjective ?>»:
					</h2>

					<div class="comment-row">
						<div style="background-image: url(<?= $LoggedUser->avatar ?>)" class="ava-60 pull-left <?=($LoggedUser->stretch ? "stretch" : "")?>"></div>
						<input type="text" class="comments-box" ng-model="comment" ng-keydown="watchEnter($event)" placeholder="А что думаешь ты?"><button class="btn btn-success comment-button" ng-click="leaveComment()">OK</button> 
					</div>
				</div>
			</div>
			
			<h3 class="trans center-content text-white badge-success animate-show mg-top" ng-show="!comments">
					<span class='glyphicon glyphicon-comment'></span>Ваш комментарий будет первым
			</h3>
			
			<div ng-init="<?= angInit("comments", $Comments) ?>">
				<div ng-repeat="comment in comments | orderBy:['order', 'id']:true" class="animate-repeat" style="clear: both">
					<a ng-if="comment._ang_login" target="_blank" href="{{comment._ang_login}}"><div style="background-image: url({{comment._ang_ava}})" class="ava-60 comment-ava" ng-class="{stretch : comment._ang_stretch,  right : comment.id_user == (commentator.id != 0 ? commentator.id : <?= $User->id ?>)}"></div></a>
					
					<div ng-if="!comment._ang_login" style="background-image: url('img/profile/noava.png')" class="ava-60 comment-ava cursor-default"></div>
					
					<div class="bubble" ng-class="{
						right : comment.id_user == (commentator.id != 0 ? commentator.id : <?= $User->id ?>),
						active : (comment.order > 0)
					}">
						{{comment.comment}}
					</div>
				</div>
			</div>
		
	</div>
	
<?php
	// Инициализация сообщений, которые показываются только один раз после регистрации 
	// (о публичности или анонимности мнения)
	if (!empty($_intro_for_anonymous_or_public)) {
		echo "<div ng-init=\"intro_message = $_intro_for_anonymous_or_public; user_name = '".$User->first_name."'\"></div>";
	}
?>
</div>
