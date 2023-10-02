<?php

use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;

class CreateUserFromFormTableMigration
{
    public static function up()
    {
        $connection = Application::getConnection();
        $tableName = 'users_from_form';

        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (
            ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            NAME VARCHAR(255) NOT NULL,
            LAST_NAME VARCHAR(255) NOT NULL,
            HAS_ACCESS BOOLEAN NOT NULL,
            PHONE_NUMBER VARCHAR(15) NOT NULL
        )";

        try {
            $connection->queryExecute($sql);
        } catch (SqlQueryException $e) {
            echo 'Error creating table: ' . $e->getMessage();
        }
    }

    public static function down()
    {
        $connection = Application::getConnection();
        $tableName = 'your_custom_table_name';

        $sql = "DROP TABLE IF EXISTS {$tableName}";

        try {
            $connection->queryExecute($sql);
        } catch (SqlQueryException $e) {
            echo 'Error dropping table: ' . $e->getMessage();
        }
    }
}
