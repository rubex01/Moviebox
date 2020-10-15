<?php

namespace Migrations;

use Framework\Migrations\Migrations;
use Framework\Migrations\MigrationInterface;

class users20200921080920 extends Migrations implements MigrationInterface
{
    public function up(): void
    {
        $this->addSql("
                CREATE TABLE `users` (
                  `user_id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(255) NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `password` varchar(255) NOT NULL,
                  `role` varchar(255) DEFAULT NULL,
                  PRIMARY KEY (user_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
    }

    public function down(): void
    {
        $this->addSql("DROP TABLE users");
    }
}
