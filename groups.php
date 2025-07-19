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

                        <!-- Настройки отображения -->
                        <fieldset class="w-100 w-md-50" style="min-width: 260px;">
                            <legend class="fs-6 text-muted mb-2">Признаки</legend>
                            <div class="d-flex flex-wrap gap-2" id="chooseColumns">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggleAllColumns" checked>
                                    <label class="form-check-label" for="toggleAllColumns">
                                        <strong>Все признаки</strong>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </section>

                <?= get_table() ?>

                <!-- Основная таблица -->
                <div id="table-wrapper" class="rounded"></div>
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
                <div class="modal-footer">
                    <button type="button" name="delete" onclick="submitForm()"
                        class="btn btn-danger btn-sm">Удалить</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" name="apply" onclick="submitForm()" class="btn btn-primary btn-sm">Сохранить
                        изменения</button>
                </div>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-success btn-sm" id="createBeetleBtn">Создать</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Подключение библиотек Grid.js и Bootstrap для функциональности таблицы и модальных окон -->
    <script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/TableManager.js"></script>
    <script>
        const manager = new TableManager('geo');
        $(document).ready(() => manager.init());
    </script>

</body>

</html>