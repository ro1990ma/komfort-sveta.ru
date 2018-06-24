<?php

$shost = substr(strtoupper(md5( 'mp' . md5('komfort-sveta.ru') . 'poster' )), 0, 29);
$real_key = substr( $shost, 0, 5 ) . '-' . substr( $shost, 6, 5 ) . '-' . substr( $shost, 12, 5 ) . '-' . substr( $shost, 18, 5 ) . '-' . substr( $shost, 24, 5 );

echo $real_key;