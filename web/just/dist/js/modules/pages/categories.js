/**
 * Categories page module.
 * @module pages/categories
 * @author AdminBootstrap.com
 */

App.page.categories = function() {

  /**
   * @const {object} Constant css-selectors to link up with HTML markup.
   */
  var c = {
    TABLE: {
      TABLE: '#cats-table',
      INFO: '#cats-info',
      LENGTH: '#cats-length',
      SEARCH: '#cats-search',
      PAGINATION: '#cats-pagination',
      SELECTED: {
        BUTTONS: '#cats-selected',
        REMOVE: '#cats-remove',
        ACTIVATE: '#cats-activate',
        DEACTIVATE: '#cats-deactivate',
        DESELECT: '#cats-deselect'
      }
    },
    FILTER: {
      BUTTONS: {
        CONTAINER: '#cats-filter-button',
        RESET: '#cats-reset'
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
      checkboxes: true,
      dataTable: {
        'columnDefs': [
          { 'orderable': false, 'targets': [0, 6] }
        ],
        'order': [[4, 'desc']]
      }
    });
  }

  function bindUIActions() {
    // Reset filter and search
    $(document).on('click', c.FILTER.BUTTONS.RESET, function() {
      s.widgets.table.clearSearch();
    })

    // Remove Selected
    $(document).on('click', c.TABLE.SELECTED.REMOVE, function() {
      s.widgets.table.getSelected().remove().draw();
      handleSelectedBtn();
    })

    // Activate Selected
    $(document).on('click', c.TABLE.SELECTED.ACTIVATE, function() {
      activateCats();
    })

    // Deactivate Selected
    $(document).on('click', c.TABLE.SELECTED.DEACTIVATE, function() {
      deactivateCats();
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
        $(c.FILTER.BUTTONS.CONTAINER).append('<button id="cats-reset" class="btn btn-default"><i class="fa fa-refresh"></i></button>')
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
  function activateCats() {
    var cats = s.widgets.table.getSelected();
    items.every(function() {
      $(this.node()).find('.cats-status')
        .attr('data-filter', '1').attr('data-sort', '1')
        .html('<span class="label label-success">Active</span>');
      this.invalidate();
    })
  }

  // Deactivate Selected items
  function deactivateCats() {
    var cats = s.widgets.table.getSelected();
    items.every(function() {
      $(this.node()).find('.cats-status')
        .attr('data-filter', '0').attr('data-sort', '0')
        .html('<span class="label label-danger">Disabled</span>');
      this.invalidate();
    })
  }

  function init() {
    initWidgets();
    bindTableEvents();
    bindUIActions();
  }

  init();

  return s;

}();
