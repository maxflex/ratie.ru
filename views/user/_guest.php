<!-- СПИСОК ПРИЛАГАТЕЛЬНЫХ -->
<div ng-repeat="adj in adjectives | orderBy:order_expression:true"   class="adjective-row animate-repeat">
				
			<span class="adjective-toptext">{{adj._ang_adjective}}</span>
			
			<!-- ПАЛЬЦЫ ВВЕРХ/ВНИЗ -->
			<div class="adj-vote-block pull-right trans">
				<div class="voting">
					<span ng-click="vote(adj, 1)" class="glyphicon glyphicon-thumbs-up text-white adj-vote for {{adj._ang_pos}}" id="vote-for-{{adj.id}}"></span>
					<span class="vote-count" id="vote-for-count-{{adj.id}}">{{adj._ang_pos_count}}</span>
				</div>
				
				<div class="voting">
					<span ng-click="vote(adj, 0)" class="glyphicon glyphicon-thumbs-down text-white adj-vote against {{adj._ang_neg}}" id="vote-against-{{adj.id}}"></span>
					<span class="vote-count" id="vote-against-count-{{adj.id}}">{{adj._ang_neg_count}}</span>
				</div>
				
				<div class="voting">
					<span ng-click="comment(adj.id)" class="glyphicon glyphicon-comment text-white trans-h"></span>
					<span class="vote-count">{{adj._ang_comment_count}}</span>
				</div>
			</div>
			<!-- КОНЕЦ ПАЛЫМ -->
			
			<!-- БАР-ПРИЛАГАТЕЛЬНОГО -->
			<div class="adjective-bar progress-primary" style="margin: 2px 0 20px">
				<span class="flaticon-more more-dots trans-h" ng-click="moreInfo(adj.id)" ng-class="{'blue' : (adj._ang_pos_percent <= 94)}"></span>
				<div id="bar-{{adj.id}}" class="bar" style="width: {{adj._ang_pos_percent}}%"></div>
			</div>
		</div>
</div>

<?php
	// Инициализация сообщений, которые показываются только один раз после регистрации 
	// (о публичности или анонимности мнения)
	if (!empty($_intro_for_anonymous_or_public)) {
		echo "<div ng-init=\"intro_message = $_intro_for_anonymous_or_public; user_name = '$user_name'\"></div>";
	}
?>