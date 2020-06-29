# Wires

Inspired by [Laravel Livewire](https://laravel-livewire.com/), this app provides
simple template extensions for the [Elefant CMS](https://www.elefantcms.com/) that
wires any handler and its view template up to [Alpine.js](https://github.com/alpinejs/alpine),
providing a few interesting features:

* Server-side rendering of the initial request
* Converts Elefant's `{{var}}` tags to Alpine.js `<span x-text="var">` tags
* Provides automatic server-side callbacks via a handful of custom template tags

## Installation

Via composer:

```bash
composer require elefant/app-wires
```

## Usage

Here's how it works:

In your handler (`apps/myapp/handlers/demo.php`):

```php
<?php

// Initialize and inject the controller
Wires::init ($this);

// Set your default values here
$defaults = [
	'fname' => 'First',
	'lname' => 'Last'
];

// Handle the request, providing an API callback handler
echo Wires::handle ($defaults, function ($response) {
	// Process API requests here
});
```

Then in your accompanying view template (`apps/myapp/views/demo.html`):

```html
<div {{_wire_}}>
	<p>{{lname}}, {{fname}}</p>
	
	<p>
		<button {{_wire_button_}} data-fname="John" data-lname="Doe">Doe, John</button>
		<button {{_wire_button_}} data-fname="First" data-lname="Last">Reset</button>
	</p>
</div>
```

Alternately, you can modify the response data on the server-side before returning it:

```php
echo Wires::handle ($defaults, function ($response) {
	$response['fname'] = 'Jane';
	$response['lname'] = 'Doe';
	return $response;
});
```

And in case of an error on the server, you can use:

```php
echo Wires::handle ($defaults, function ($response) {
	return Wires::error (500, 'Nope');
});
```

Behind the scenes, the `<div {{_wire_}}>` connects Alpine.js to the template data.

If you want to define your own functions for use with Alpine.js, you can initialize
the component like this instead, using the `{{_wire_data_}}` tag:

```html
<div x-data="myComponent()">
	<p x-text="name()"></p>
</div>

<script>
function myComponent() {
	return Object.assign ({{_wire_data_}}, {
		name() {
			return this.fname + ' ' + this.lname;
		}
	});
}
</script>
```

In addition to `{{_wire_}}` and `{{_wire_data_}}`, three custom tags can be used to
connect buttons, inputs, and links to make API calls to the same endpoint:

* `{{_wire_button_}}` - Wires a `<button>` element, passing its `data-*` attributes to the server.
* `{{_wire_input_}}` - Wires an `<input>`, `<select>`, or `<textarea>` element to send live updates to the server as you type or make a selection, passing its `name` attribute and value to the server.
* `{{_wire_link_}}` - Wires an `<a>` link, passing its `data-*` attributes to the server.

Other features of Alpine.js should work just the same too, although loops should use
pure Alpine.js instead of Elefant's template syntax for looping. For example:

```php
$default = [
	'list' => ['One', 'Two', 'Three']
];
```

Can be connected like this:

```html
<ul>
	<template x-for="item in list">
		<li x-text="item"></li>
	</template>
</ul>
```

Modifying the list on either the client or the server will update the displayed list of elements.

## Demo

Once you've installed this app, go to `/wires/demo` and log in as a site administrator to see a working demo.
