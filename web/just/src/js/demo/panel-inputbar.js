/*
 *  DataTable for Panel with Inpur Bar
 *  ui-panels.html
 */


$(document).ready(function() {

  // Updating Table Info String
  updateTableInfo = function(settings) {
    $('#persons-table_info').hide();

    if (settings) {
      $('#persons-table-info').html($(settings.aanFeatures.i).html())
    }
  }

  // DataTable Init
  var table = $('#persons-table')
  .on('init.dt', function (e, settings) {
    updateTableInfo(settings);
  })
  .on('draw.dt', function (e, settings) {
    updateTableInfo(settings);
  })
  .DataTable({
    'dom': 'ti',
    'select': {
      'style': 'multi',
      'selector': 'td:first-child input[type="checkbox"], tr'
    }
  });

  // Rows select Handle
  table.on('select', function ( e, dt, type, indexes ) {
    updateTableInfo(dt.settings()[0]);
    indexes.forEach(function(row) {
      $(dt.row(row).node()).find('input[type="checkbox"]').prop('checked', true);
    })
  })

  // Rows deselect Handle
  table.on('deselect', function ( e, dt, type, indexes ) {
    updateTableInfo(dt.settings()[0]);
    indexes.forEach(function(row) {
      $(dt.row(row).node()).find('input[type="checkbox"]').prop('checked', false);
    })
  })

  // Table Search
  $('#persons-table-search').on('keyup', function () {
    table.search($(this).val()).draw();
  });

});
