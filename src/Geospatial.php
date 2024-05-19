<?php

namespace Javaabu\Geospatial;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\SQLiteConnection;

class Geospatial
{
    public static function supported(ConnectionInterface $connection): bool
    {
        if (self::isSqlite($connection)) {
            return false;
        }

        return true;
    }

    private static function isSqlite(ConnectionInterface $connection): bool
    {
        return $connection instanceof SQLiteConnection;
    }
}
