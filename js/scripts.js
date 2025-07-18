const TableManager = {
  beetles: [],
  grid: null,
  btnDetails: `<button class="btn btn-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#detailsModal">Подробнее</button>`,
  allColumns: [
    { name: 'Подсемейство' },
    { name: 'Триба' },
    { name: 'Род' },
    { name: 'Подрод' },
    { name: 'Вид' },
    { name: 'Широтная группа' },
    { name: 'Долготная группа' },
    { name: 'Экологическая группа' },
    { name: 'Трофическая группа' },
    { name: 'Ярусная группа' },
    {
      name: 'Действия',
      sort: false,
      formatter: () => gridjs.html(TableManager.btnDetails),
    },
  ],

  init() {
    this.initSelect2();
    this.loadData();
    this.renderTable();
    this.bindEvents();
  },

  initSelect2() {
    $('.regions-select').select2({ placeholder: 'Выберите опции' });
    $('.points-select').select2({ placeholder: 'Выберите опции' });
  },

  loadData() {
    this.beetles = [];
    $('#data_table tbody tr').each((_, tr) => {
      const row = [];
      $(tr)
        .find('td')
        .each((_, td) => row.push(td.innerText.trim()));
      this.beetles.push(row);
    });
  },

  getVisibleIndexes() {
    return new Set(
      $('.column-toggle:checked')
        .map((_, el) => +el.value)
        .get()
    );
  },

  getFilteredColumns() {
    const visible = this.getVisibleIndexes();
    return this.allColumns.filter((_, i) => i < 5 || i > 9 || visible.has(i));
  },

  getFilteredData() {
    const visible = this.getVisibleIndexes();
    return this.beetles.map((row) =>
      row.filter((_, i) => i < 5 || i > 9 || visible.has(i))
    );
  },

  renderTable() {
    const currentSearch =
      document.querySelector('.gridjs-search input')?.value || '';
    if (this.grid) {
      this.grid.destroy();
      this.grid = null;
    }

    this.grid = new gridjs.Grid({
      columns: this.getFilteredColumns(),
      data: this.getFilteredData(),
      sort: true,
      search: {
        selector: (cell) => String(cell).toLowerCase().trim(),
      },
      pagination: { enabled: true, limit: 10 },
      className: {
        table: 'table table-bordered table-hover table-sm',
        thead: 'thead-dark',
      },
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
      style: {
        td: { padding: '6px' },
        table: { fontSize: '12px' },
      },
    }).render(document.getElementById('table-wrapper'));

    this.grid
      .updateConfig({ search: { keyword: currentSearch } })
      .forceRender();
  },

  bindEvents() {
    $('.column-toggle').on('change', () => this.renderTable());
  },
};

$(document).ready(() => TableManager.init());
