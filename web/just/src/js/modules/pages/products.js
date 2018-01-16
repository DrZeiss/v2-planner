/**
 * Products page module.
 * @module pages/products
 * @author AdminBootstrap.com
 */

App.page.products = function() {

  /**
   * @const {object} Constant css-selectors to link up with HTML markup.
   */
  var c = {
    TABLE: {
      TABLE: '#items-table',
      INFO: '#items-info',
      LENGTH: '#items-length',
      SEARCH: '#items-search',
      PAGINATION: '#items-pagination',
      EXPORT: {
        BUTTONS: '#items-export',
        FILE: 'products-' + moment().format('MMDDYYYY')
      },
      SELECTED: {
        BUTTONS: '#items-selected',
        REMOVE: '#items-remove',
        ACTIVATE: '#items-activate',
        DEACTIVATE: '#items-deactivate',
        DESELECT: '#items-deselect'
      }
    },
    FILTER: {
      CATEGORY: '#items-category',
      PRICE: '#items-price',
      DATE: '#items-date',
      ACTIVE: '#items-active',
      BUTTONS: {
        CONTAINER: '#items-filter-buttons',
        RESET: '#items-reset'
      }
    }
  }

  /**
   * @var {object} Module settings object.
   */
  var s = {
    widgets: {}
  };

  function initWidgets() {
    // DataTable Widget
    s.widgets.table = new App.classes.TableWidget({
      table: c.TABLE.TABLE,
      info: c.TABLE.INFO,
      search: c.TABLE.SEARCH,
      length: c.TABLE.LENGTH,
      pagination: c.TABLE.PAGINATION,
      buttons: c.TABLE.EXPORT.BUTTONS,
      checkboxes: true,
      dataTable: {
        'columnDefs': [
          { 'orderable': false, 'targets': [0, 5, 7] }
        ],
        'order': [[4, 'desc']],
        'buttons': {
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'PDF',
              title: c.TABLE.EXPORT.FILE,
              exportOptions: {
                columns: ':not(.items-img):not(.items-action)'
              }
            },
            {
              extend: 'excelHtml5',
              text: 'Excel',
              title: c.TABLE.EXPORT.FILE,
              exportOptions: {
                columns: ':not(.items-img):not(.items-action)'
              }
            },
            {
              extend: 'csvHtml5',
              text: 'CSV',
              title: c.TABLE.EXPORT.FILE,
              exportOptions: {
                columns: ':not(.items-img):not(.items-action)'
              }
            },
            {
              extend: 'print',
              text: 'Print',
              exportOptions: {
                columns: ':not(.items-img):not(.items-action)'
              }
            },
            {
              extend: 'copyHtml5',
              text: 'Copy',
              exportOptions: {
                columns: ':not(.items-img):not(.items-action)'
              }
            }
          ],
          dom: {
            container: {
              tag: 'ul',
              className: 'dropdown-menu'
            },
            button: {
              tag: 'li',
              className: ''
            },
            buttonLiner: {
              tag: 'a'
            }
          }
        }
      }
    });

    // TableFilter Widget
    s.widgets.filter = new App.classes.TableFilter({
      dataTable: s.widgets.table.dataTable,
      filters: {
        category: {
          type: 'select',
          el: c.FILTER.CATEGORY,
          column: 2,
          placeholder: 'All Categories'
        },
        price: {
          type: 'slider-range',
          el: c.FILTER.PRICE,
          column: 3
        },
        date: {
          type: 'date-range',
          el: c.FILTER.DATE,
          column: 4
        },
        active: {
          type: 'checkbox',
          el: c.FILTER.ACTIVE,
          column: 6
        }
      },
      types: {}
    })
  }

  function initPlugins() {
    // Fancybox
    $(".fancybox").fancybox({
  		prevEffect	: 'none',
  		nextEffect	: 'none',
      padding : 0,
  		helpers	: {
  			thumbs	: {
  				width	: 50,
  				height	: 50
  			}
  		}
  	});

    // Proper tabs handling.
    // (Since we have 2 sets of same tab-toggles inside their tab-panes
    // we need to trigger both of them while switching for keeping them
    // in proper states)
    $('.tables-tabs a').on('click', function() {
      var tab = $(this).attr('href');
      if ($(tab).length) {
        $('.tables-tabs a[href="'+tab+'"]').tab('show');
      }
      return false;
    })
  }

  function bindUIActions() {
    // Reset filter and search
    $(document).on('click', c.FILTER.BUTTONS.RESET, function() {
      s.widgets.table.clearSearch();
      s.widgets.filter.reset();
    })

    // Remove Selected
    $(document).on('click', c.TABLE.SELECTED.REMOVE, function() {
      s.widgets.table.getSelected().remove().draw();
      handleSelectedBtn();
    })

    // Activate Selected
    $(document).on('click', c.TABLE.SELECTED.ACTIVATE, function() {
      activateItems();
    })

    // Deactivate Selected
    $(document).on('click', c.TABLE.SELECTED.DEACTIVATE, function() {
      deactivateItems();
    })

    // Deselect Selected
    $(document).on('click', c.TABLE.SELECTED.DESELECT, function() {
      s.widgets.table.getSelected().deselect();
      handleSelectedBtn();
    })
  }

  function bindTableEvents() {
    var table = s.widgets.table.dataTable;

    table.on('search.dt', function(e, settings) {
      handleResetBtn();
    })

    table.on('select', function(e, dt, type, indexes) {
      handleSelectedBtn();
    })
    table.on('deselect', function(e, dt, type, indexes) {
      handleSelectedBtn();
    })
  }

  // Create/Remove the "Reset Filter" Button
  function handleResetBtn() {
    var dt = s.widgets.table.dataTable;
    if (dt.page.info().recordsTotal > dt.page.info().recordsDisplay) {
      if (!$(c.FILTER.BUTTONS.RESET).length) {
        $(c.FILTER.BUTTONS.CONTAINER).append('<button id="items-reset" class="btn btn-default"><i class="fa fa-refresh"></i></button>')
      }
    } else {
      $(c.FILTER.BUTTONS.RESET).remove();
    }
  }

  // Enable/Disable the "Selected" Button
  function handleSelectedBtn() {
    var count = s.widgets.table.getSelected().count();
    if (count) {
      $(c.TABLE.SELECTED.BUTTONS).removeClass('disabled');
    } else {
      $(c.TABLE.SELECTED.BUTTONS)
        .addClass('disabled')
        .closest('.dropdown').removeClass('open');
    }
  }

  // Activate Selected items
  function activateItems() {
    var items = s.widgets.table.getSelected();
    items.every(function() {
      $(this.node()).find('.items-status')
        .attr('data-filter', '1').attr('data-sort', '1')
        .html('<span class="label label-success">Active</span>');
      this.invalidate();
    })
  }

  // Deactivate Selected items
  function deactivateItems() {
    var items = s.widgets.table.getSelected();
    items.every(function() {
      $(this.node()).find('.items-status')
        .attr('data-filter', '0').attr('data-sort', '0')
        .html('<span class="label label-danger">Disabled</span>');
      this.invalidate();
    })
  }

  function init() {
    initPlugins();
    initWidgets();
    bindTableEvents();
    bindUIActions();
  }

  init();

  return s;

}();
