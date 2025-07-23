<?php
require_once('php/connect_db.php');
require_once('php/utils.php');

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <!-- Установка кодировки и адаптивности страницы -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Интерактивная таблица жужелиц</title>
    <!-- Подключение стилей Bootstrap и Grid.js для оформления таблицы -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="css/select.css">
</head>

<body>
    <!-- Основной контейнер страницы с отступами -->
    <div class="container py-4">
        <!-- Заголовок страницы -->
        <h1 class="mb-4 text-center fw-bold">Интерактивная таблица по видам жужелиц</h1>

        <!-- Кнопка для открытия модального окна создания новой записи -->
        <div class="d-flex justify-content-center mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBeetleModal">
                Создать новую жужелицу
            </button>
        </div>
        <div id="filters">
            <form action="" method="get" style="display: flex; gap: 8px; height: 100%;">
                <?= get_select('regions_select', 'regions', true) ?>
                <button type="button" onclick="resetFilters()" class="btn btn-secondary btn-sm">Сбросить</button>
                <button class="btn btn-secondary btn-sm">Применить</button>
            </form>
        </div>
        <?= get_table(isset($_GET['regions']) ? 'WHERE p.region IN (' . join(', ', $_GET['regions']) . ')' : '') ?>

        <!-- Контейнер для отображения таблицы с данными -->
        <div id="table-wrapper" class="shadow-sm rounded"></div>
    </div>

    <!-- Контейнер для уведомлений (тостов) -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="notification-container"></div>
    </div>

    <!-- Модальное окно для просмотра и редактирования деталей жужелицы -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold" id="detailsModalLabel">Детали жужелицы</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?= get_form('edit') ?>
            </div>
        </div>
    </div>

    <!-- Модальное окно для создания новой жужелицы -->
    <div class="modal fade" id="createBeetleModal" tabindex="-1" aria-labelledby="createBeetleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold" id="createBeetleModalLabel">Создать новую жужелицу</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?= get_form('new') ?>
            </div>
        </div>
    </div>

    <!-- Подключение библиотек Grid.js и Bootstrap для функциональности таблицы и модальных окон -->
    <script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/TableManager.js"></script>
    <script>
        const manager = new TableManager('geo');
        $(document).ready(() => {
            manager.init();
            let params = new URLSearchParams(window.location.search);
            const region = params.getAll('regions[]');
            if (region) $('#regions_select').val(region).trigger('change');
            console.log(document.getElementById('filters') )
            $('.gridjs-head')[0].append(document.getElementById('filters'))
            
        });

        function resetFilters() {
            $('#regions_select').val("").trigger('change');
            window.location.replace(window.location.pathname);
        }
    </script>
</body>

</html>