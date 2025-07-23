<?php
require_once('connect_db.php');

function get_table($where = ''): string
{
    global $link;
    $query = "SELECT s.id as 'ID', f.name as 'Подсемейство', t.name as 'Триба', g.name as 'Род', sg.name as 'Подрод', s.name as 'Вид', s.synonyms as 'Синонимы',
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
    $classes = "form-select form-select-sm ";
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

function get_selects($row_name, $modal, $selects, $multiple = false): string
{
    $result = "<div class='modal-section'>
                        <h6 style='color: black;'>$row_name</h6>
                            <div class='row mb-3'>";
    foreach ($selects as $field => $label) {
        $id = $modal . '_' . $field;
        $select = get_select($id, $field, $multiple);
        $flex = $multiple ? "d-flex flex-column" : '';
        $result .= "<div class='col-md-" . ($multiple ? 6 : 4) . " $flex'>
                        <label for='$id' class='form-label'>$label</label>
                        $select
                    </div>";
    }
    return $result . "</div></div>";
}

function get_form($name): string
{
    $selects = get_selects('Таксономия', $name, ["genus" => 'Род', 'subgenus' => 'Подрод']);
    $id = $name . '_name';
    $synonyms_id = $name . '_synonyms';
    $selects = str_replace('</div></div></div>', "</div><div class='col-md-4'>
        <label for='$id' class='form-label'>Вид</label>
        <input type='text' class='form-control' id='$id' name='name' reqired>
        </div>
        <div class='col-md-12'>
        <label for='$synonyms_id' class='form-label'>Синонимы</label>
        <input type='text' class='form-control' id='$synonyms_id' name='synonyms'>
        </div>
        </div></div>", $selects);
    $genus_name = $name . "_genus";
    $selects = str_replace("id='$genus_name'", "id='$genus_name' required", $selects);

    $range_selects = get_selects('Ареал', $name, [
        'width_ranges' => 'Широтная группа',
        'long_ranges' => 'Долготная группа',
    ]);
    $eco_selects = get_selects('Экология', $name, [
        'ecologic_groups' => 'Экологическая группа',
        'trophic_groups' => 'Трофическая группа',
        'tiered_groups' => 'Ярусная группа'
    ]);
    $multiple_selects = get_selects('География', $name, ['regions' => 'Районы сбора', 'points' => 'Точки сбора'], true);
    $desc_id = $name . "_description";
    $action = $name == 'edit' ? 'edit' : 'create';
    $range_selects = str_replace('col-md-4', 'col-md-6', $range_selects);
    $footer = $name == 'edit' ? "
              <button name='action' value='delete' class='btn btn-danger btn-sm me-auto'>Удалить</button>
              <button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>Закрыть</button>
              <button name='action' value='apply' class='btn btn-primary btn-sm'>Сохранить</button>" : "<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>Отмена</button>
                    <button class='btn btn-success btn-sm' id='createBeetleBtn'>Создать</button>";
    return "<div class='modal-body'>
                <form id='$name" . "_form' method='post' action='php/$action.php'>
                    <div class='row g-2'>
                        <input hidden id='$name" . "_id' name='id'>
                        $selects
                        $multiple_selects
                        $range_selects
                        $eco_selects
                        <div class='modal-section'>
                            <label for='$desc_id' class='form-label fw-medium'><h6 style='color: black;'>Распространение</h6></label>
                            <div class='col-12'>
                                <textarea class='form-control form-control-sm' name='description' id='$desc_id'
                                    rows='3'></textarea>
                            </div>
                        </div>
                    </div>
                    <input hidden name='action'>
                    <div class='modal-footer'>$footer</div>
                </form>
            </div>";
}
?>