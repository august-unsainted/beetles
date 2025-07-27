class TableManager {
  constructor(type) {
    this.grid = null;
    this.btnDetails = `<button class="btn btn-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#detailsModal">Подробнее</button>`;
    this.loadData();
    this.getColumns(type);
    this.columnIndexMap = {};
    this.name = name;
  }

  detailsFormatter = (_, row) => {
    const originalRowData = row.cells.map((c) => c.data);
    const rowIndex = this.beetles.findIndex((b) => b[0] === originalRowData[0]);
    return gridjs.html(`<button class="btn btn-secondary btn-sm view-details-btn" data-bs-toggle="modal" data-bs-target="#detailsModal" data-row-index="${rowIndex}">Подробнее</button>`);
  };

  init() {
    this.initSelect2();
    this.syncColumnsState();
    this.renderTable();
    this.renderColumnControls();
    this.bindHighlightColumns();
    this.bindModalEvents();
    this.bindSwitchEvents();
  }

  initSelect2() {
    ['details', 'createBeetle'].forEach((modalName) => {
      $.each($(`#${modalName}Modal .form-select`), function (_, element) {
        $(element).select2({ dropdownParent: $(element).parent() });
      });
    });

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
        checkbox.prop('checked', !col.hidden);
      } else if (to === 'columns') {
        col.hidden = !checkbox.prop('checked');
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
    if (localStorage.getItem('columns')) {
      // localStorage.clear()
      const storedColumns = JSON.parse(localStorage.getItem('columns'));
      if (storedColumns) {
        storedColumns[15].formatter = this.detailsFormatter;
        this.allColumns = storedColumns;
        return;
      }
    }
    let columnsConfig = [];
    let baseColumns = [
      ['ID', 'id'],
      ['Подсемейство', 'family', 'main'],
      ['Триба', 'tribe', 'main'],
      ['Род', 'genus', 'main'],
      ['Подрод', 'subgenus', 'main'],
      ['Вид', 'name'],
      ['Синонимы', 'synonyms', 'extra'],
    ];
    let geoColumns = [
      ['Районы', 'region', 'geo'],
      ['Пункты', 'points', 'geo'],
    ];
    let ecoColumns = [
      ['Широтная группа', 'width_range', 'eco'],
      ['Долготная группа', 'long_range', 'eco'],
      ['Экологическая группа', 'ecologic_group', 'eco'],
      ['Трофическая группа', 'trophic_group', 'eco'],
      ['Ярусная группа', 'tiered_group', 'eco'],
    ];
    let allColumns = [...baseColumns, ...geoColumns, ...ecoColumns, ['Распространение', 'description', 'extra']];

    for (let i = 0; i < allColumns.length; i++) {
      let column = allColumns[i];
      columnsConfig.push({
        name: column[0],
        id: column[1],
        group: column[2] || '',
        hidden: (type == 'geo' && ecoColumns.includes(column)) || (type == 'eco' && geoColumns.includes(column)) || ['ID', 'Синонимы', 'Распространение'].includes(column[0]),
      });
    }
    columnsConfig.push({ name: 'Действия', sort: false, hidden: false, formatter: this.detailsFormatter });

    this.allColumns = columnsConfig;
  }

  renderTable() {
    const currentSearch = document.querySelector('.gridjs-search input')?.value || '';
    this.syncColumnsState({ to: 'columns' });
    const data = this.beetles;
    const columns = this.allColumns;
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
    localStorage.setItem('columns', JSON.stringify(columns));
    let $hidden = $('#filters_hidden');
    $hidden.clone().attr('style', '').attr('id', 'filters').appendTo('.gridjs-head');
    const $select = $('#filters select').select2({ placeholder: 'Выберите опции' });
    $select.val($hidden.val()).trigger('change')
  }

  bindSwitchEvents() {
    const toggleAll = $('#toggleAllColumns');
    toggleAll.on('change', () => {
      const isChecked = toggleAll.prop('checked');
      $('.column-toggle').prop('checked', isChecked).trigger('change');
    });
    $('.column-toggle').on('change', () => this.renderTable());
  }

  highlightColumn(colId) {
    const visibleCols = this.allColumns.filter((c) => !c.hidden).map((c) => c.id);
    const index = visibleCols.indexOf(colId);
    if (index === -1) return;
    $('#table-wrapper table tr').each((_, row) => {
      $(row).find(`td:eq(${index}), th:eq(${index})`).addClass('highlight-column');
    });
  }

  bindHighlightColumns() {
    $('.form-switch').on('mouseenter', (e) => {
      const $input = $(e.currentTarget).find('input');
      if ($input.hasClass('toggle-all-local')) {
        const groupId = $input.attr('id').replace('toggleAll', '').toLowerCase();
        this.allColumns.filter((c) => c.group === groupId && !c.hidden && c.id !== 'species').forEach((c) => this.highlightColumn(c.id));
      } else {
        const colId = $input.val();
        if (!$input.prop('checked')) return;
        this.highlightColumn(colId);
      }
    });

    $('.form-switch').on('mouseleave', () => $('#table-wrapper table td, #table-wrapper table th').removeClass('highlight-column'));
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
        if (!['id', 'name', 'description', 'synonyms'].includes(child.name) && value != '') {
          let options = [];
          value.split(', ').map((element) => {
            if (child.name == 'regions[]' && element != 'Улан-Удэ') element += ' район';
            options.push($(`#${child.id} option[name="${element}"]`).val());
          });
          value = options;
        }
        $(child).val(value).trigger('change');
      });
    });

    $('#edit_genus, #new_genus').on('select2:select', function (e) {
      const modal = this.id.split('_')[0];
      $(`#${modal}_subgenus`).val('').trigger('change');
    });

    $('#edit_subgenus, #new_subgenus').on('select2:select', function (e) {
      const modal = this.id.split('_')[0];
      const selectedOption = $(this).find(`option[value="${this.value}"]`)[0];
      if (selectedOption) {
        const genus = selectedOption.getAttribute('data-genus');
        if (genus) $(`#${modal}_genus`).val(genus).trigger('change');
      }
    });

    $('#createBeetleModal').on('show.bs.modal', function (e) {
      $.each($('#new_form').find('input, select, textarea'), function (_, element) {
        $(element).val('').trigger('change');
      });
    });
  }

  renderColumnControls() {
    $('.nav-link').attr('aria-selected', false);
    $('#main-tab').attr('aria-selected', true).addClass('active');
    $('#main').addClass('show active');
    this.groups = ['main', 'eco', 'geo', 'extra'];
    this.groups.forEach((group) => {
      const container = $(`#${group} .tab-pane-content`);
      const cols = this.allColumns.filter((c) => c.group === group && c.name !== 'Вид');
      const toggleId = `toggleAll${group}`;

      container.find('.column-toggle').closest('.form-check').remove();

      cols.forEach((col) => {
        const label = col.name.replace(' группа', '');
        container.append(`
            <div class="form-check form-switch">
              <input class="form-check-input column-toggle" type="checkbox" value="${col.id}" ${!col.hidden ? 'checked' : ''} id="check_${col.id}">
              <label class="form-check-label" for="check_${col.id}">${label}</label>
            </div>
          `);
      });

      const $toggle = $(`#${toggleId}`);
      $toggle.off('change').on('change', () => {
        const checked = $toggle.prop('checked');
        container.find('.column-toggle').prop('checked', checked);
        this.syncGlobalToggle();
        this.renderTable();
      });
    });

    $('#toggleAllGlobal')
      .off('change')
      .on('change', () => {
        const checked = $('#toggleAllGlobal').prop('checked');
        $('.column-toggle').prop('checked', checked);
        $('.toggle-all-local').prop('checked', checked);
        this.renderTable();
      });

    this.syncGlobalToggle();
  }

  syncGlobalToggle() {
    const allChecked = this.groups.every((group) => $(`#toggleAll${group}`).prop('checked'));
    $('#toggleAllGlobal').prop('checked', allChecked);
  }
}

function submitForm() {
  const button = event.target;
  const form = $(button).parent().parent().find('form')[0];
  if (button.name) {
    $(form.action).val(button.name);
  }
  $(form).trigger('submit');
}
