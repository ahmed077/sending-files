<?php
	if($_SERVER['REQUEST_METHOD']==='POST') {
		$searchWord = $_POST['word'];
		$searchType = $_POST['type'];
		$x = shell_exec('python test.py -query=' . $searchWord . " -type=" . $searchType);
		var_dump(json_decode($x, true));
	} else {
		header('location:search.html');
		exit();
	}