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

In your handler (`apps/myapp/handlers/example.php`):

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

Then in your accompanying view template (`apps/myapp/views/example.html`):

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

## Convenience methods

Wires defines two additional convenience methods for communicating with the server:

* `this.sync()` - Post the component's current state to the server-side handler.
* `this.send(params)` - Post the specified request data to the server-side handler.

The first serializes and sends the entire component state, while the second only sends
the specific data that it's given.

These can be used to send the updated component state to the server for storage and
additional processing after modifying it locally.

Example usage:

```js
function toDoList() {
	return Object.assign ({{_wire_data_}}, {
		toggleToDoCompleted(index) {
			this.todos[index].completed = !this.todos[index].completed;
			this.sync(); // Send updated state to the server
		}
	});
}
```

Using `this.send(params)`, you could also send just the item that changed, which you would
also need to check for on the server-side.

```js
function toDoList() {
	return Object.assign ({{_wire_data_}}, {
		toggleToDoCompleted(index) {
			this.todos[index].completed = !this.todos[index].completed;
			this.send({updated: index, todo: this.todos[index]});
		}
	});
}
```

## Demos

Once you've installed this app, go to `/wires/demos` in your Elefant CMS installation
and log in as a site administrator to see some working demos.

You can inspect the source code for the demos under
[apps/wires/handlers/demos](https://github.com/jbroadway/wires/tree/master/handlers/demos) and
[apps/wires/views/demos](https://github.com/jbroadway/wires/tree/master/views/demos)
in your Elefant CMS installation.
