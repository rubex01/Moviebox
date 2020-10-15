<?php

namespace Framework\Commands\EnableRoles;

class EnableRoles
{
    /**
     * Contains json encoded roles file
     *
     * @var string
     */
    public static $rolesJson = '{
    "roles": [
        "super_admin",
        "admin",
        "user"
    ],
    "actions": {
        "ADMIN_PANEL": [
            0,
            1
        ],
        "BLOCK_USER": [
            0,
            1,
            2
        ]
    }
}';

    /**
     * start function
     *
     * @return bool
     */
    public function start() : bool
    {
        if (!file_exists(__DIR__ . '/../../../Roles.json')) {
            $newFile = fopen(__DIR__ . '/../../../Roles.json', 'w');
            fwrite($newFile, self::$rolesJson);
            echo "Roles are enabled. You can find the Roles.json in the root of your project.";
            return true;
        }
        echo 'There already is a roles file, please use this one. It is located in the root of the project.';
        return false;
    }
}