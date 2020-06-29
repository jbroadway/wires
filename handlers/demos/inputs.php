<?php

$page->title = 'Wires Demo: Inputs';
$page->layout = 'admin';
$this->require_admin ();

Wires::init ($this);

$defaults = [
	'name' => 'Joe',
	'age' => '27'
];

echo Wires::handle ($defaults, function ($res) {
	if (isset ($res['random'])) {
		$res['age'] = mt_rand (18, 72);
		return $res;
	}
});
