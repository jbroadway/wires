<?php

$page->title = 'Wires Demo';
$page->layout = 'admin';
$this->require_admin ();

Wires::init ($this);

$defaults = [
	'name' => 'Joe',
	'age' => '27',
	'list' => ['One', 'Two', 'Three']
];

echo Wires::handle ($defaults, function ($res) {
	// Process API requests here
	$res['list'] = ['Four', 'Five'];
	$res['age'] = '34';
	
	// In case of errors
	// return Wires::error (500, 'Nope');

	return $res;
});
