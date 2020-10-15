<?php

namespace Migrations;

use Framework\Migrations\Migrations;
use Framework\Migrations\MigrationInterface;

class add_time_to_user_movies20201009163801 extends Migrations implements MigrationInterface
{
    public function up(): void
    {
        $this->addSql("ALTER TABLE `user_movies` ADD `time` INT NULL DEFAULT NULL;");
    }

    public function down(): void
    {
        $this->addSql("ALTER TABLE `user_movies` DROP `time`;");
    }
}
