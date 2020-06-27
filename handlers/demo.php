<?php

$page->title = 'Wires Demo';

$page->layout = 'admin';

$this->require_admin ();

Wires::init ($this);

$defaults = [
	'name' => 'Joe',
	'list' => ['One', 'Two', 'Three']
];

echo Wires::handle ($defaults, function ($res) {
	// Process subsequent API requests
	$res['list'] = ['Four', 'Five'];
	
	//return Wires::error (500, 'Nope');

	return $res;
});
