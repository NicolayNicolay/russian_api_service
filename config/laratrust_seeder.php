<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users'    => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => false,

    'roles_structure' => [
        'admin' => [
            'display_name' => 'Администратор',
            'permissions'  => 'users',
        ],
    ],

    'permissions_map' => [
        'users'           => 'Управление пользователями и ролями',
    ],
];
