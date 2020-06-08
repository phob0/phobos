<?php namespace Phobos\Framework\Generators\Syntax;

class DroppedTable {

    /**
     * Get string for dropping a table
     *
     * @param $tableName
     * @return string
     */
    public function drop($tableName)
    {
        return "Schema::drop('$tableName');";
    }

}
