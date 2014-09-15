<?php
//	preType($_SERVER["QUERY_STRING"]);
	
?>
<div class="container" ng-app="UserCommentsPage" id="user-comments-page" ng-controller="UserCommentsCtrl" ng-init="
	subscribed		= <?= (!empty($subscribed) ? $subscribed : 0) ?>; 
	id_adjective 	= <?= $id_adjective ?>; 
	id_viewing		= <?= $id_viewing ?>;
	own_page		= <?= (!empty($own_page) ? $own_page : 0) ?>;
	have_messages	= <?= ($have_messages ? 1 : 0) ?>;
	<?= angInit("commentator", $LoggedUser )?>" 
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
			<!-- ДРУЗЬЯ/ПОДПИСЧИКИ/ПОДПИСКИ -->
			<?php
				globalPartial("buttons", array(
					"User" 			=> $User,
					"own_page"		=> $own_page,
					"subscribed"	=> $subscribed,
					"no_message_button"	=> ($id_adjective ? false : true),
				));
			?>
			
				
		</div>
		
		<div class="col-md-7">
			<div class="row">
				<div class="clearfix">
					<h2 class="trans text-white">
						<?php
							if (!$Adjective) {
								echo "Открытая беседа";
								if ($own_page) {
									echo '<button class="btn br5 btn-success pull-right" ng-click="offerChat()" style="margin-top: 4px"><span class="glyphicon glyphicon-plus" style="margin: 0"></span></button>';
								}
							} else {
								echo "Комментарии к «{$Adjective->adjective}»:";
							}
						?>
					</h2>

					<div class="comment-row">
						<div style="background-image: url(<?= $LoggedUser->avatar ?>)" class="ava-60 pull-left <?=($LoggedUser->stretch ? "stretch" : "")?>"></div>
						<input id="comment-input" type="text" class="comments-box" ng-model="comment" ng-keydown="watchEnter($event)" placeholder="<?= $Adjective ? "А что думаешь ты?" : (($LoggedUser->id && !$LoggedUser->anonymous) ? "Ваше публичное сообщение здесь" : "Ваше анонимное сообщение здесь") ?>"><button class="btn btn-success comment-button" ng-click="leaveComment()">
						<span ng-hide="id_adjective" class='glyphicon glyphicon-send' style="font-size: 25px"></span>
						<span ng-show="id_adjective">ОК</span>
						</button> 
					</div>
				</div>
			</div>
			
			<h3 class="trans center-content text-white badge-success animate-show mg-top" 
				ng-show="<?= $Adjective ? "!comments" : "!have_messages" ?>">
					<span class='glyphicon glyphicon-comment'></span>
					<?= $Adjective ? "Ваш комментарий будет первым" : "Ваше сообщение будет первым" ?>
			</h3>
			
			<?php
				// Если это страница сообщений, то выводить «Сообщения загружаются..»
				if (!$Adjective->id) {
					?>
					<div class="loading-messages" ng-hide="comments_loaded || !have_messages">
						<img src="img/loader/spinner.gif">Загрузка сообщений
					</div>
					<?php
				}
			?>
			
			<div ng-init="<?= ( $Adjective ? angInit("comments", $Comments) : "" )?>">
				<div ng-repeat="comment in comments | <?= ( $Adjective ? "orderBy:['order', 'id']:true" : "orderByPriority | reverse" ) ?>" 
					 ng-class="{'animate-repeat' : (comment.order > 0)}" style="clear: both">
					<a ng-if="comment._ang_login" target="_blank" href="{{comment._ang_login}}"><div style="background-image: url({{comment._ang_ava}})" class="ava-60 comment-ava" ng-class="{
					stretch : comment._ang_stretch,  
					right : comment.id_user == (commentator.id != 0 ? commentator.id : <?= $User->id ?>),
					deleted: comment.deleted}"></div></a>
					
					<div ng-if="!comment._ang_login" style="background-image: url('img/profile/noava.png')" class="ava-60 comment-ava cursor-default"
					ng-class="{deleted: comment.deleted}"></div>
					
					<div class="bubble" ng-class="{
						right : comment.id_user == (commentator.id != 0 ? commentator.id : <?= $User->id ?>),
						active : (comment.order > 0),
						deleted: comment.deleted
					}">
						<span class="glyphicon glyphicon-remove icon remove" ng-show="((own_page || ((comment.id_user == commentator.id) && comment.id_user!=0)) && !comment.deleted)" ng-click="deleteComment(comment)"></span>
						<span class="glyphicon glyphicon-ok icon restore" ng-show="comment.deleted" ng-click="restoreComment(comment)"></span>
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
