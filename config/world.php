<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Supported locales.
	|--------------------------------------------------------------------------
	*/
	"accepted_locales" => [
		"ar",
		"bn",
		"br",
		"de",
		"en",
		"es",
		"fr",
		"ja",
		"kr",
		"nl",
		"pl",
		"pt",
		"ro",
		"ru",
		"zh",
	],
	/*
	|--------------------------------------------------------------------------
	| Enabled modules.
	| The city module depends on the state module.
	|--------------------------------------------------------------------------
	*/
	"modules" => [
		"states" => true,
		"cities" => true,
		"timezones" => true,
		"currencies" => true,
		"languages" => true,
	],
	/*
	|--------------------------------------------------------------------------
	| Routes.
	|--------------------------------------------------------------------------
	*/
	"routes" => true,
	/*
	|--------------------------------------------------------------------------
	| Migrations.
	|--------------------------------------------------------------------------
	*/
	"migrations" => [
		"countries" => [
			"table_name" => "countries",
			"optional_fields" => [
				"phone_code" => [
					"required" => true,
					"type" => "string",
					"length" => 5,
				],
				"iso3" => [
					"required" => true,
					"type" => "string",
					"length" => 3,
				],
				"native" => [
					"required" => true,
					"type" => "string",
				],
				"region" => [
					"required" => true,
					"type" => "string",
				],
				"subregion" => [
					"required" => true,
					"type" => "string",
				],
				"latitude" => [
					"required" => false,
					"type" => "string",
				],
				"longitude" => [
					"required" => false,
					"type" => "string",
				],
				"emoji" => [
					"required" => false,
					"type" => "string",
				],
				"emojiU" => [
					"required" => false,
					"type" => "string",
				],
			],
		],
		"states" => [
			"table_name" => "states",
			"optional_fields" => [
				"country_code" => [
					"required" => false,
					"type" => "string",
					"length" => 10,
				],
				"state_code" => [
					"required" => true,
					"type" => "string",
					"length" => 10,
				],
				"latitude" => [
					"required" => false,
					"type" => "string",
				],
				"longitude" => [
					"required" => false,
					"type" => "string",
				],
			],
		],
		"cities" => [
			"table_name" => "cities",
			"optional_fields" => [
				"country_code" => [
					"required" => false,
					"type" => "string",
					"length" => 10,
				],
				"state_code" => [
					"required" => false,
					"type" => "string",
					"length" => 10,
				],
				"latitude" => [
					"required" => false,
					"type" => "string",
				],
				"longitude" => [
					"required" => false,
					"type" => "string",
				],
			],
		],
		"timezones" => [
			"table_name" => "timezones",
		],
		"currencies" => [
			"table_name" => "currencies",
		],
		"languages" => [
			"table_name" => "languages",
		],
	],
];
