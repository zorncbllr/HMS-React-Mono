<?php

class Generate
{
    static function createNewController(string $filename)
    {
        $class = ucfirst($filename);
        $directory = __DIR__ . "/../../../controllers";

        if (!is_dir($directory)) {
            mkdir($directory);
            file_put_contents(
                $directory . "/_404.php",
                "<?php\n\nclass _404 extends Controller {\n\tpublic static function error(){\n\n\t\treturn view('404');\n\t}\n}\n"
            );
        }

        file_put_contents(
            $directory . "/$class.php",
            "<?php\n\nclass $class extends Controller {\n\n\t#[Route(method: 'GET')]\n\tpublic function index(Request \$request){\n\n\t\treturn '$class controller';\n\t}\n}\n"
        );

        exit();
    }

    static function createNewModel(string $filename)
    {
        $class = ucfirst($filename);
        $directory = __DIR__ . "/../../../models";

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        file_put_contents(
            $directory . "/$class.php",
            "<?php\n\nclass $class extends Model {\n\tprivate \$id;\n\tpublic function __construct(\$id = null) {\n\t\t\$this->id = \$id;\n\t}\n}"
        );

        exit();
    }

    static function createNewComponent(string $filename)
    {
        $comp = ucfirst($filename);
        $directory = __DIR__ . "/../../../views/components";

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        file_put_contents(
            $directory . "/$comp.com.php",
            "<div>\n\t<h1>$comp component</h1>\n<div>"
        );

        exit();
    }

    static function createView(string $filename)
    {
        $view = ucfirst($filename);
        $directory = __DIR__ . "/../../../views";

        if (!is_dir($directory)) {
            mkdir($directory);
            file_put_contents(
                $directory . "/404.view.php",
                "<!DOCTYPE html>\n<html lang='en'>\n<head>\n\t<meta charset='UTF-8'>\n\t<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n\t<title>404 | Page not Found</title>\n</head>\n<body>\n\t<h1>404 | Page not Found</h1>\n</body>\n</html>"
            );
        }

        file_put_contents(
            $directory . "/$view.view.php",
            "<!DOCTYPE html>\n<html lang='en'>\n<head>\n\t<meta charset='UTF-8'>\n\t<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n\t<title>$view</title>\n</head>\n<body>\n\t<h1>$view</h1>\n</body>\n</html>"
        );

        exit();
    }

    static function fillSchema(string $filename)
    {
        $path = self::getPath($filename);
        $constructorBlocks = self::getconstructorBlocks($filename);
        $attrs = self::getAttributes($filename, keys: true);

        $initAttrs = "";
        $gettersAndSetters = "\n";

        $constructorBlocks[2] = explode("}", $constructorBlocks[2])[0] . "\n\t}";

        foreach ($attrs as $index => $attr) {
            $copy = str_replace("$", "", $attr);
            $copy = explode(" ", $copy);

            $pattern = "/public/";

            $copy = $copy[sizeof($copy) - 1];

            if (!preg_match($pattern, $copy)) {
                $gettersAndSetters .= self::gettersAndSetters($copy);
            }
            $copy = trim(str_replace("public", "", $copy));
            $initAttrs .= "\n\t\t\$this->$copy = \$$copy;" . ($index < (sizeof($attrs) - 1) ? "" : "\n");
        }

        $constructorBlocks[2] = "\n" . $initAttrs . $constructorBlocks[2];
        $constructorBlocks[1] = self::getConstructParams($filename);

        $content = "";

        foreach ($constructorBlocks as $index => $block) {
            $content .= $block . ($index < sizeof($constructorBlocks) - 1 ? " {" : "");
        }

        $content .= self::initModelFunction($filename);
        $content .= $gettersAndSetters . "\n}";

        file_put_contents($path, $content);
        echo ucfirst($filename) . " Model Schema updated.";

        exit();
    }

