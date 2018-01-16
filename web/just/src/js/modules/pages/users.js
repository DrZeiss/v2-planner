/**
 * Users page module.
 * @module pages/users
 * @author AdminBootstrap.com
 */

App.page.users = function() {

  /**
   * @const {object} Constant css-selectors to link up with HTML markup.
   */
  var c = {
    TABLE: {
      TABLE:      '#users-table',
      INFO:       '#users-info',
      LENGTH:     '#users-length',
      SEARCH:     '#users-search',
      PAGINATION: '#users-pagination',
      ACTIONS: {
        REMOVE:   '.users-remove',
        ACTIVATE: '.users-activate',
        DISABLE:  '.users-disable'
      }
    },
    FILTER: {
      GROUPS: '#users-groups',
      DATE:   '#users-date',
      STATUS: 'input[name="users-status"]',
      CONTACTS: {
        ADDRESS:  '#users-contacts-address',
        PHONE:    '#users-contacts-phone',
        FACEBOOK: '#users-contacts-facebook',
        TWITTER:  '#users-contacts-twitter'
      },
      RESET:  '#users-filter-reset'
    },
    PREVIEW: {
      CONTAINER: '.st-users-preview',
      FULLNAME:  '.st-users-preview__fullname',
      USERNAME:  '.st-users-preview__username',
      PHOTO:     '.st-users-preview__photo .thumbnail'
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
      dataTable: {
        'columnDefs': [
          { 'orderable': false, 'targets': [2, 5] },
          { "visible": false, "targets": [6] }
        ],
        'pagingType': 'numbers',
        'order': [[0, 'asc']],
        'select': {
          'style': 'single',
          'selector': 'td:not(.st-users__action)'
        }
      }
    });

    // TableFilter Widget
    s.widgets.filter = new App.classes.TableFilter({
      dataTable: s.widgets.table.dataTable,
      filters: {
        groups: {
          type: 'tags',
          el: c.FILTER.GROUPS,
          column: 1,
          placeholder: 'Groups'
        },
        date: {
          type: 'date-range',
          el: c.FILTER.DATE,
          column: 3
        },
        status: {
          type: 'radio',
          el: c.FILTER.STATUS,
          column: 4
        },
        contacts: {
          type: 'array',
          el: '',
          column: 2
        }
      },
      types: {
        'array': {
          init: function(f, dt) {
            for(var contact in c.FILTER.CONTACTS) {
              $(c.FILTER.CONTACTS[contact]).on('change', function() {
                dt.draw();
              });
            }
          },
          reset: function(f, dt) {
            for(var contact in c.FILTER.CONTACTS) {
              $(c.FILTER.CONTACTS[contact]).prop('checked', false);
            }
          },
          compare: function(f, dt, row) {
            var filter = {
                  1: $(c.FILTER.CONTACTS.ADDRESS).prop('checked'),
                  2: $(c.FILTER.CONTACTS.PHONE).prop('checked'),
                  3: $(c.FILTER.CONTACTS.FACEBOOK).prop('checked'),
                  4: $(c.FILTER.CONTACTS.TWITTER).prop('checked')
                },
                values = JSON.parse(row[f.column]);

            for(var contact in filter) {
              if (filter[contact] && (values.indexOf(parseInt(contact)) == -1)) {
                return false;
              }
            }

      			return true;
          }
        }
      }
    })
  }

  function bindUIActions() {
    // Reset filter and search
    $(document).on('click', c.FILTER.RESET, function() {
      s.widgets.table.clearSearch();
      s.widgets.filter.reset();
    })

    // Remove User
    $(document).on('click', c.TABLE.ACTIONS.REMOVE, function() {
      var id = $(this).data('user'),
          row = s.widgets.table.dataTable.row('[data-user="' + id + '"]'),
          user = row.data();

      if (user) {
        bootbox.confirm({
          title: "Delete User?",
          message: "Do you really want to delete user <b>" + user[0]['display'] + "</b>?",
          backdrop: true,
          buttons: {
            cancel: {
              label: 'Cancel'
            },
            confirm: {
              label: 'Delete',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result) {
              row.remove().draw();
            }
          }
        });
      }
    })

    // Activate User
    $(document).on('click', c.TABLE.ACTIONS.ACTIVATE, function() {
      var id = $(this).data('user'),
          row = s.widgets.table.dataTable.row('[data-user="' + id + '"]'),
          user = row.data();
      if (user) {
        // Change status
        $(row.node())
          .find('.st-users__status')
            .attr('data-filter', '1').attr('data-sort', '1')
            .html('<span class="label label-success">Active</span>')
        // Change action
        $(row.node())
          .find('.st-users__action .users-activate')
            .removeClass('users-activate')
            .addClass('users-disable')
            .text('Disable')
        // Apply changes to dataTable
        row.invalidate();
      }
    })

    // Disable User
    $(document).on('click', c.TABLE.ACTIONS.DISABLE, function() {
      var id = $(this).data('user'),
          row = s.widgets.table.dataTable.row('[data-user="' + id + '"]'),
          user = row.data();
      if (user) {
        // Change status
        $(row.node())
          .find('.st-users__status')
            .attr('data-filter', '0').attr('data-sort', '0')
            .html('<span class="label label-danger">Disabled</span>')
        // Change action
        $(row.node())
          .find('.st-users__action .users-disable')
            .removeClass('users-disable')
            .addClass('users-activate')
            .text('Activate')
        // Apply changes to dataTable
        row.invalidate();
      }
    })
  }

  function bindTableEvents() {
    var table = s.widgets.table.dataTable;

    table.on('search.dt', function(e, settings) {
      handleResetBtn();
    })

    table.on('select', function(e, dt, type, indexes) {
      showUserPreview();
    })
  }

  function showUserPreview() {
    var table = s.widgets.table,
        user = table.getSelected() ? table.getSelected().data()[0] : null;
    if (user) {
      $(c.PREVIEW.CONTAINER).removeClass('hide');

      var names = JSON.parse(user[0]['@data-filter']);
      if (names && names.length) {
        $(c.PREVIEW.FULLNAME).text(names[0]);
        $(c.PREVIEW.USERNAME).text('@'+names[1]);
      }

      if (user[6]) {
        $(c.PREVIEW.PHOTO).css('background-image', 'url(' + user[6] + ')')
      }
    } else {
      $(c.PREVIEW.CONTAINER).addClass('hide');
    }
  }

  // Show/Hide the "Reset Filter" Button
  function handleResetBtn() {
    var dt = s.widgets.table.dataTable;
    if (dt.page.info().recordsTotal > dt.page.info().recordsDisplay) {
      $(c.FILTER.RESET).removeClass('hide');
    } else {
      $(c.FILTER.RESET).addClass('hide');
    }
  }

  function init() {
    initWidgets();
    bindTableEvents();
    bindUIActions();
    // Select first user
    s.widgets.table.dataTable.rows(0).select();
  }

  init();

  return s;

}();
