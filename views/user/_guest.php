<div ng-repeat="adj in adjectives | orderBy:['_ang_new_order', '_ang_order', 'id']:true"   class="adjective-row animate-repeat">
				
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
			</div>
			<!-- КОНЕЦ ПАЛЫМ -->
			
			
			<!-- БАР-ПРИЛАГАТЕЛЬНОГО -->
			<div class="adjective-bar progress-primary" style="margin: 2px 0 20px">
				<div id="bar-{{adj.id}}" class="bar" style="width: {{adj._ang_pos_percent}}%"></div>
			</div>
		</div>
</div>