<?php

namespace Migrations;

use Framework\Migrations\Migrations;
use Framework\Migrations\MigrationInterface;

class user_movies20201009163711 extends Migrations implements MigrationInterface
{
    public function up(): void
    {
        $this->addSql("
            CREATE TABLE `user_movies` (
              `user_id` int(11) NOT NULL,
              `movie_id` int(11) NOT NULL,
              FOREIGN KEY (user_id) REFERENCES users(user_id),
              FOREIGN KEY (movie_id) REFERENCES movies(movie_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->addSql("DROP TABLE user_movies");
    }
}
