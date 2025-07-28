<?php
require_once('php/connect_db.php');
require_once('php/utils.php');
session_start();
$edit = $_SESSION['edit'] ?? false;

if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $result = mysqli_query($link, 'SELECT * FROM config');
    $hash = mysqli_fetch_all($result)[0][0];
    if (password_verify($password, $hash)) {
        $_SESSION['edit'] = true;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <!-- Установка кодировки и адаптивности страницы -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Жуки-жужелицы (Coleoptera Carabidae) Республики Бурятия</title>
    <!-- Подключение стилей Bootstrap и Grid.js для оформления таблицы -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />

    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="css/select.css">
</head>

<body>
    <!-- Основной контейнер страницы с отступами -->
    <div class="container-fluid py-4 px-3">
        <div class="card border-0">
            <div class="card-body">
                <section class="mb-4">
                    <div
                        class="d-flex flex-column flex-md-row px-0 justify-content-between align-items-start gap-3 w-100">
                        <div class="w-100 w-md-50">
                            <h1 class="fw-bold text-primary mb-3">Жуки-жужелицы (Coleoptera, Carabidae) Республики
                                Бурятия</h1>
                            <h6 class="fw-bold mb-3">По книге «Жуки-жужелицы (Coleoptera, Carabidae) Бурятии» (Л.Ц.
                                Хобракова, В.Г. Шиленков, Р.Ю. Дудко)</h1>
                                <?= $edit ? '<button type="button" class="btn btn-primary w-100 w-md-auto" style="min-width: 180px;"
                                    data-bs-toggle="modal" data-bs-target="#createBeetleModal">
                                    + Создать жужелицу
                                </button>' : '<button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#allowEdit">
                                Редактировать
                            </button>' ?>
                        </div>

                        <?= get_columns_fieldset($edit) ?>
                    </div>
                </section>
                <div id="filters_hidden" style="display: none">
                    <form action="" method="get" style="display: flex; gap: 8px; height: 100%;">
                        <?= str_replace('multiple-select', '', get_select('regions_select', 'regions', true)) ?>
                        <button type="button" onclick="resetFilters()"
                            class="btn btn-secondary btn-sm">Сбросить</button>
                        <button class="btn btn-secondary btn-sm">Применить</button>
                    </form>
                </div>
                <?= get_table($edit, isset($_GET['regions']) ? 'WHERE p.region IN (' . join(', ', $_GET['regions']) . ')' : '') ?>

                <!-- Основная таблица -->
                <div id="table-wrapper" class="rounded"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="allowEdit" tabindex="-1" aria-labelledby="allowEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allowEditLabel">Получить доступ к редактированию</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="allowEditForm" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="point" class="form-label">Пароль</label>
                            <input type="text" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary btn-sm">Проверить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?= $edit ? get_modals() : '' ?>
    <!-- Подключение библиотек Grid.js и Bootstrap для функциональности таблицы и модальных окон -->
    <script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/TableManager.js"></script>
    <script>
        const manager = new TableManager('eco');
        $(document).ready(() => {
            manager.init()
            let params = new URLSearchParams(window.location.search);
            const region = params.getAll('regions[]');
            if (region) {
                $('#filters select').val(region).trigger('change');
                $('#filters_hidden').val(region)
            };
            console.log('Длина', $('#data_table').rows[0].cells.length)
        });

        function resetFilters() {
            $('#regions_select').val("").trigger('change');
            window.location.replace(window.location.pathname);
        }
    </script>

</body>

</html>