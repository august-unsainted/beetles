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
    <div class="container-fluid py-4 px-3">
        <!-- Карточка с контентом -->
        <div class="card border-0">
            <div class="card-body">
                <section class="mb-4">
                    <div
                        class="d-flex flex-column flex-md-row px-0 justify-content-between align-items-start gap-3 w-100">

                        <!-- Заголовок и кнопка -->
                        <div class="w-100 w-md-50">
                            <h1 class="fw-bold text-primary mb-3">Интерактивная таблица жужелиц</h1>
                            <button type="button" class="btn btn-primary w-100 w-md-auto" style="min-width: 180px;"
                                data-bs-toggle="modal" data-bs-target="#createBeetleModal">
                                + Создать жужелицу
                            </button>
                        </div>
                        <?= get_columns_fieldset() ?>
                    </div>
                </section>

                <?= get_table() ?>

                <!-- Основная таблица -->
                <div id="table-wrapper" class="rounded"></div>
            </div>
        </div>
    </div>

    <!-- Модальное окно: Создать район/пункт -->
    <div class="modal fade" id="createGeoModal" tabindex="-1" aria-labelledby="createGeoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createGeoModalLabel">Создать новый элемент</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Форма охватывает body и footer -->
                <form id="createGeoForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="geoName" class="form-label">Название</label>
                            <input type="text" class="form-control" id="geoName" name="geoName" required>
                        </div>

                        <div class="mb-3" id="regionSelectGroup">
                            <label for="regionSelect" class="form-label">Район</label>
                            <?= get_select('regionsGeo', 'regions') ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- Кнопка "Отмена" — просто закрывает модалку -->
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                        <!-- Кнопка "Сохранить" — отправляет форму -->
                        <button type="submit" class="btn btn-primary btn-sm">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Модальное окно для просмотра и редактирования деталей жужелицы -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold" id="detailsModalLabel">Детали жужелицы</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?= get_form("edit") ?>
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
                <?= get_form("new") ?>

            </div>
        </div>
    </div>


    <!-- Подключение библиотек Grid.js и Bootstrap для функциональности таблицы и модальных окон -->
    <script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/TableManager.js"></script>
    <script>
        const manager = new TableManager('eco');
        $(document).ready(() => manager.init());
    </script>

</body>

</html>