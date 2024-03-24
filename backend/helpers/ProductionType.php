<?php

namespace backend\helpers;

class ProductionType
{
    private static $data = [
        'REP' => 'REP',
        'RTF' => 'RTF'
    ];

    private static $dataobj = [
        ['id'=>'REP','name' => 'REP'],
        ['id'=>'RTF','name' => 'RTF']
    ];
    public static function asArray()
    {
        return self::$data;
    }
    public static function asArrayObject()
    {
        return self::$dataobj;
    }
    public static function getTypeById($idx)
    {
        if (isset(self::$data[$idx])) {
            return self::$data[$idx];
        }

        return 'Unknown Type';
    }
    public static function getTypeByName($idx)
    {
        if (isset(self::$data[$idx])) {
            return self::$data[$idx];
        }

        return 'Unknown Type';
    }
}
