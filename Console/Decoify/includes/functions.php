<?php
	function isAuthorized($user){
		return ($user['role'] === 'admin' || $user['role'] === 'user');
	}
	function isAdmin($user){
		return $user['role'] === 'admin';
	}
?>