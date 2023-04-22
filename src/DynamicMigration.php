<?php

namespace Feyyazcankose\LaravelDynamicMigrate;
use Illuminate\Support\Facades\Schema;


class DynamicMigration
{
    // Rename Column
    static public function renameColumn($now, $new, $tableName, $table)
    {
        if (Schema::hasColumn($tableName, $now)) {
            $table->renameColumn($now, $new);
        }
    }

    // Drop Column
    static public function dropColumn($column, $tableName, $table)
    {
        if (Schema::hasColumn($tableName, $column)) {
            $table->dropColumn($column);
        }
    }

    // Add Column
    static public function addColumn($column, $tableName, $func)
    {
        if (!Schema::hasColumn($tableName, $column)) {
            $func();
        }
    }

    // Change Column
    static public function changeColumn($column, $tableName, $func)
    {
        if (Schema::hasColumn($tableName, $column)) {
            $func();
        }
    }
}
