<?php

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	die( "Hacking attempt!" );
}

$pspvolt_id = intval($_POST['pspvolt_id']);

$db->query( "UPDATE " . PREFIX . "_post set kp_id_movie='{$pspvolt_id}' where id = '{$item_db[0]}'" );

?>