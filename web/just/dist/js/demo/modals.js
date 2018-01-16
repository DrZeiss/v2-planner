$(document).ready(function() {

  // Custom Alert
  $('.bootbox-alert').on('click', function() {
    bootbox.alert({
      title: 'Alert',
      message: "This is an alert with a callback.",
      backdrop: true,
      size: 'small',
      buttons: {
        ok: {
          label: 'OK',
          className: 'btn-info'
        }
      }
    });
  })

  // Custom Confirm
  $('.bootbox-confirm').on('click', function() {
    bootbox.confirm({
      title: "Delete Item?",
      message: "Do you really want to delete this Item?",
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
      callback: function (result) {}
    });
  })

  // Custom Prompt
  $('.bootbox-prompt').on('click', function() {
    bootbox.prompt({
      title: "Input Your Name",
      inputType: 'text',
      placeholder: 'Name',
      backdrop: true,
      size: 'small',
      buttons: {
        cancel: {
          label: 'Cancel'
        },
        confirm: {
          label: 'Enter',
          className: 'btn-info'
        }
      },
      callback: function (result) {}
    });
  })

})
