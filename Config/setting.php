<?php
$config['BcSquare'] = [
    "environment" => [
        1 => "本番環境",
        2 => "開発環境",
    ],
];



$config['BcApp.adminNavigation'] = [
	'Plugins' => [
		'menus' => [
			'BcSquareConfigs' => [
				'title' => 'BcSquare設定',
				'url' => ['plugin' => 'bc_square', 'controller' => 'bc_square_configs', 'action' => 'index']
			],
		]
	]
];
