<?php

$page->title = 'Wires Demo: Random Names';
$page->layout = 'admin';
$this->require_admin ();

Wires::init ($this);

$defaults = [
	'fname' => '',
	'lname' => '',
	'last_index' => -1
];

echo Wires::handle ($defaults, function ($response) {
	$names = [
		['fname' => 'David', 'lname' => 'Bowie'],
		['fname' => 'Janis', 'lname' => 'Joplin'],
		['fname' => 'Freddie', 'lname' => 'Mercury'],
		['fname' => 'Joni', 'lname' => 'Mitchell'],
		['fname' => 'Jimi', 'lname' => 'Hendrix'],
		['fname' => 'Debbie', 'lname' => 'Harry'],
		['fname' => 'Elton', 'lname' => 'John']
	];
	
	$index = mt_rand (0, count ($names) - 1);
	while ($index == $response['last_index']) {
		$index = mt_rand (0, count ($names) - 1);
	}
	
	$response['fname'] = $names[$index]['fname'];
	$response['lname'] = $names[$index]['lname'];
	$response['last_index'] = $index;

	return $response;
});
