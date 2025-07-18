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
        <div>
            <form action="" method="get" style="display: flex; gap: 8px">
                <select name="region" id="region_select" class="form-select form-select-sm" style="width: auto">
                    <?php
                    $result = mysqli_query($link, "SELECT DISTINCT id, name from regions");
                    $values = mysqli_fetch_all($result);
                    foreach ($values as $value) {
                        echo "<option value='$value[0]'>$value[1]</option>";
                    }
                    ?>
                </select>
                <button class="btn btn-secondary btn-sm">Применить</button>
            </form>
            
        </div>

        <table id="data_table" style="display: none;">
            <tbody class="gridjs-tbody">
                <?php
                $where = isset($_GET['region']) ? 'WHERE p.region = ' . $_GET['region'] : '';
                $query = get_query($where);
                $result = mysqli_query($link, $query);
                $species = mysqli_fetch_all($result, MYSQLI_ASSOC);
                foreach ($species as $specie) {
                    $specie['id'];
                    echo "
                    <tr class='gridjs-tr'>
                        <td>$specie[id]</td>
                        <td>$specie[Подсемейство]</td>
                        <td>$specie[Триба]</td>
                        <td>$specie[Род]</td>
                        <td>$specie[Вид]</td>
                        <td>$specie[Районы]</td>
                        <td>$specie[Пункты_сбора]</td>
                    </tr>
                    ";
                }
                ?>
                <!-- <tr class="gridjs-tr">
                    <td data-column-id="family" class="gridjs-td">CARABINAE</td>
                    <td data-column-id="tribe" class="gridjs-td">PELOPHILINI</td>
                    <td data-column-id="genus" class="gridjs-td">Pelophila</td>
                    <td data-column-id="species" class="gridjs-td">Pelophila (s. str.) borealis</td>
                    <td data-column-id="district" class="gridjs-td">СЕВЕРО–БАЙКАЛЬСКИЙ РАЙОН</td>
                    <td data-column-id="location" class="gridjs-td">Аяя</td>
                    <td data-column-id="действия" class="gridjs-td"><span><button
                                class="btn btn-secondary btn-sm view-details-btn" data-bs-toggle="modal"
                                data-bs-target="#detailsModal" data-row-index="1">Подробнее</button></span></td>
                </tr> -->
            </tbody>
        </table>

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
                <div class="modal-body">
                    <!-- Форма для редактирования данных жужелицы -->
                    <form id="beetleDetailsForm" action="php/edit_beetle.php" method="post">
                        <input type="hidden" id="modalBeetleIndex">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modalFamily" class="form-label fw-medium">Семейство</label>
                                <select class="form-select form-select-sm" name="family" id="modalFamily">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from families");
                                    $families = mysqli_fetch_all($result);
                                    foreach ($families as $family) {
                                        echo "<option value='$family[0]'>$family[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modalTribe" class="form-label fw-medium">Триба</label>
                                <select class="form-select form-select-sm" name="tribe" id="modalTribe">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from tribes");
                                    $tribes = mysqli_fetch_all($result);
                                    foreach ($tribes as $tribe) {
                                        echo "<option value='$tribe[0]'>$tribe[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modalGenus" class="form-label fw-medium">Род</label>
                                <select class="form-select form-select-sm" name="genus" id="modalGenus">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from genus");
                                    $values = mysqli_fetch_all($result);
                                    foreach ($values as $value) {
                                        echo "<option value='$value[0]'>$value[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modalSpecies" class="form-label fw-medium">Вид</label>
                                <input type="text" class="form-control form-control-sm" name="species"
                                    id="modalSpecies">
                            </div>
                            <div class="col-md-6">
                                <label for="modalDistrict" class="form-label fw-medium">Район сбора</label>
                                <select class="form-select form-select-sm regions-select" name="regions[]" id="modalDistrict" multiple="multiple">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from regions");
                                    $values = mysqli_fetch_all($result);
                                    foreach ($values as $value) {
                                        echo "<option value='$value[0]'>$value[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modalLocation" class="form-label fw-medium">Точка сбора</label>
                                <select class="form-select form-select-sm points-select" name="points[]" id="modalLocation" multiple="multiple">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from points");
                                    $values = mysqli_fetch_all($result);
                                    foreach ($values as $value) {
                                        echo "<option value='$value[0]'>$value[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="modalNotes" class="form-label fw-medium">Примечание</label>
                                <textarea class="form-control form-control-sm" name="description" id="modalNotes"
                                    rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="action" value="delete"
                                class="btn btn-danger btn-sm">Удалить</button>
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Закрыть</button>
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
                <div class="modal-body">
                    <!-- Форма для ввода данных новой жужелицы -->
                    <form id="createBeetleForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modalFamily" class="form-label fw-medium">Семейство</label>
                                <select class="form-select form-select-sm" name="family">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from families");
                                    $families = mysqli_fetch_all($result);
                                    foreach ($families as $family) {
                                        echo "<option value='$family[0]'>$family[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modalTribe" class="form-label fw-medium">Триба</label>
                                <select class="form-select form-select-sm" name="tribe">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from tribes");
                                    $tribes = mysqli_fetch_all($result);
                                    foreach ($tribes as $tribe) {
                                        echo "<option value='$tribe[0]'>$tribe[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modalGenus" class="form-label fw-medium">Род</label>
                                <select class="form-select form-select-sm" name="genus">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from genus");
                                    $values = mysqli_fetch_all($result);
                                    foreach ($values as $value) {
                                        echo "<option value='$value[0]'>$value[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modalSpecies" class="form-label fw-medium">Вид</label>
                                <input type="text" class="form-control form-control-sm" name="species">
                            </div>
                            <div class="col-md-6">
                                <label for="modalDistrict" class="form-label fw-medium">Район сбора</label>
                                <select class="form-select form-select-sm regions-select" name="regions[]" multiple="multiple">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from regions");
                                    $values = mysqli_fetch_all($result);
                                    foreach ($values as $value) {
                                        echo "<option value='$value[0]'>$value[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modalLocation" class="form-label fw-medium">Точка сбора</label>
                                <select class="form-select form-select-sm points-select" name="points[]" multiple="multiple">
                                    <?php
                                    $result = mysqli_query($link, "SELECT * from points");
                                    $values = mysqli_fetch_all($result);
                                    foreach ($values as $value) {
                                        echo "<option value='$value[0]'>$value[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="modalNotes" class="form-label fw-medium">Примечание</label>
                                <textarea class="form-control form-control-sm" name="description"
                                    rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
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

    <script>
        $(document).ready(function () {
            $('.regions-select').select2({
                placeholder: 'Выберите опции'
            });
            $('.points-select').select2({
                placeholder: 'Выберите опции'
            });
            let params = new URLSearchParams(window.location.search);
            const region = params.get('region');
            if (region != '') {
                $('#region_select').val(region);
            }
        });

        let beetles_arr = []
        $('#data_table tr').each((_, tr) => {
            tr_arr = []
            $(tr).find('td').each((_, td) => {
                tr_arr.push(td.innerText);
            })
            beetles_arr.push(tr_arr)
        })
        // Исходные данные жужелиц в виде массива
        const beetles = beetles_arr;

        // Переменные для отслеживания изменений в данных
        let hasUnsavedChanges = false;
        let originalBeetleData = [];

        // Функция для получения уникальных значений из столбца данных
        function getUniqueValues(data, columnIndex) {
            const values = data.map(row => row[columnIndex]);
            return [...new Set(values)];
        }

        // Хранение уникальных значений для выпадающих списков
        let uniqueData = {
            'Семейство': getUniqueValues(beetles, 0),
            'Триба': getUniqueValues(beetles, 1),
            'Род': getUniqueValues(beetles, 2),
            'Район сбора': getUniqueValues(beetles, 4),
            'Точка сбора': getUniqueValues(beetles, 5)
        };

        // Конфигурация столбцов для таблицы Grid.js
        const columnsConfig = [
            { name: 'ID', id: 'species_id' },
            { name: 'Семейство', id: 'family' },
            { name: 'Триба', id: 'tribe' },
            { name: 'Род', id: 'genus' },
            { name: 'Вид', id: 'species' },
            { name: 'Район сбора', id: 'district' },
            { name: 'Точка сбора', id: 'location' },
            {
                name: 'Действия',
                sort: false,
                formatter: (cell, row) => {
                    const originalRowData = row.cells.map(c => c.data);
                    const rowIndex = beetles.findIndex(b =>
                        b[0] === originalRowData[0] &&
                        b[1] === originalRowData[1] &&
                        b[2] === originalRowData[2] &&
                        b[3] === originalRowData[3]
                    );
                    return gridjs.html(`<button class="btn btn-secondary btn-sm view-details-btn" data-bs-toggle="modal" data-bs-target="#detailsModal" data-row-index="${rowIndex}">Подробнее</button>`);
                }
            }
        ];

        // Инициализация таблицы Grid.js
        const grid = new gridjs.Grid({
            columns: columnsConfig,
            data: beetles,
            sort: true,
            search: true,
            pagination: {
                enabled: true,
                limit: 10
            },
            className: {
                table: 'table table-bordered table-striped table-hover table-sm',
                thead: 'table-dark'
            },
            language: {
                'search': { 'placeholder': 'Поиск по таблице...' },
                'pagination': {
                    'previous': 'Назад', 'next': 'Вперед', 'showing': 'Показано с',
                    'to': 'по', 'of': 'из', 'results': 'результатов'
                },
                'loading': 'Загрузка...',
                'noRecordsFound': 'Записей не найдено',
                'error': 'Произошла ошибка при загрузке данных'
            }
        }).render(document.getElementById("table-wrapper"));

        // Функция для отображения уведомлений (тостов)
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('notification-container');
            const toastId = `toast-${Date.now()}`;
            const toastHtml = `
                <div class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
            toast.show();
            toastElement.addEventListener('hidden.bs.toast', function () {
                toastElement.remove();
            });
        }

        // Функция для заполнения выпадающих списков в модальных окнах
        function populateModalSelects(modalId, currentBeetleData = []) {
            let names = ['Family', 'Tribe', 'Genus', 'District', 'Location']
            for (let i = 0; i < names.length - 1; i++) {
                $(modalId + names[i]).val(currentBeetleData[i + 1])
            }
        }

        // Обработчик открытия модального окна редактирования
        const detailsModal = document.getElementById('detailsModal');
        detailsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const rowIndex = button.getAttribute('data-row-index');
            const beetle = beetles[rowIndex];

            document.getElementById('modalBeetleIndex').value = rowIndex;
            populateModalSelects('modal', beetle);
            document.getElementById('modalSpecies').value = beetle[3];
            document.getElementById('modalNotes').value = beetle[6] || '';
            originalBeetleData = [...beetle];
            hasUnsavedChanges = false;
        });

        // Модальное окно и форма для создания новой записи
        const createBeetleModal = document.getElementById('createBeetleModal');
        const createBeetleForm = document.getElementById('createBeetleForm');

        // Функция для сохранения данных формы в localStorage
        function saveCreateFormData() {
            const formData = {
                newFamily: document.getElementById('newFamily').value,
                newTribe: document.getElementById('newTribe').value,
                newGenus: document.getElementById('newGenus').value,
                newSpecies: document.getElementById('newSpecies').value,
                newDistrict: document.getElementById('newDistrict').value,
                newLocation: document.getElementById('newLocation').value,
                newNotes: document.getElementById('newNotes').value
            };
            localStorage.setItem('createBeetleFormData', JSON.stringify(formData));
        }

        // Функция для восстановления данных формы из localStorage
        function restoreCreateFormData() {
            const savedData = localStorage.getItem('createBeetleFormData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                document.getElementById('newFamily').value = formData.newFamily || '';
                document.getElementById('newTribe').value = formData.newTribe || '';
                document.getElementById('newGenus').value = formData.newGenus || '';
                document.getElementById('newSpecies').value = formData.newSpecies || '';
                document.getElementById('newDistrict').value = formData.newDistrict || '';
                document.getElementById('newLocation').value = formData.newLocation || '';
                document.getElementById('newNotes').value = formData.newNotes || '';
            }
        }

        // Сохранение данных формы при их изменении
        createBeetleForm.addEventListener('input', saveCreateFormData);

        // Восстановление данных формы при открытии модального окна
        createBeetleModal.addEventListener('show.bs.modal', function () {
            document.getElementById('createBeetleForm').reset();
            populateModalSelects('new');
            restoreCreateFormData();
        });

        // Обработчик создания новой жужелицы
        document.getElementById('createBeetleBtn').addEventListener('click', function () {
            const newFamily = document.getElementById('newFamily').value;
            const newTribe = document.getElementById('newTribe').value;
            const newGenus = document.getElementById('newGenus').value;
            const newSpecies = document.getElementById('newSpecies').value;
            const newDistrict = document.getElementById('newDistrict').value;
            const newLocation = document.getElementById('newLocation').value;
            const newNotes = document.getElementById('newNotes').value;

            if (!newFamily || !newTribe || !newGenus || !newSpecies || !newDistrict || !newLocation) {
                showToast('Пожалуйста, заполните все обязательные поля.', 'danger');
                return;
            }

            const newBeetle = [newFamily, newTribe, newGenus, newSpecies, newDistrict, newLocation, newNotes];
            beetles.push(newBeetle);
            updateGridAndUniqueValues();
            localStorage.removeItem('createBeetleFormData');
            document.getElementById('createBeetleForm').reset();
            const modalInstance = bootstrap.Modal.getInstance(createBeetleModal);
            modalInstance.hide();
            showToast('Новая жужелица успешно добавлена!', 'success');
        });

        // Обновление таблицы и уникальных значений после изменений
        function updateGridAndUniqueValues() {
            uniqueData = {
                'Семейство': getUniqueValues(beetles, 0),
                'Триба': getUniqueValues(beetles, 1),
                'Род': getUniqueValues(beetles, 2),
                'Район сбора': getUniqueValues(beetles, 4),
                'Точка сбора': getUniqueValues(beetles, 5)
            };
            grid.updateConfig({ data: beetles.slice() }).forceRender();
        }
    </script>
</body>

</html>