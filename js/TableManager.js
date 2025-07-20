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
    this.bindModalEvents();
  }

  initSelect2() {
    $('.multiple-select')
      .select2({ placeholder: 'Выберите опции' })
      .on('select2:unselect', function (e) {
        let id = this.id;
        const isRegions = id.endsWith('regions');
        const modal = id.split('_')[0];
        const $points = $(`#${modal}_points`);
        const pointsValues = $points.val();
        const region = isRegions ? e.params.data.id : $(e.params.data.element).attr('data-region');

        let regionOptions = [];

        $points.find(`option[data-region="${region}"]`).each((_, option) => {
          if (pointsValues.includes(option.value)) {
            isRegions ? pointsValues.splice(pointsValues.indexOf(option.value), 1) : regionOptions.push(option);
          }
        });

        if (isRegions) {
          $points.val(pointsValues).trigger('change');
        } else if (!regionOptions.length) {
          const regions = $(`#${modal}_regions`).val();
          regions.splice(regions.indexOf(region), 1);
          $(`#${modal}_regions`).val(regions).trigger('change');
        }
      })
      .on('select2:select', function (e) {
        if (this.id.endsWith('points')) {
          const modal = this.id.split('_')[0];
          const $regions = $(`#${modal}_regions`);
          const region = $(e.params.data.element).attr('data-region');
          let selectedRegions = $regions.val();
          if (!selectedRegions.includes(region)) {
            selectedRegions.push(region);
            $regions.val(selectedRegions).trigger('change');
          }
        }
      });
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
      ['ID', 'id'],
      ['Подсемейство', 'family'],
      ['Триба', 'tribe'],
      ['Род', 'genus'],
      ['Подрод', 'subgenus'],
      ['Вид', 'name'],
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
      let isHidden = (type == 'geo' && ecoColumns.includes(column)) || (type == 'eco' && geoColumns.includes(column)) || column[0] == 'ID';
      columnsConfig.push({
        name: column[0],
        id: column[1],
        visible: !isHidden,
      });
    }
    columnsConfig.push({
      name: 'Распространение',
      id: 'description',
      visible: false,
    });
    columnsConfig.push({
      name: 'Действия',
      sort: false,
      visible: true,
      formatter: (_, row) => {
        const originalRowData = row.cells.map((c) => c.data);
        const rowIndex = this.beetles.findIndex((b) => b[0] === originalRowData[0]);
        return gridjs.html(`<button class="btn btn-secondary btn-sm view-details-btn" data-bs-toggle="modal" data-bs-target="#detailsModal" data-row-index="${rowIndex}">Подробнее</button>`);
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
    const currentSearch = document.querySelector('.gridjs-search input')?.value || '';

    const { columns, data } = this.getFiltered();
    let table = document.getElementById('data_table');
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

    this.grid.updateConfig({ columns: columns, search: { keyword: currentSearch } }).forceRender();
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
        $(row).find(`td:eq(${visibleIndex}), th:eq(${visibleIndex})`).addClass('highlight-column');
      });
    });

    $('.form-switch').on('mouseleave', function () {
      $('#table-wrapper table td, #table-wrapper table th').removeClass('highlight-column');
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

  bindModalEvents() {
    $('#detailsModal').on('show.bs.modal', function (e) {
      const button = e.relatedTarget;
      const rowIndex = $(button).attr('data-row-index');
      const original = manager.beetles[rowIndex];
      const beetle = original.slice();
      beetle.splice(1, 2);
      let children = $('#edit_form')
        .find('input, select, textarea')
        .filter((i, el) => el.id);
      $.each(children, function (i, child) {
        let value = beetle[i];
        if (!['id', 'name', 'description'].includes(child.name) && value != '') {
          let options = [];
          value.split(', ').map((element) => {
            if (child.name == 'regions' && element != 'Улан-Удэ') element += ' район';
            options.push($(`#${child.id} option[name="${element}"]`).val());
          });
          value = options;
        }
        $(child).val(value).trigger('change');
      });
    });

    $('#edit_genus, #new_genus').change(function (e) {
      const modal = this.id.split('_')[0];
      $(`#${modal}_subgenus`).val('');
    });

    $('#edit_subgenus, #new_subgenus').change(function (e) {
      const modal = this.id.split('_')[0];
      const selectedOption = $(this).find(`option[value="${this.value}"]`)[0];
      if (selectedOption) {
        const genus = selectedOption.getAttribute('data-genus');
        if (genus) $(`#${modal}_genus`).val(genus);
      }
    });

    $('#createBeetleModal').on('show.bs.modal', function (e) {
      $.each($('#new_form').find('input, select, textarea'), function (_, element) {
        $(element).val('');
      });
    });
  }
}

function submitForm() {
  const button = event.target;
  const form = $(button).parent().parent().find('form')[0];
  $(form.action).val(button.name);
  $(form).trigger('submit');
}
