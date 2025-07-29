<?php
require_once('php/connect_db.php');
require_once('php/utils.php');
session_start();

if (isset($_POST['password']) || isset($_POST['code_word'])) {
    $key = isset($_POST['password']) ? 'password' : 'code_word';
    $password = $_POST[$key];
    $result = mysqli_query($link, "SELECT value FROM config WHERE config.column = '$key'");
    $hash = mysqli_fetch_all($result)[0][0];
    if (password_verify($password, $hash)) {
        if ($key == 'password') {
            $_SESSION['edit'] = true;
        } else {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            mysqli_query($link, "UPDATE config SET value = '$new_password' WHERE config.column = 'password'");
            $alert = ['success', 'check-circle-fill', 'Пароль успешно изменен'];
        }
    } else {
        $alert = ['danger', 'exclamation-triangle-fill'];
        array_push($alert, $key == 'password' ? 'Неверный пароль' : 'Неверное кодовое слово');
    }
    if (isset($alert)) {
        echo "<div class='alert alert-$alert[0] d-flex align-items-center m-4' role='alert' style='width: max-content; margin-bottom: -24px !important;'>
            <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Warning:'><use xlink:href='#$alert[1]'/></svg>
            <div>
                $alert[2]!
            </div>
        </div>";
    }
}

if (isset($_POST['point'])) {
    $query = "INSERT INTO points (name, region) VALUES ('$_POST[point]', $_POST[regions])";
    mysqli_query($link, $query);
}

if (isset($_POST["logout"])) {
    session_destroy();
    $edit = false;
    $_SESSION['edit'] = false;
}

$edit = $_SESSION['edit'] ?? false;
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
    <link rel="apple-touch-icon" sizes="57x57" href="favicon//apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicon//apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicon//apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicon//apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicon//apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicon//apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicon//apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicon//apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicon//apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="favicon//android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon//favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon//favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon//favicon-16x16.png">
    <link rel="manifest" href="favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="favicon//ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>
    <!-- Основной контейнер страницы с отступами -->
    <div class="container-fluid p-4">
        <div class="card border-0">
            <div class="card-body p-0">
                <section class="mb-4">
                    <div
                        class="d-flex flex-column flex-md-row px-0 justify-content-between align-items-start gap-3 w-100">
                        <div class="w-100 w-md-50">
                            <div class="page-header">
                                <h1 class="text-primary">Жуки-жужелицы Республики Бурятия</h1>
                                <p>По книге «Жуки-жужелицы (Coleoptera, Carabidae) Бурятии» (2014) Л.Ц. Хобраковой.</p>
                            </div>
                            <?= $edit ? '<div class="d-flex gap-2"><button type="button" class="btn btn-primary flex-fill w-md-auto" style="min-width: 180px; height: max-content"
                                    data-bs-toggle="modal" data-bs-target="#createBeetleModal">+ Создать жужелицу</button>
                                    <form class="flex-fill w-md-auto" method="POST"><button name="logout" value="logout" class="btn btn-outline-primary btn-edit-mode w-100">Выйти из режима редактирования</button></form></div>' : '<button type="button" class="btn btn-primary w-md-auto" data-bs-toggle="modal" data-bs-target="#allowEdit">
                                Войти в режим редактирования
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
                    <h5 class="modal-title" id="allowEditLabel">Режим редактирования</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="allowEditForm" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="point" class="form-label">Пароль</label>
                            <input type="text" class="form-control" id="password" name="password" required>
                            <div class="form-text">Введите пароль для доступа к режиму редактирования</div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="changePasswordBtn"
                            data-bs-toggle="modal" data-bs-target="#editPassword">Сменить пароль</button>
                        <div>
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Отмена</button>
                            <button type="submit" class="btn btn-primary btn-sm">Проверить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPassword" tabindex="-1" aria-labelledby="editPasswordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPasswordLabel">Изменение пароля</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPasswordForm" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="point" class="form-label">Кодовое слово</label>
                            <input type="text" class="form-control" id="code_word" name="code_word" required>
                        </div>
                        <div class="mb-3">
                            <label for="point" class="form-label">Новый пароль</label>
                            <input type="text" class="form-control" id="new_password" name="new_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary btn-sm">Сменить пароль</button>
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
        const manager = new TableManager();
        $(document).ready(() => {
            manager.init()
            let params = new URLSearchParams(window.location.search);
            const region = params.getAll('regions[]');
            if (region) {
                $('#filters select').val(region).trigger('change');
                $('#filters_hidden').val(region)
            };
        });

        function resetFilters() {
            $('#regions_select').val("").trigger('change');
            window.location.replace(window.location.pathname);
        }
    </script>

</body>

</html>