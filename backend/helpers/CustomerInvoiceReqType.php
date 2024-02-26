<?php

namespace backend\helpers;

class CustomerInvoiceReqType
{
    private static $data = [
        '0' => 'ไม่เอาใบกำกับ',
        '1' => 'เอาใบกำกับ'
    ];

    private static $dataobj = [
        ['id'=>'0','name' => 'ไม่เอาใบกำกับ'],
        ['id'=>'1','name' => 'เอาใบกำกับ']
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
