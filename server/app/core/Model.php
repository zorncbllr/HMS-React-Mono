<?php

abstract class Model extends Database
{
    public static function find(array $param = [])
    {
        $query = "select * from `" . lcfirst(get_called_class()) . "s`";

        if (empty($param)) {
            return self::mapper(parent::query($query));
        }

        $query .= " where " . key($param) . " = :" . key($param);

        $data = parent::query($query, [...$param]);

        if (sizeof($data) === 1) {
            return self::mapper($data[0]);
        }

        return self::mapper($data);
    }

    public static function findById(int | string $id)
    {
        $query = "select * from " . lcfirst(get_called_class()) . "s where id = :id";
        return self::mapper(
            parent::query($query, ["id" => $id])[0]
        );
    }

    private static function mapper(array $data)
    {
        $class = get_called_class();

        if (array_is_list($data)) {
            return array_map(
                fn($dt) => new $class(...$dt),
                $data
            );
        }

        return new $class(...$data);
    }
}
