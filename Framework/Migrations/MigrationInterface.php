<?php

namespace Framework\Migrations;

interface MigrationInterface
{
    /**
     * Makes database connection and returns it
     * 
     * @return void
     */
    public function up();

    /**
     * In case of connection error trigger exception
     * 
     * @return void
     */
    public function down();
}
