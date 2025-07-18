class TableManager {
  constructor(type) {
    this.grid = null;
    this.btnDetails = `<button class="btn btn-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#detailsModal">Подробнее</button>`;
    this.loadData();
    this.getColumns(type);
    this.columnIndexMap = {};
    this.name = name;
  }

  init() {
    this.initSelect2();
    this.syncColumnsState();
    this.renderTable();
    this.fillFieldset();
    this.bindEvents();
    this.bindToggleAllSwitch();
    this.bindHighlightColumns();
  }

  initSelect2() {
    $('.multiple-select').select2({ placeholder: 'Выберите опции' });
  }

  syncColumnsState({ to = 'checkboxes' } = {}) {
    this.columnIndexMap = {};
    this.allColumns.forEach((col, index) => {
      this.columnIndexMap[col.id] = index;
    });

    this.allColumns.forEach((col) => {
      if (col.id === 'actions') return;
      const checkbox = $(`.column-toggle[value="${col.id}"]`);
      if (!checkbox.length) return;
      if (to === 'checkboxes') {
        checkbox.prop('checked', col.visible);
      } else if (to === 'columns') {
        col.visible = checkbox.prop('checked');
      }
    });
  }

  loadData() {
    this.beetles = [];
    $('#data_table tbody tr').each((_, tr) => {
      const row = [];
      $(tr)
        .find('td')
        .each((_, td) => row.push(td.innerText.trim()));
      this.beetles.push(row);
    });
  }

  getColumns(type) {
    let columnsConfig = [];
    let baseColumns = [
      ['Подсемейство', 'family'],
      ['Триба', 'tribe'],
      ['Род', 'genus'],
      ['Подрод', 'subgenus'],
      ['Вид', 'species'],
    ];
    let geoColumns = [
      ['Районы', 'region'],
      ['Пункты', 'points'],
    ];
    let ecoColumns = [
      ['Широтная группа', 'width_range'],
      ['Долготная группа', 'long_range'],
      ['Экологическая группа', 'ecologic_group'],
      ['Трофическая группа', 'trophic_group'],
      ['Ярусная группа', 'tiered_group'],
    ];
    let allColumns = [...baseColumns, ...geoColumns, ...ecoColumns];

    for (let i = 0; i < allColumns.length; i++) {
      let column = allColumns[i];
      let isHidden = (type == 'geo' && ecoColumns.includes(column)) ||
          (type == 'eco' && geoColumns.includes(column));
      columnsConfig.push({
        name: column[0], id: column[1], visible: !isHidden,
      });
    }
    columnsConfig.push({
      name: 'Действия',
      sort: false,
      visible: true,
      formatter: (_, row) => {
        const originalRowData = row.cells.map((c) => c.data);
        const rowIndex = this.beetles.findIndex(
          (b) =>
            b[0] === originalRowData[0] &&
            b[1] === originalRowData[1] &&
            b[2] === originalRowData[2] &&
            b[3] === originalRowData[3]
        );
        return gridjs.html(
          `<button class="btn btn-secondary btn-sm view-details-btn" data-bs-toggle="modal" data-bs-target="#detailsModal" data-row-index="${rowIndex}">Подробнее</button>`
        );
      },
    });

    this.allColumns = columnsConfig;
  }

  getFiltered() {
    this.syncColumnsState({ to: 'columns' });
    let data = this.beetles;

    return {
      columns: this.allColumns.map((col) => ({
        name: col.name,
        sort: col.sort,
        hidden: !col.visible,
        formatter: col.formatter,
      })),
      data,
    };
  }

  renderTable() {
    const currentSearch =
      document.querySelector('.gridjs-search input')?.value || '';

    const { columns, data } = this.getFiltered();

    if (!this.grid) {
      this.grid = new gridjs.Grid({
        columns,
        data,
        resizable: true,
        sort: true,
        search: true,
        pagination: { enabled: true, limit: 25 },
        language: {
          search: { placeholder: 'Поиск по таблице...' },
          pagination: {
            previous: 'Назад',
            next: 'Вперед',
            showing: 'Показано с',
            to: 'по',
            of: 'из',
            results: 'результатов',
          },
          loading: 'Загрузка...',
          noRecordsFound: 'Записей не найдено',
        },
        className: {
          table: 'table table-bordered table-striped table-hover table-sm',
          thead: 'table-dark',
        },
        style: {
          td: { padding: '6px' },
          table: { fontSize: '14px' },
        },
      }).render(document.getElementById('table-wrapper'));
    }

    this.grid
      .updateConfig({ columns: columns, search: { keyword: currentSearch } })
      .forceRender();
  }

  bindEvents() {
    $('.column-toggle').on('change', () => this.renderTable());
  }

  bindToggleAllSwitch() {
    const toggleAll = $('#toggleAllColumns');
    toggleAll.on('change', () => {
      const isChecked = toggleAll.prop('checked');
      $('.column-toggle').prop('checked', isChecked).trigger('change');
    });
  }

  bindHighlightColumns() {
    const columns = this.allColumns;
    $('.form-switch').on('mouseenter', function () {
      const $input = $(this).find('input');
      const colId = $input.val();
      // console.log(colId)
      if (!$input.prop('checked')) return; // Если свич выкл — не подсвечиваем

      // Получаем массив id видимых колонок
      const visibleCols = columns.filter((c) => c.visible).map((c) => c.id);
      const visibleIndex = visibleCols.indexOf(colId);
      if (visibleIndex === -1) return;

      $('#table-wrapper table tr').each((_, row) => {
        $(row)
          .find(`td:eq(${visibleIndex}), th:eq(${visibleIndex})`)
          .addClass('highlight-column');
      });
    });

    $('.form-switch').on('mouseleave', function () {
      $('#table-wrapper table td, #table-wrapper table th').removeClass(
        'highlight-column'
      );
    });
  }

  fillFieldset() {
    this.allColumns.forEach((child) => {
      if (!['Вид', 'Действия'].includes(child.name) && child.visible) {
        let id = child.id;
        let name = child.name.replace(' группа', '');
        $('#chooseColumns').append(
          `<div class="form-check form-switch">
                <input class="form-check-input column-toggle" type="checkbox" value="${id}"
                    checked id="check_${id}">
                <label class="form-check-label" for="check_${id}">${name}</label>
            </div>`
        );
      }
    });
  }
}
