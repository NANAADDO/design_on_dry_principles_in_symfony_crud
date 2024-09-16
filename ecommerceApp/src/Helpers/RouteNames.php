<?php

namespace App\Helpers;

class RouteNames {

    public static $routeName;
    public static $entityName;

    private final function __construct() {

    }

    public static function getEntityName() {
       return self::$entityName;
    }
}