/**
 * Product Form page module.
 * @module pages/product-form
 * @author AdminBootstrap.com
 */

App.page.productForm = function() {

  /**
   * @const {object} Constant css-selectors to link up with HTML markup.
   */
  var c = {
    INFO: {
      CATEGORY: '#product-category',
      TAGS:     '#product-tags'
    },
    FIELDS: {
      TMPL:   '#product-field-template',
      ADD:    '.add-field',
      REMOVE: '.remove-field'
    },
    COVER: {
      IMG:     '.st-cropper-img',
      PREVIEW: '.st-cropper__preview',
      FILE:    '#cFile',
      DATA: {
        X:      '#cDataX',
        Y:      '#cDataY',
        HEIGHT: '#cDataHeight',
        WIDTH:  '#cDataWidth',
        SCALEX: '#cDataScaleX',
        SCALEY: '#cDataScaleY',
        ROTATE: '#cDataRotate',
        RATIO:  'input[name="cDataRatio"]',
        BOX: {
          LEFT:   '#cDataBoxLeft',
          TOP:    '#cDataBoxTop',
          WIDTH:  '#cDataBoxWidth',
          HEIGHT: '#cDataBoxHeight'
        },
        REMOVED: '#cRemoved'
      },
      BUTTONS: {
        REMOVE:   '#cRemove',
        PREVIEW:  '#cPreview',
        RESTORE:  '#cRestore',
        DOWNLOAD: '#cDownload'
      }
    },
    IMAGES: {
      TMPL:  '#table-images__template',
      FILES: '#product-images-files',
      TABLES: {
        STORED: '#product-images-stored',
        ADDED:  '#product-images-added'
      },
      IMAGE: {
        VIEW:    '.table-images__view',
        PREVIEW: '.table-images__preview',
        NAME:    '.table-images__name',
        SIZE:    '.table-images__size',
        RES:     '.table-images__res',
        ORDER:   '.table-images__order',
        COPY:    '.table-images__copy',
        DELETE:  '.table-images__delete'
      },
      BUTTONS: {
        ADD:          '#product-images-add',
        REMOVE_ADDED: '#product-images-remove'
      }
    },
    RELATED: '#product-related'
  }

  /**
   * @var {object} Module settings object.
   */
  var s = {
    widgets: {}
  };

  // Fancybox Plugin options
  var fancybox = {
		prevEffect	: 'none',
		nextEffect	: 'none',
    type: 'image',
    padding : 0
  }

  function initWidgets() {
    // Cropper Widget for Product Cover
    s.widgets.cropper = new App.classes.CropperWidget({
      img: c.COVER.IMG,
      file: c.COVER.FILE,
      preview: c.COVER.PREVIEW,
      data: {
        x: c.COVER.DATA.X,
        y: c.COVER.DATA.Y,
        width: c.COVER.DATA.WIDTH,
        height: c.COVER.DATA.HEIGHT,
        scaleX: c.COVER.DATA.SCALEX,
        scaleY: c.COVER.DATA.SCALEY,
        rotate: c.COVER.DATA.ROTATE,
        ratio: c.COVER.DATA.RATIO,
        box: {
          left: c.COVER.DATA.BOX.LEFT,
          top: c.COVER.DATA.BOX.TOP,
          width: c.COVER.DATA.BOX.WIDTH,
          height: c.COVER.DATA.BOX.HEIGHT,
        },
        removed: c.COVER.DATA.REMOVED
      },
      buttons: {
        remove: c.COVER.BUTTONS.REMOVE,
        restore: c.COVER.BUTTONS.RESTORE,
        preview: c.COVER.BUTTONS.PREVIEW,
        download: c.COVER.BUTTONS.DOWNLOAD
      }
    });
  }

  function initPlugins() {
    $(c.INFO.CATEGORY).select2({});
    $(c.INFO.TAGS).select2({});

    // Fancybox
    $(".fancybox").fancybox(fancybox);

    // Duallistbox (Related Products)
    $(c.RELATED).bootstrapDualListbox({
      nonSelectedListLabel: 'Products List',
      selectedListLabel: 'Related Products',
    });

    // Update CropperWidget on Tab toggle
    $('.st-panel__header a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      if ($(e.target).attr('href') == '#cover') {
        s.widgets.cropper.update();
      }
    })
  }

  function bindUIActions() {
    // Add new field
    $(document).on('click', c.FIELDS.ADD, function() {
      var $group = $(this).closest('.form-group'),
          $field = $(c.FIELDS.TMPL).clone()
                     .removeClass('hide').attr('id', '').insertBefore($group);

      $field.find('select').select2({});
    })
    // Remove existed field
    $(document).on('click', c.FIELDS.REMOVE, function() {
      $(this).closest('.form-group').remove();
    })

    // Add Images
    $(c.IMAGES.FILES).on('change', function() {
      var files = this.files;

      if (files.length) {
        for (var i = 0; i < files.length; i++) {
          var file = files[i];

          if (!/^image\//.test(file.type)) {
            continue;
          }

          $(c.IMAGES.TABLES.ADDED).parent().removeClass('hide');

          var $i = $(c.IMAGES.TMPL).clone().attr('id', '').removeClass('hide'),
              data = '',
              reader = new FileReader(),
              image = new Image();

          image.onload = (function($i) {
            return function() {
              $i.find(c.IMAGES.IMAGE.RES).html(this.width + 'x' + this.height);
            }
          })($i)

          reader.onload = (function($i, image) {
            return function(e) {
              var data = e.target.result;
              $i.find(c.IMAGES.IMAGE.PREVIEW)
                .attr('href', data).css('backgroundImage', 'url('+ data +')')
                .fancybox(fancybox);
              $i.find(c.IMAGES.IMAGE.VIEW)
                .attr('href', data)
                .fancybox(fancybox);
              image.src = data;
            }
          })($i, image)

          reader.readAsDataURL(file);

          $i.find(c.IMAGES.IMAGE.NAME).val(file.name);
          $i.find(c.IMAGES.IMAGE.SIZE).html((file.size / 1024 / 1024).toFixed(2) + ' MB');

          $(c.IMAGES.TABLES.ADDED).find('tbody').append($i);
        }

        $(c.IMAGES.BUTTONS.ADD).addClass('hide');
        $(c.IMAGES.BUTTONS.REMOVE_ADDED).removeClass('hide');
      }
    })

    // Remove added images
    $(c.IMAGES.BUTTONS.REMOVE_ADDED).on('click', function() {
      $(c.IMAGES.TABLES.ADDED).find('tbody tr:not(' + c.IMAGES.TMPL + ')').remove();
      $(c.IMAGES.FILES).val(null);

      $(c.IMAGES.BUTTONS.ADD).removeClass('hide');
      $(c.IMAGES.TABLES.ADDED).parent().addClass('hide');
      $(this).addClass('hide');
    })

    // Delete stored image
    $(c.IMAGES.IMAGE.DELETE).on('click', function() {
      $(this).closest('tr').remove();
    })
  }

  function init() {
    initPlugins();
    initWidgets();

    bindUIActions();
  }

  init();

  return s;

}();
