<?php

namespace App\Helpers;

use DB;

class GlobalHelper {

    public static function getAllColsFromTbl($tableName, $prefix) {
        $cols = [];
        $temp = DB::select('SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA=? and TABLE_NAME =?;', [env('DB_DATABASE'), $tableName]);
//        dd($temp);
//        dd($temp);
        return collect($temp)->map(function($ar) {
                    return $ar->COLUMN_NAME;
                })->toArray();
    }

}
