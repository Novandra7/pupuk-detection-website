<?php

// Define permissions name and permissions group with snake case
return [
    [
        'group_name' => 'user',
        'permissions' => [
            'browse',
            'create',
            'update',
            'delete',
        ]
    ],
    [
        'group_name' => 'role',
        'permissions' => [
            'browse',
            'create',
            'update',
            'delete',
            'assign_permission',
        ]
    ],
    [
        'group_name' => 'user_log',
        'permissions' => [
            'browse',
        ]
    ],
    [
        'group_name' => 'warehouse',
        'permissions' => [
            'browse',
            'create',
            'update',
            'delete',
        ]
    ],
    [
        'group_name' => 'cctv',
        'permissions' => [
            'browse',
            'create',
            'update',
            'delete',
        ]
    ],
    [
        'group_name' => 'bag',
        'permissions' => [
            'browse',
            'create',
            'update',
            'delete',
        ]
    ],
    [
        'group_name' => 'shift',
        'permissions' => [
            'browse',
            'create',
            'update',
            'delete',
        ]
    ]
];
