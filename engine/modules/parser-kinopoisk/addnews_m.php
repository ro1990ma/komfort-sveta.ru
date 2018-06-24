<?php

$pspvolt_id = intval($_POST['pspvolt_id']);

$db->query( "UPDATE " . PREFIX . "_post set kp_id_movie='{$pspvolt_id}' where id = '{$row['id']}'" );

?>