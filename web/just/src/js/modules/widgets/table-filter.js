/**
 * TableFilter Widget.
 * @module widgets/table-filter
 * @param {object} options - Widget init options
 * @author AdminBootstrap.com
 */

App.classes.TableFilter = function(options) {
  var o = $.extend({}, options);

  /**
   * @var {object} Module settings object.
   */
  var s = {
    dataTable:   o.dataTable,
    filters:     o.filters
  };

  /**
   * @var {object} Predefined filter types, that used to ...
   */
  var types = {
    'select': {
      init: function(f, dt) {
        $(f.el)
          .select2({})
          .on('change', function() {
            dt.draw();
          });
      },
      reset: function(f, dt) {
        var values = {},
            rows = dt.rows().data(),
            options = [{ id: -1, text: f.placeholder || 'Select' }];
        for (var i = 0; i < rows.length; i++) {
          values[rows[i][f.column]['@data-filter']] = rows[i][f.column]['@data-sort'];
        }
        for (var val in values) {
          if (values.hasOwnProperty(val)) {
            options.push({ id: val, text: values[val] })
          }
        }
        $(f.el).empty().select2({
          data: options
        })
      },
      compare: function(f, dt, row) {
        var value = $(f.el).val();
        if (value && value != -1) {
          if (row[f.column] == value) {
            return true;
          } else {
            return false;
          }
        }
        return true;
      }
    },
    'slider-range': {
      init: function(f, dt) {
        $(f.el).ionRangeSlider({
          type: 'double',
          grid: false,
          max: 0,
          min: 0,
          from: 0,
          to: 0,
          prefix: 'Price $: ',
          decorate_both: false,
          onFinish: function() {
            dt.draw();
          }
        })
      },
      reset: function(f, dt) {
        var nubmers = dt.column(f.column).cache('search'),
            min = Math.min.apply(null, nubmers),
            max = Math.max.apply(null, nubmers);
        $(f.el).data('ionRangeSlider').update({
          max: max + ((max - min) * 0.5),    // make some space
          min: (min - ((max - min) * 0.5)),  // around actual edges
          from: min,
          to: max
        })
      },
      compare: function(f, dt, row) {
        var slider = $(f.el).data('ionRangeSlider'),
            min = slider.result.from,
            max = slider.result.to,
            value = parseFloat(row[f.column]);
        if ( ( min == undefined && max == undefined ) ||
          ( isNaN(value) ) ||
          ( min == undefined && value <= max ) ||
          ( min <= value && max == undefined ) ||
          ( min <= value && value <= max ) ) {
          return true;
        }
        return false;
      }
    },
    'date-range': {
      init: function(f, dt) {
        $(f.el).daterangepicker({
          "ranges": {
            'Today': [moment(), moment()],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          "alwaysShowCalendars": true,
          "startDate": moment().format('MM/DD/YYYY'),
          "endDate": moment().format('MM/DD/YYYY'),
          "opens": "left",
          "linkedCalendars": false
        },
        function(start, end, label) {
          $(f.el).html(start.format('MMM D YYYY') + ' — ' + end.format('MMM D YYYY'));
          dt.draw();
        });
      },
      reset: function(f, dt) {
        var dates = dt.column(f.column).cache('search'),
            min = moment(parseInt(Math.min.apply(null, dates))),
            max = moment(parseInt(Math.max.apply(null, dates)));
        $(f.el).data('daterangepicker').setStartDate(min.format('MM/DD/YYYY'));
        $(f.el).data('daterangepicker').setEndDate(max.format('MM/DD/YYYY'));
        $(f.el).html(min.format('MMM D YYYY') + ' — ' + max.format('MMM D YYYY'));
      },
      compare: function(f, dt, row) {
        var range = $(f.el).data('daterangepicker'),
            from = range.startDate.clone().valueOf(),
            to = range.endDate.clone().valueOf(),
            date = parseInt(row[f.column]);
        if ( (isNaN(from) && isNaN(to)) ||
          ( isNaN(date) ||
          ( isNaN(from) && date <= to ) ||
          ( from <= date && isNaN(to) ) ||
          ( from <= date && date <= to )) ) {
          return true;
        }
        return false;
      }
    },
    'checkbox': {
      init: function(f, dt) {
        $(f.el).on('change', function() {
          dt.draw();
        })
      },
      reset: function(f, dt) {
        $(f.el).prop('checked', false);
      },
      compare: function(f, dt, row) {
        var active = $(f.el).prop('checked') || false,
            status = row[f.column];
        if (( !active ) || (active && JSON.parse(status))) {
          return true;
        }
        return false;
      }
    },
    'radio': {
      init: function(f, dt) {
        $(f.el).on('change', function() {
          dt.draw();
        })
      },
      reset: function(f, dt) {
        $(f.el).prop('checked', false);
        $(f.el).filter('input[value="-1"]').prop('checked', true).change();
      },
      compare: function(f, dt, row) {
        var checked = $(f.el).filter(':checked').val(),
            status = row[f.column];
        if (( checked == '-1' ) || (checked == status)) {
          return true;
        }
        return false;
      }
    },
    'tags': {
      init: function(f, dt) {
        $(f.el)
          .select2({ placeholder: f.placeholder || $(f.el).data('placeholder') })
          .on('change', function() {
            dt.draw();
          })
      },
      reset: function(f, dt) {
        var values = {},
            rows = dt.rows().data(),
            options = [];
        for (var i = 0; i < rows.length; i++) {
          values[rows[i][f.column]['@data-filter']] = rows[i][f.column]['display'];
        }
        for (var val in values) {
          if (values.hasOwnProperty(val)) {
            options.push({ id: val, text: values[val] })
          }
        }
        $(f.el).empty().select2({
          data: options
        })
      },
      compare: function(f, dt, row) {
        var selected = $(f.el).select2('val'),
            value = row[f.column];

        if (selected.length && selected.indexOf(value) == -1) {
          return false;
        }

  			return true;
      }
    }
  };

  var t = $.extend(types, o.types);

  /**
   * @function Validate initial options
   *
   * @return {boolean} Result
   */
  function validate() {
    try {
      if (!s.dataTable && s.dataTable.constructor.name != '_Api') throw "TableFilter: '" + o.dataTable + "' is not valid DataTables instanse.";
    } catch(e) {
      console.warn(e);
      return false;
    }

    return true;
  }

  function initFilters() {
    for (var filter in s.filters) {
      if (s.filters.hasOwnProperty(filter)) {
        var f = s.filters[filter];

        t[f.type].init(f, s.dataTable);

        $.fn.dataTable.ext.search.push(
          function(f) {
            return function(settings, data, dataIndex) {
              if (settings.nTable.id == s.dataTable.table().node().id) {
                return t[f.type].compare(f, s.dataTable, data);
              }
              return true;
            }
          }(f)
        )
      }
    }
  }

  s.reset = function() {
    for (var filter in s.filters) {
      if (s.filters.hasOwnProperty(filter)) {
        var f = s.filters[filter];
        t[f.type].reset(f, s.dataTable);
      }
    }
    s.dataTable.search('');
    s.dataTable.draw();
  }

  function init() {
    if (validate()) {
      initFilters();
      s.reset();
    }
  };

  init();

  return s;
};
