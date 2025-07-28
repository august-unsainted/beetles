<?php
require_once('connect_db.php');

if (isset($_POST)) {
    $params = [];
    $fields = [];
    $values = [];
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['regions', 'points']) && $value != '') {
            if (str_ends_with($key, 's') && !in_array($key, ['genus', 'subgenus'])) {
                $key = substr($key, 0, strlen($key) - 1);
            }
            $value = mysqli_real_escape_string($link, $value);
            array_push($fields, $key);
            array_push($values, "'$value'");
        }
    }
    $query = "INSERT INTO species (" . join(", ", $fields) . ") VALUES (" . join(", ", $values) . ")";
    mysqli_query($link, $query);
    if (!empty($_POST['points'])) {
        $id = mysqli_insert_id($link);
        $points = [];
        foreach ($_POST['points'] as $point) {
            array_push($points, "($id, $point)");
        }
        mysqli_query($link, "INSERT INTO species_points VALUES " . join(', ', $points) . '<br>');
    }
}
header('Location: /index.php');

?>