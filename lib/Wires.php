<?php

/**
 * A class for auto-connecting server-rendered templates to the
 * Alpine.js framework, with their handlers automatically doubling
 * as an API endpoint.
 *
 * Usage:
 *
 * At the top of your handler, add this line:
 *
 *     Wires::init ($this);
 *
 * This injects the controller and initializes itself with the correct
 * settings for the current response type.
 *
 * Next, create an associative array of default values like you would
 * for a form submission:
 *
 *     $defaults = [
 *         'fname' => 'First',
 *         'lname' => 'Last'
 *     ];
 *
 * The last step in the handler is also similar to `Form::handle()`:
 *
 *     echo Wires::handle ($defaults, function ($res) {
 *         // Process as API request, if necessary modifying and returning $res
 *         // as a response object
 *         $res['lname'] = 'Doe';
 *         return $res;
 *     });
 *
 * In the view template, you can wire Alpine up with the following special tags:
 *
 *     <div {{_wire__}}>
 *         <p>{{lname}}, {{fname}}</p>
 *         <p>
 *             <input {{_wire_input_}} type="text name="fname" placeholder="First name..." />
 *             <button {{_wire_button_}} data-fname="First" data-lname="Last">Reset</button>
 *         </p>
 *     </div>
 */
class Wires {
	private static $controller;
	
	private static $is_wired = false;
	
	private static $request_uri = '';
	
	private static $error = '';
	
	private static $error_code = 0;
	
	private static $c = 1;
	
	/**
	 * Initialize the response settings and inject the controller.
	 */
	public static function init ($controller) {
		self::$controller = $controller;
		
		if (isset ($_GET['_wired_']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
			self::$controller->header ('Content-Type: application/json');
			self::$controller->page ()->layout = false;
			self::$is_wired = true;
		} else {
			self::$controller->page ()->add_script ('/apps/wires/js/alpine-2.4.1.min.js', 'defer');
		}
	}
	
	/**
	 * Renders the connection logic that wires the template to Alpine.js.
	 */
	private static function _setup ($state) {
		$out = self::$controller->template ()->render (
			'wires/setup',
			[
				'c' => self::$c,
				'state' => $state,
				'endpoint' => self::$request_uri
			]
		);

		self::$c++;

		return $out;
	}
	
	/**
	 * Use to return an error processing an API request.
	 *
	 *     return Wires::error (500, 'Internal server error');
	 */
	public static function error ($code, $msg) {
		self::$error_code = $code;
		self::$error = $msg;
		return false;
	}
	
	/**
	 * Use to process and either render the initial HTML response or call
	 * the API endpoint handler, depending on how the request was made.
	 */
	public static function handle ($defaults, $handler) {
		$params = self::$is_wired
			? array_merge ($defaults, json_decode (self::$controller->get_put_data (), true))
			: $defaults;
		
		if (self::$is_wired) {
			$res = $handler ($params);
			
			if ($res === false) {
				error_log ('Error (' . self::$error_code . '): ' . self::$error);
				self::$controller->header ('HTTP/1.1 ' . self::$error_code . ' ' . self::$error);
				return json_encode (['code' => self::$error_code, 'error' => self::$error]);
			} elseif (! $res) {
				$res = $params; // Unmodified but forgot to return
			}
			
			return json_encode ($res);
		}
		
		return self::_render (trim (self::$controller->uri, '/'), $params);
	}
	
	/**
	 * Handles rendering the template with the custom `Wires::filter`
	 * default filter.
	 */
	private static function _render ($template, $data) {
		self::$request_uri = $template;

		self::$controller->template ()->default_filter = 'Wires::filter';
		$out = self::$controller->template ()->render ($template, $data);
		self::$controller->template ()->default_filter = 'Template::sanitize';

		$out .= self::_setup ($data);

		return $out;
	}

	/**
	 * A custom template filter for initializing Alpine and connecting
	 * `{{var}}`-style tags to Alpine tags.
	 */
	public static function filter ($val, $charset, $label) {
		switch ($label) {
			case '_wire_':
				return 'x-data="_wire_' . self::$c . '()"';
			case '_wire_data_':
				return '_wire_' . self::$c . '()';
			case '_wire_input_':
				return 'x-on:input.debounce="handle($event)"';
			case '_wire_button_':
			case '_wire_link_':
				return 'x-on:click="handle($event)"';
			default:
				return '<span x-text="' . $label . '">' . Template::sanitize ($val, $charset, $label) . '</span>';
		}
	}
}
