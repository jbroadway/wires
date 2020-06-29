<?php

// Original Alpine.js demo: https://codepen.io/ryangjchandler/pen/qBOEgjg

$page->window_title = 'To Do List';
$page->layout = 'admin';
$this->require_admin ();

Wires::init ($this);

$page->add_style ('https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.2.0/tailwind.min.css');

$defaults = [
	'todos' => [
		(object) ['todo' => 'Item one', 'completed' => false],
		(object) ['todo' => 'Item two', 'completed' => false],
		(object) ['todo' => 'Item three', 'completed' => true]
	]
];

echo Wires::handle ($defaults, function ($res) {
	error_log (json_encode ($res));
	return $_POST;
});
