<?php

// Подключаем класс дляр аботы с API HDLight
require_once ENGINE_DIR . '/classes/hdlight.class.php';
$hdlight = new hdlight($config, $db, $tpl);
