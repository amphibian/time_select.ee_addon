<?php

include(PATH_THIRD.'/time_select/config.php');
return array(
	'author' => 'Amphibian',
	'author_url' => 'http://amphibian.info',
	'description' => 'A simple custom field for selecting the time.',
	'docs_url' => 'https://github.com/amphibian/time_select.ee_addon',
	'fieldtypes' => array(
		'time_select' => array(
			'name' => 'Time Select',
			'compatibility' => 'date'
		)
	),
	'name' => 'Time Select',
	'namespace' => 'Amphibian\TimeSelect',
	'version' => TIME_SELECT_VERSION
);