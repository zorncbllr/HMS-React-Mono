<?php

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        try {
            $config = (require __DIR__ . '/../config/config.php')['database'];

            if ($config['dbname'] !== 'sample_db') {
                $dsn = "mysql:" . http_build_query($config, "", ";");

                $this->pdo = new PDO($dsn, $config["user"], $config["password"], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            }
        } catch (PDOException $e) {
            echo 'Connection error: ' . $e->getMessage();
        }
    }
    public function getPDO()
    {
        return $this->pdo;
    }

    public static function query(string $query, array $params = []): array
    {
        $pdo = $GLOBALS['database']->getPDO();
        $statement = $pdo->prepare($query);

        $statement->execute($params);

        return $statement->fetchAll();
    }

    private static function trimClassName(Model $model): array
    {
        $attributes = array_filter(
            (array) $model,
            fn($attr) => $attr !== null
        );

        $keys = array_map(
            fn($attr) => trim(str_replace(get_class($model), "", $attr)),
            array_keys($attributes)
        );

        $values = array_values($attributes);
        $attributes = [];

        for ($i = 0; $i < sizeof($keys); $i++) {
            $attributes[$keys[$i]] = $values[$i];
        }

        return $attributes;
    }
    public static function create(Model $model): bool
    {
        $pdo = $GLOBALS['database']->getPDO();
        $attributes = self::trimClassName($model);

        $table = lcfirst(get_called_class()) . "s";

        $query = "insert into `$table` (";

        $attach_attributes = function (string $prep = "")
        use (&$attributes, &$query) {
            $tag = $prep ? "" : "`";
            foreach (array_keys($attributes) as $key) {
                $query .= $tag . $prep . $key . $tag . ($key != array_key_last($attributes) ? ", " : ") ");
            }
        };

        $attach_attributes();
        $query .= "values (";
        $attach_attributes(":");

        try {
            $statement = $pdo->prepare($query);
            $statement->execute($attributes);
            return true;
        } catch (PDOException $_) {
            return false;
        }
    }

    public static function createTable(string $config)
    {
        $pdo = $GLOBALS['database']->getPDO();

        $query = "CREATE TABLE IF NOT EXISTS `" . lcfirst(get_called_class()) . "s" . "` ( " . $config . " )";
        $pdo->exec($query);
    }
    protected static function initModels()
    {
        $modelsPath = __DIR__ . "/../models";
        $iterator = new DirectoryIterator($modelsPath);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $modelClass = $file->getBasename(".php");
                new $modelClass();
            }
        }
    }

    public static function update(Model $model): bool
    {
        $params = self::trimClassName($model);
        try {
            $pdo = $GLOBALS['database']->getPDO();

            $table = strtolower(get_called_class()) . "s";
            $query = "UPDATE `$table` SET ";

            foreach ($params as $key => $value) {
                $query .= "`$key` = :$key" . ($key != array_key_last($params) ? ", " : " ");
            }

            $query .= " WHERE `$table`.`id` = :id";

            $statement = $pdo->prepare($query);

            $statement->execute([...$params, "id" => $params['id']]);

            return true;
        } catch (PDOException $_) {
            return false;
        }
    }

    public static function delete(Model | int $target): bool
    {
        $id = is_int($target) ? $target : self::trimClassName($target)["id"];
        try {
            $pdo = $GLOBALS['database']->getPDO();
            $table = lcfirst(get_called_class()) . "s";
            $query = "DELETE FROM `$table` WHERE `$table`.`id` = :id";

            $statement = $pdo->prepare($query);
            $statement->execute(["id" => $id]);

            return true;
        } catch (PDOException $_) {
            return false;
        }
    }
}
