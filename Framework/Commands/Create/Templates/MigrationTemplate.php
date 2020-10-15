<?php

$fileContent = '<?php

namespace '.$namespace.';

use Framework\Migrations\Migrations;
use Framework\Migrations\MigrationInterface;

class ' . $name . ' extends Migrations implements MigrationInterface
{
    public function up(): void
    {
        $this->addSql("Your sql script here");
    }

    public function down(): void
    {
        $this->addSql("Drop sql script here");
    }
}
';