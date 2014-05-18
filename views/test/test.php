<div class="row">
	<h2 class="text-white">
		<div class="col-md-12">
<?php

	$User = User::findById(User::fromSession(false)->id);
	
	
	echo var_dump($User->social);
	
	
	// preType(unserialize($User->social));
//	$social = $User->social;
	/*
	$User->social = array();
	
	$User->social["twitter"]	= "mkolyadin";
	$User->social["vk"]			= "mkolyaidn";
	$User->social["instagram"]	= "makolyadin";
	
	$User->save();
	
	preType($User);
//	preType(unserialize($social));	

?>
		</div>
	</h2>
</div>
