<?php
$tables = DB::select('SHOW TABLES');
foreach($tables as $table) {
    $name = array_values((array)$table)[0];
    echo $name . "\n";
    $cols = DB::select('DESCRIBE ' . $name);
    foreach($cols as $col) {
        echo "  " . $col->Field . " (" . $col->Type . ")\n";
    }
    echo "\n";
}
