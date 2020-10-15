<?php

namespace Migrations;

use Framework\Migrations\Migrations;
use Framework\Migrations\MigrationInterface;

class add_color_to_users20201009041013 extends Migrations implements MigrationInterface
{
    public function up(): void
    {
        $this->addSql("ALTER TABLE `users` ADD `color` VARCHAR(64) NOT NULL;");
    }

    public function down(): void
    {
        $this->addSql("ALTER TABLE `users` DROP `color`;");
    }
}
