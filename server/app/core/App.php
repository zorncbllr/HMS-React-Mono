<?php

class App
{
    public string $URI_PATH;
    public function __construct()
    {
        if ($_SERVER["HTTP_HOST"] === "localhost:3000") {
            $this->URI_PATH = $_SERVER["PHP_SELF"] === "/" ? $_SERVER["PHP_SELF"] . "index.php" : $_SERVER["PHP_SELF"];
        } else {
            $this->URI_PATH = isset($_GET["url"]) ? "/" . $_GET["url"] : "/Home";
        }
    }

    static function debug_print(mixed $prop)
    {
        echo "<pre>";
        (is_array($prop) ? print_r($prop) : var_dump($prop));
        echo "</pre>";
    }
}
