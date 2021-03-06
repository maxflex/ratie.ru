<!-- СПИСОК ПРИЛАГАТЕЛЬНЫХ -->	
<div ng-repeat="adj in adjectives | filter: {_ang_hidden : 0} | orderBy:order_expression:true"   class="adjective-row animate-repeat">
					
	<span class="adjective-toptext">{{adj._ang_adjective}}</span>
	
	<!-- ПАЛЬЦЫ ВВЕРХ/ВНИЗ -->
	<div class="adj-vote-block pull-right trans">
		<div class="voting">
			<span class="glyphicon glyphicon-thumbs-up text-white"></span>
			<span class="vote-count">{{adj._ang_pos_count}}</span>
		</div>
		
		<div class="voting">
			<span class="glyphicon glyphicon-thumbs-down text-white"></span>
			<span class="vote-count">{{adj._ang_neg_count}}</span>
		</div>
		
		<div class="voting">
					<span ng-click="comment(adj.id)" class="glyphicon glyphicon-comment text-white trans-h"></span>
					<span class="vote-count">{{adj._ang_comment_count}}</span>
		</div>
	
	</div>
	
	<!-- БАР-ПРИЛАГАТЕЛЬНОГО -->
	<div class="adjective-bar progress-primary" style="margin: 2px 0 20px">
		<div ng-hide="adj.hidden" class="eye pointer glyphicon glyphicon-eye-open animate-show-bounce bar-eye trans-h" 
			ng-click="hide(adj)" 
			ng-class="{'blue' : (adj._ang_pos_percent <= 92)}"></div>
		<div ng-show="adj.hidden" class="eye pointer glyphicon glyphicon-eye-close animate-show-bounce bar-eye trans-h" 
			ng-click="hide(adj)" 
			ng-class="{'blue' : (adj._ang_pos_percent <= 92)}"></div>
		<span class="flaticon-more more-dots trans-h" ng-click="moreInfo(adj.id)" ng-class="{'blue' : (adj._ang_pos_percent <= 94)}"></span>
		<div id="bar-{{adj.id}}" class="bar" style="width: {{adj._ang_pos_percent}}%"></div>
	</div>
	</div>
</div>


<!-- СПИСОК СКРЫТЫХ ПРИЛАГАТЕЛЬНЫХ -->

<?php
	// Если есть скрытые прилагательные
	if ($hidden_count) {
	?>
	<fieldset class="hidden-thoughts" id="hidden-thoughts-button">
	    <legend class="text-white" ng-click="showHidden()">{{ !show_hidden && 'Показать скрытые мнения: <?= $hidden_count ?>' || 'Свернуть скрытые мнения' }}</legend>
	</fieldset>​
<div class="animate-up-down" ng-show="show_hidden" id="hidden-thoughts">
	<div ng-repeat="adj in adjectives | filter: {_ang_hidden : 1} | orderBy:order_expression:true"   class="adjective-row animate-repeat trans">
						
		<span class="adjective-toptext">{{adj._ang_adjective}}</span>
		
		<!-- ПАЛЬЦЫ ВВЕРХ/ВНИЗ -->
		<div class="adj-vote-block pull-right trans">
			<div class="voting">
				<span class="glyphicon glyphicon-thumbs-up text-white"></span>
				<span class="vote-count">{{adj._ang_pos_count}}</span>
			</div>
			
			<div class="voting">
				<span class="glyphicon glyphicon-thumbs-down text-white"></span>
				<span class="vote-count">{{adj._ang_neg_count}}</span>
			</div>
			
			<div class="voting">
					<span ng-click="comment(adj.id)" class="glyphicon glyphicon-comment text-white trans-h"></span>
					<span class="vote-count">{{adj._ang_comment_count}}</span>
			</div>
		</div>
		
		<!-- БАР-ПРИЛАГАТЕЛЬНОГО -->
		<div class="adjective-bar progress-primary" style="margin: 2px 0 20px">
			<div ng-hide="adj.hidden" class="eye pointer glyphicon glyphicon-eye-open animate-show-bounce bar-eye trans-h" 
				ng-click="hide(adj)" 
				ng-class="{'blue' : (adj._ang_pos_percent <= 92)}"></div>
			<div ng-show="adj.hidden" class="eye pointer glyphicon glyphicon-eye-close animate-show-bounce bar-eye trans-h" 
				ng-click="hide(adj)" 
				ng-class="{'blue' : (adj._ang_pos_percent <= 92)}"></div>
			<span class="flaticon-more more-dots trans-h" ng-click="moreInfo(adj.id)" ng-class="{'blue' : (adj._ang_pos_percent <= 94)}"></span>
			<div id="bar-{{adj.id}}" class="bar" style="width: {{adj._ang_pos_percent}}%"></div>
		</div>
		</div>
	</div>	
</div>
	<?php	
	}
	// Если заходит в первый раз, показать друзей на Ratie
	if ($show_friends) {
		// Отображаем список друзей, которые уже есть на Ratie (если пользователь заходит в первый раз)
		globalPartial("friends_on_ratie");
	}
	?>
