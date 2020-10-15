<?php

namespace Migrations;

use Framework\Migrations\Migrations;
use Framework\Migrations\MigrationInterface;

class user_sessions20200921080922 extends Migrations implements MigrationInterface
{
    public function up(): void
    {
        $this->addSql("
                    CREATE TABLE `user_sessions` (
                      `user_sessions_id` int(11) NOT NULL AUTO_INCREMENT,
                      `user_id` int(11) NOT NULL,
                      `token` varchar(255) NOT NULL,
                      `valid_until` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                      `user_agent` text NOT NULL,
                      `get_browser_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`get_browser_info`)),
                      `browser` varchar(255) NOT NULL,
                      `operating_system` varchar(255) NOT NULL,
                      `device_type` varchar(255) NOT NULL,
                      `ip_address` varchar(255) NOT NULL,
                      PRIMARY KEY (user_sessions_id), 
                      FOREIGN KEY (user_id) REFERENCES users(user_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
    }

    public function down(): void
    {
        $this->addSql("DROP TABLE user_sessions");
    }
}
