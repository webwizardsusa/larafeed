<?php
require_once(__DIR__ . '/../vendor/autoload.php');
$data = json_decode(file_get_contents(__DIR__ . '/itunes_cats.json'), true);
foreach ($data as $parent) {
    foreach ($parent as $parentName => $children) {
        $classname = ucfirst(\Illuminate\Support\Str::camel(str_replace('&', ' And ', $parentName)));
        $enums = [];
        foreach ($children as $childName) {
            $childMachine = (string)\Illuminate\Support\Str::of(str_replace(['&', '-'], [' And ', ' '], $childName))
                ->snake()
                ->upper();
            $enums[] = '    case ' . $childMachine . ' = "' . $childName . '";';
        }

        $stub = file_get_contents(__DIR__ . '/cat_stub.txt');
        $stub = str_replace('{{CLASS_NAME}}', $classname, $stub);
        $stub = str_replace('{{VALUES}}', implode(PHP_EOL, $enums), $stub);
        $stub = str_replace('{{FRIENDLY_NAME}}', $parentName, $stub);
        $filename = $classname . '.php';
        echo "Generated " . $filename . PHP_EOL;
        file_put_contents(__DIR__ . '/../src/ITunes/Categories/' . $filename, $stub);
    }

}