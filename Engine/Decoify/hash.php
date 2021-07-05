<?php
/**
 * We just want to hash our password using the current DEFAULT algorithm.
 * This is presently BCRYPT, and will produce a 60 character result.
 *
 * Beware that DEFAULT may change over time, so you would want to prepare
 * By allowing your storage to expand past 60 characters (255 would be good)
 */

$hash = password_hash("changepassword", PASSWORD_DEFAULT);

echo $hash;

if(password_verify($_GET['password'], $hash)){
	echo "correct password";
}
else{
	echo "wrong password";
}
?>