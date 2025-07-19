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
    if ($_POST['action'] == 'apply') {
        $params = [];
        $points_params = [];
        foreach ($_POST as $key => $value) {
            if (!in_array($key, ['id', 'regions', 'points', 'action'])) {
                if (str_ends_with($key, 's') && !in_array($key, ['genus', 'subgenus'])) {
                    $key = substr($key, 0, strlen($key) - 1);
                }
                $value = mysqli_real_escape_string($link, $value);
                array_push($params, "$key = '$value'");
            }
        }
        speciesPointsActions();
        mysqli_query($link, "UPDATE species SET " . join(", ", $params) . " WHERE id = $_POST[id]");
        echo "UPDATE species SET " . join(", ", $params) . " WHERE id = $_POST[id]";

    } else {
        $result = mysqli_query($link, "DELETE FROM species WHERE id = $_POST[id]");
    }
}
header('Location: /groups.php');

?>