<?php

namespace Migrations;

use Framework\Migrations\Migrations;
use Framework\Migrations\MigrationInterface;

class movies20201009163647 extends Migrations implements MigrationInterface
{
    public function up(): void
    {
        $this->addSql("
            CREATE TABLE `movies` (
              `movie_id` int(11) NOT NULL AUTO_INCREMENT,
              `hash` varchar(255) NOT NULL,
              `movie_name` varchar(65) NOT NULL,
              `official_id` int(11) NOT NULL,
              `status` varchar(40) NOT NULL,
              `static_data` JSON NOT NULL,
              PRIMARY KEY (movie_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->addSql("DROP TABLE movies");
    }
}
