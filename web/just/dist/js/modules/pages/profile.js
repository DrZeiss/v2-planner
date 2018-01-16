/**
 * Profile page module.
 * @module pages/profile
 * @author AdminBootstrap.com
 */

App.page.profile = function() {

  /**
   * @const {object} Constant css-selectors to link up with HTML markup.
   */
  var c = {
    ORDERS: {
      TABLE: '#profile-orders-table',
      PAGINATION: '#profile-orders-pagination'
    }
  }

  /**
   * @var {object} Module settings object.
   */
  var s = {
    widgets: {}
  };

  function initWidgets() {
    // Orders DataTable Init
    s.widgets.orders = new App.classes.TableWidget({
      table: c.ORDERS.TABLE,
      pagination: c.ORDERS.PAGINATION,
      dataTable: {
        'dom': 'tp',
        "pageLength": 11,
        'columns': [
            {
              data: "product",
              className: "text-nowrap"
            },
            {
              data: "price",
              className: "text-right",
              render: function(data, type, row) {
                return type === "display" ?
                  App.utils.formatNumber(data, 2, '$') :
                  data;
              }
            },
            {
              data: "date",
              className: "text-nowrap",
              render: function(data, type, row) {
                return type === "display" ?
                  moment.utc(+data).format('D MMM YYYY HH:mm') :
                  data;
              }
            },
            {
              data: "status",
              render: function(data, type, row) {
                if (!type === "display") {
                  return data;
                }

                return data ?
                  '<span class="label label-success">Completed</span>' :
                  '<span class="label label-default">In Process</span>';
              }
            },
            {
              data: "",
              render: function(data, type, row) {
                if (!type === "display") {
                  return "";
                }
                return '<a class="btn btn-default btn-xs">Details</a>';
              }
            }
        ],
        'columnDefs': [
          { 'orderable': false, 'targets': [4] }
        ],
        'order': [[2, 'desc']]
      }
    });
  }

  function initPlugins() {
    // Init jQuery.Sparkline Charts
    $('.st-profile__spark').sparkline('html', { enableTagOptions: true });

    // Responsive Tabs
    $('.st-profile__tabs').tabdrop()
  }

  function updateOrdersTable(data) {
    s.widgets.orders.dataTable
      .clear()
      .rows.add(data)
      .draw();
  }

  function getOrders(from, to) {
    var data = App.service.api.getSales(
      App.utils.getUTCTimeFromLocalTime(from.valueOf()),
      App.utils.getUTCTimeFromLocalTime(to.valueOf())
    );

    updateOrdersTable(data);
  }

  function bindUIActions() {}

  function init() {
    initPlugins();
    initWidgets();
    bindUIActions();
    getOrders(
      moment().subtract(1, 'year').hour(0).minute(0).second(0).millisecond(0),
      moment().hour(23).minute(59).second(59).millisecond(999)
    );
  }

  init();

  return s;

}();
