<?php

namespace backend\helpers;

class StdpricegroupType
{
    const ROLE = 1;
    const RULE = 2;
    private static $data = [
        1 => 'ขายสด',
        2 => 'ขายเชื่อ',
        3 => 'ฟรี'
    ];

    private static $dataobj = [
        ['id'=>1,'name' => 'ขายสด'],
        ['id'=>2,'name' => 'ขายเชื่อ'],
        ['id'=>3,'name' => 'ฟรี'],
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
