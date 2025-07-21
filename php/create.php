<?php
require_once('connect_db.php');

function speciesPointsActions()
{
    global $link;
    if (!isset($_POST['points'])) {
        mysqli_query($link, "DELETE FROM species_points WHERE beetle=$_POST[id]");
        return;
    }
    $query_template = " FROM species_points WHERE beetle=$_POST[id] AND point %s IN (" . join(', ', $_POST['points']) . ")";

    mysqli_query($link, "DELETE" . sprintf($query_template, 'NOT'));
    // echo "DELETE" . sprintf($query_template, 'NOT') . '<br>';


    $result = mysqli_query($link, "SELECT point" . sprintf($query_template, ''));
    $otherValues = mysqli_fetch_all($result);

    $otherIds = [];
    foreach (array_values($otherValues) as $value) {
        array_push($otherIds, $value[0]);
    }
    $cleanIds = array_diff($_POST['points'], $otherIds);

    $insertIds = [];
    foreach ($cleanIds as $id) {
        $insertIds[$id] = "($_POST[id], $id)";
    }
    if (!empty($insertIds)) {
        mysqli_query($link, "INSERT INTO species_points VALUES " . join(', ', $insertIds));
        // echo "INSERT INTO species_points VALUES " . join(', ', $insertIds) . '<br>';
    }
}

if (isset($_POST)) {
    $params = [];
    $fields = [];
    $values = [];
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['regions', 'points'])) {
            if (str_ends_with($key, 's') && !in_array($key, ['genus', 'subgenus'])) {
                $key = substr($key, 0, strlen($key) - 1);
            }
            if ($value != '') {
                $value = mysqli_real_escape_string($link, $value);
                array_push($fields, $key);
                array_push($values, $value);
            }

        }
    }
    // speciesPointsActions();
    $query = "INSERT INTO species (" . join(", ", $fields) . ") VALUES (" . join(", ", $values) . ")";
    // mysqli_query($link, $query);
    echo $query;
    if (!empty($_POST['points'])) {
        $id = mysqli_insert_id($link);
        $points = [];
        foreach ($_POST['points'] as $point) {
            array_push($points, "($id, $point)");
        }
        echo "INSERT INTO species_points VALUES " . join(', ', $points) . '<br>';
    }
}
header('Location: /groups.php');

?>