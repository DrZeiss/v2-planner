$(document).ready(function() {
  // Sparkline Plugin
  $('.sparkline').sparkline('html', { enableTagOptions: true });
  // Catching chart container resizeing by watching hidden iframe
  $(document.getElementById('resize-iframe').contentWindow).on('resize', function() {
    $('.sparkline').sparkline('html', { enableTagOptions: true });
  })

  // Table Widget
  new App.classes.TableWidget({
    table: '#tw-table',
    info: '#tw-info',
    search: '#tw-search',
    pagination: '#tw-pagination',
    checkboxes: true,
    dataTable: {
      //'dom': 'tip',
      'columnDefs': [
        { 'orderable': false, 'targets': [0] }
      ],
      'pageLength': 4,
      'pagingType': 'first_last_numbers',
      'language': {
        paginate: {
            first:    '«',
            previous: '‹',
            next:     '›',
            last:     '»'
        }
      },
      'order': [[1, 'asc']]
    }
  });

  // Settings Widget
  new App.classes.SettingsWidget('#w-settings', {
    controls: [
      {
        el: '#w-settings__backuper',
        type: 'switch'
      },
      {
        el: '#w-settings__optimizer',
        type: 'switch'
      },
      {
        el: '#w-settings__reports',
        type: 'switch'
      },
      {
        el: '#w-settings__send',
        type: 'switch'
      },
      {
        el: '#w-settings__driver',
        type: 'select2'
      },
      {
        el: '#w-settings__limit',
        type: 'slider'
      }
    ]
  });

  // Notifications Widget
  new App.classes.NotificationsWidget('#w-notifications', {});
})
