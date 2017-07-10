<?php

return array(

	'user' => array(
		'title' => '使用者管理',
		'progs' => array(
			'users' => array(
				'path' => 'users',
				'title' => '使用者資料表'
			),
            'roles' => array(
                'path' => 'roles',
                'title' => '角色資料表'
            ),
			'users_history' => array(
				'path' => 'userhistories',
				'title' => '操作記錄'
			)
		)
	),
	'system' => array(
		'title' => '系統與程式管理',
		'progs' => array(
			'system' => array(
				'path' => 'system',
				'title' => '系統資料表'
			),
			'progs' => array(
				'path' => 'progs',
				'title' => '程式資料表'
			),
			'announcements' => array(
				'path' => 'announcements',
				'title' => '公告資料表'
			),
			'menus' => array(
				'path' => 'menus',
				'title' => '目錄資料表'
			)
		)
	),
	'post' => array(
		'title' => '文章管理',
		'progs' => array(
			'posts' => array(
				'path' => 'posts',
				'title' => '文章資料表'
			),
			'post_histories' => array(
				'path' => 'post_histories',
				'title' => '文章編修紀錄'
			),
			'post_sources' => array(
				'path' => 'sources',
				'title' => '文章來源'
			),
			'post_categories' => array(
				'path' => 'categories',
				'title' => '文章分類'
			),
			'authors' => array(
				'path' => 'authors',
				'title' => '作者管理'
			)
		)
	),
	'banner' => array(
		'title' => '跑馬燈管理',
		'progs' => array('carousel' => array(
				'path' => 'carousel',
				'title' => '跑馬燈資料表'
			))
	),
	'other1' => array(
		'title' => '推播管理',
		'progs' => array(
			'notification' => array(
				'path' => 'notification',
				'title' => '推播資料表'
			),
			'notification_hits' => array(
				'path' => 'notification_hits',
				'title' => '推播成效資料表'
			)
		)
	),
	'other2' => array(
		'title' => '直播管理',
		'progs' => array(
			'live' => array(
				'path' => 'live',
				'title' => '直播資料表'
			),
			'live_hits' => array(
				'path' => 'live_hits',
				'title' => '直播成效資料表'
			)
		)
	),
);
