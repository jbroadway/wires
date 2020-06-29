<?php

$page->title = 'Wires Demos';
$page->layout = 'admin';
$this->require_admin ();

echo $tpl->render ('wires/demos');
