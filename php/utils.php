<?php
require_once('connect_db.php');

function get_table($where = ''): string
{
    global $link;
    $query = "SELECT s.id as 'ID', f.name as 'Подсемейство', t.name as 'Триба', g.name as 'Род', sg.name as 'Подрод', s.name as 'Вид',
    GROUP_CONCAT(DISTINCT SUBSTRING_INDEX(r.name, ' район', 1) SEPARATOR ', ') as 'Районы', GROUP_CONCAT(DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(p.name, ' [', 1), ', ', 1) SEPARATOR ', ') as 'Пункты_сбора',
    w.name as 'Широтная_группа', l.name as 'Долготная_группа', e.name as 'Экологическая_группа', tr.name as 'Трофическая_группа', ti.name as 'Ярусная_группа', s.description as 'Распространение'
    FROM species s
    left join subgenus sg on sg.id = s.subgenus
    left join genus g on g.id = s.genus
    left join tribes t on t.id = g.triba
    left join families f on f.id = t.family
    left join species_points sp on sp.beetle = s.id
    left join points p on sp.point = p.id
    left join regions r on r.id = p.region
    left join width_ranges w on w.id = s.width_range
    left join long_ranges l on l.id = s.long_range
    left Join ecologic_groups e on e.id = s.ecologic_group
    left join trophic_groups tr on tr.id = s.trophic_group
    left join tiered_groups ti on ti.id = s.tiered_group
    {}
    GROUP BY s.id;";
    $query = str_replace('{}', $where, $query);
    $result = mysqli_query($link, $query);
    $species = mysqli_fetch_all($result);
    $result = '<table id="data_table" style="display: none;"><tbody>';
    foreach ($species as $specie) {
        $result .= '<tr>';
        foreach ($specie as $specie_td) {
            $result .= "<td>$specie_td</td>";
        }
        $result .= "</tr>";
    }
    $result .= '</tbody></table>';
    return $result;
}

function get_select($id, $table, $multiple = false): string
{
    global $link;
    $query = $table == 'points' ? "SELECT DISTINCT id, SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' [', 1), ', ', 1), region FROM points" : "SELECT * from $table";
    $result = mysqli_query($link, $query);
    $values = mysqli_fetch_all($result);
    $classes = "form-select form-select-sm";
    $name = $table;
    if ($multiple) {
        $classes .= " multiple-select";
        $name .= "[]";
        $multiple_str = "multiple='multiple'";
    }
    $result = "<select class='$classes' name='$name' id='$id' " . ($multiple_str ?? '') . ">";
    foreach ($values as $value) {
        if ($table == 'points') {
            $extra = "data-region='$value[2]'"; 
        } elseif ($table == 'subgenus') {
            $extra = "data-genus='$value[2]'";
        } else {
            $extra = '';
        }
        $result .= "<option name='$value[1]' value='$value[0]' $extra>$value[1]</option>";
    }
    $result .= "</select>";
    return $result;
}

function get_selects($modal, $selects, $multiple = false): string
{
    $result = '';
    foreach ($selects as $field => $label) {
        $id = $modal . '_' . $field;
        $select = get_select($id, $field, $multiple);
        $flex = $multiple ? "d-flex flex-column" : '';
        $result .= "<div class='col-md-6 $flex'><label for='$id' class='form-label fw-medium'>$label</label>$select</div>";
    }
    return $result;
}

function get_form($name): string
{
    $selects = get_selects($name, ["genus" => 'Род', 'subgenus' => 'Подрод']);
    $id = $name . '_name';
    $inputs = "<input hidden id='$name" . "_id' name='id'>
                <div class='col-md-6'>
                <label for='$id' class='form-label fw-medium'>Вид</label>
                <input type='text' class='form-control form-control-sm' id='$id' name='name'>
                </div>";
    $eco_selects = get_selects($name, ['width_ranges' => 'Широтная группа ареала', 'long_ranges' => 'Долготная группа ареала', 
    'ecologic_groups' => 'Экологическая группа', 'trophic_groups' => 'Трофическая группа', 'tiered_groups' => 'Ярусная группа']);
    $multiple_selects = get_selects($name, ['regions' => 'Районы сбора', 'points' => 'Точки сбора'], true);
    $desc_id = $name . "_description";
    $action = $name == 'edit' ? 'edit' : 'create';
    return "<div class='modal-body'>
                <form id='$name" . "_form' method='post' action='php/$action.php'>
                    <div class='row g-3'>
                        $selects
                        $inputs
                        $eco_selects
                        $multiple_selects
                        <div class='col-12'>
                            <label for='$desc_id' class='form-label fw-medium'>Распространение</label>
                            <textarea class='form-control form-control-sm' name='description' id='$desc_id'
                                rows='3'></textarea>
                        </div>
                    </div>
                    <input hidden name='action'>
                </form>
            </div>";
}
?>