    private static function getAttributes(string $filename, $keys = false, $list = false, $assoc = false): array
    {
        $types = ["string", "int", "array", "object", "bool", "double", "float"];
        $directory = __DIR__ . "/../../../models/";

        $iterator = new DirectoryIterator($directory);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $modelClass = $file->getBasename(".php");
                array_push($types, $modelClass);
            }
        }

        $constructorBlocks = self::getconstructorBlocks($filename);
        $attributes = explode(";", $constructorBlocks[1]);
        $result = [];

        for ($i = 0; $i < sizeof($attributes) - 1; $i++) {
            $line = $attributes[$i];

            $datatype = "";

            foreach ($types as $type) {
                if (str_contains($line, $type)) {
                    $datatype = $type;
                    $line = str_replace($type, "", $line);
                    break;
                }
            }

            $attrs = explode(",", $line);

            foreach ($attrs as $attr) {
                $attr = trim(str_replace("private", "", $attr));
                $result[str_replace(" ", "", $attr)] = $datatype;
            }
        }

        if ($keys) {
            return array_keys($result);
        } elseif ($list) {
            $list = [];
            foreach ($result as $key => $val) {
                array_push($list, $val . " " . $key);
            }
            return $list;
        } else if ($assoc) {
            return $result;
        }

        return [];
    }

    private static function getconstructorBlocks(string $filename): array
    {
        $path = self::getPath($filename);
        $content = file_get_contents($path);

        return explode("{", $content);
    }

    private static function getPath(string $filename)
    {
        $filename = ucfirst($filename) . ".php";
        $directory = __DIR__ . "/../../../models/";
        return $directory . $filename;
    }

    private static function getConstructParams(string $filename)
    {
        $constructorBlocks = self::getconstructorBlocks($filename);
        $constructor = explode("(", $constructorBlocks[1]);

        $attrs = self::getAttributes($filename, list: true);
        $attrs = array_map(
            fn($attr) => trim(str_replace("public", "", $attr)),
            $attrs
        );

        $severalParams = sizeof($attrs) > 4;
        $finalParam =  "";
        $optionals = "";

        foreach ($attrs as $index => $attr) {
            $is_optional = sizeof(explode(' ', $attr)) === 1;
            $is_last_attr = $index === (sizeof($attrs) - 1);
            $wspce = sizeof($attrs) > 4 ? "\n\t\t" : "";
            if ($is_optional) {
                $optionals .= $wspce . $attr . " = null" . ($is_last_attr ? '' : ', ');
            } else {
                $finalParam .= $wspce . $attr . ($is_last_attr && empty($optionals) ? '' : ', ');
            }
        }

        $finalParam .= $optionals;

        $result = $constructor[0] . "($finalParam" . ($severalParams ? "\n\t)" : ")");

        return $result;
    }

    private static function gettersAndSetters(string $attribute)
    {
        $getter = "\n\tpublic function get" . ucfirst($attribute) . "() {\n\t\treturn \$this->$attribute;\n\t}\n";
        $setter = "\n\tpublic function set" . ucfirst($attribute) . "(\$$attribute) {\n\t\t\$this->$attribute = \$$attribute;\n\t}\n";

        return $getter . $setter;
    }

    private static function initModelFunction(string $filename)
    {
        $attributes = self::getAttributes($filename, assoc: true);
        $result = "\n\tpublic static function init" . ucfirst($filename) . "() {\n\t\tself::createTable('";

        foreach ($attributes as $attr => $type) {
            $copy = str_replace("$", "", $attr);
            $copy = str_replace("public", "", $copy);

            $result .= match ($type) {
                "string" => "\n\t\t\t$copy VARCHAR(20) NOT NULL",
                "int" => "\n\t\t\t$copy INT AUTO_INCREMENT PRIMARY KEY",
                default => "\n\t\t\t$copy <ADD YOUR CONFIGURATION>"
            };
            $result .= ($attr != array_key_last($attributes)) ? "," : "\n\t\t');\n\t}";
        }

        return $result;
    }

    public static function createMiddleware(string $filename)
    {
        $class = ucfirst($filename);
        $directory = __DIR__ . "/../../../middlewares";

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        file_put_contents(
            $directory . "/$class.php",
            "<?php\n\nuse App\Core\Middleware;\n\nclass $class extends Middleware\n{\n\tstatic function runnable(Request \$request, callable \$next)\n\t{\n\t\techo '$class Middleware';\n\t}\n}"
        );

        exit();
    }
}
