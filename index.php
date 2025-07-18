<?php
require_once('php/connect_db.php');
require_once('php/utils.php');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Жужелицы Бурятии</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="css/select.css">
</head>

<body>
    <div class="container py-4">
        <h1 class="mb-4 text-center fw-bold">Виды жужелиц</h1>
        <div class="d-flex justify-content-center mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBeetleModal">
                Создать новую жужелицу
            </button>
        </div>

        <?= get_table() ?>

        <!-- Контейнер для таблицы -->
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
                <?= get_form("beetleDetails") ?>
                <div class="modal-footer">
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Удалить</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" name="action" value="apply" class="btn btn-primary btn-sm">Сохранить
                        изменения</button>
                </div>
                </form>
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
                <?= get_form('createBeetle') ?>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-success btn-sm" id="createBeetleBtn">Создать</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для подтверждения сохранения изменений -->
    <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-labelledby="confirmSaveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold" id="confirmSaveModalLabel">Несохранённые изменения</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Данные не сохранены. Сохранить изменения?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                        id="cancelSaveBtn">Отмена</button>
                    <button type="button" class="btn btn-primary btn-sm" id="confirmSaveBtn">Сохранить</button>
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