<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/14/17
 * Time: 2:00 PM
 */

namespace KoperTest\Migrations;


interface MigrationInterface
{

    public function __construct(\PDO $dbh);

    /**
     * Create tables
     * @return void
     */
    public function up();

    /**
     * Remove tables
     *
     * @return void
     */
    public function down();

    /**
     * Fill tables with data
     * @return void
     */
    public function seed();
}
