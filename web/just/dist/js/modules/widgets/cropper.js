/**
 * Cropper.js Widget.
 * @module widgets/cropper
 * @param {object} options - Widget init options
 * @author AdminBootstrap.com
 */

App.classes.CropperWidget = function(options) {
  var o = options || {};

  /**
   * @const {object} Constant css-selectors to link up with HTML markup.
   */
  var c = {};

  /**
   * @var {object} Module settings object.
   */
  var s = {
    $cropper: $(o.img),
    $file: $(o.file),
    $preview: $(o.preview),
    placeholder: '',
    data: {
      $x: $(o.data.x),
      $y: $(o.data.y),
      $width: $(o.data.width),
      $height: $(o.data.height),
      $scaleX: $(o.data.scaleX),
      $scaleY: $(o.data.scaleY),
      $rotate: $(o.data.rotate),
      $ratio: $(o.data.ratio),
      box: {
        $left: $(o.data.box.left),
        $top: $(o.data.box.top),
        $width: $(o.data.box.width),
        $height: $(o.data.box.height),
      },
      $removed: $(o.data.removed)
    },
    buttons: {
      $remove: $(o.buttons.remove),
      $restore: $(o.buttons.restore),
      $preview: $(o.buttons.preview),
      $download: $(o.buttons.download)
    },
    // Cropper Data object to Restore
    // It is filled from HTML on 'built' event
    store: null
  };

  // Get Cropper Data from HTML
  s.getCropData = function() {
    return {
      x: s.data.$x.val() ? parseInt(s.data.$x.val()) : null,
      y: s.data.$y.val() ? parseInt(s.data.$y.val()) : null,
      height: s.data.$height.val() ? parseInt(s.data.$height.val()) : null,
      width: s.data.$width.val() ? parseInt(s.data.$width.val()) : null,
      rotate: s.data.$rotate.val() ? parseInt(s.data.$rotate.val()) : null,
      scaleX: s.data.$scaleX.val() ? parseInt(s.data.$scaleX.val()) : null,
      scaleY: s.data.$scaleY.val() ? parseInt(s.data.$scaleY.val()) : null
    }
  }

  // Get CropBox Data from HTML
  s.getBoxData = function() {
    return {
      left: s.data.BoxLeft.val() ? parseInt(s.data.BoxLeft.val()) : null,
      top: s.data.BoxTop.val() ? parseInt(s.data.BoxTop.val()) : null,
      width: s.data.BoxWidth.val() ? parseInt(s.data.BoxWidth.val()) : null,
      height: s.data.BoxHeight.val() ? parseInt(s.data.BoxHeight.val()) : null,
    }
  }

  // Cropper Init Options
  s.options = {
    aspectRatio: s.data.$ratio.val(),
    viewMode: 2,
    preview: s.$preview,
    crop: function(e) {
      var b = s.$cropper.cropper('getCropBoxData');
      s.data.$x.val(Math.round(e.x));
      s.data.$y.val(Math.round(e.y));
      s.data.$height.val(Math.round(e.height));
      s.data.$width.val(Math.round(e.width));
      s.data.$rotate.val(e.rotate);
      s.data.$scaleX.val(e.scaleX);
      s.data.$scaleY.val(e.scaleY);

      s.data.box.$left.val(Math.round(b.left));
      s.data.box.$top.val(Math.round(b.top));
      s.data.box.$width.val(Math.round(b.width));
      s.data.box.$height.val(Math.round(b.height));
    },
    built: function() {
      var cropData = this.getCropData(),
          boxData = this.getBoxData();

      // if have data in HTML, set it and store
      if (boxData.width && boxData.height) {
        s.$cropper.cropper('setData', cropData);
        s.$cropper.cropper('setCropBoxData', boxData);

        if (!s.store) {
          s.store = {
            crop: cropData,
            cropBox: boxData
          }
          s.buttons.$restore.removeClass('disabled');
        }
      }
    }
  }

  // Update Cropper Widget (Reinit)
  s.update = function() {
    s.$cropper.cropper('destroy').cropper(s.options);
  }

  /**
   * @function Validate initial options
   *
   * @return {boolean} Result
   */
  function validate() {

    try {
      if (!o.img.length && !$(o.img).is('img')) throw "CropperWidget: '" + o.img + "' is not valid image.";
    } catch(e) {
      console.warn(e);
      return false;
    }

    return true;
  }

  function bindUIActions() {
    // Restore Cropper to Initial state
    s.buttons.$restore.on('click', function() {
      if (s.store) {
        s.$cropper.cropper('setData', s.store.crop);
        s.$cropper.cropper('setCropBoxData', s.store.cropBox);
      }
    });

    // Change Aspect Ratio
    s.data.$ratio.on('change', function() {
      s.$cropper.cropper('setAspectRatio', $(this).val());
    });

    // Upload New Image
    s.$file.on('change', function() {
      var fileReader = new FileReader(),
          files = this.files,
          file;

      if (!files.length) {
          return;
      }

      file = files[0];

      if (/^image\/\w+$/.test(file.type)) {
          fileReader.readAsDataURL(file);
          fileReader.onload = function () {
              s.data.$removed.val('0');
              s.$cropper.attr('src', this.result);
              s.update();
          };
      }
    });

    //Preview Cropper Image
    s.buttons.$preview.on('click', function() {
      s.buttons.$download.attr('href', s.$cropper.cropper('getCroppedCanvas').toDataURL());
      showImage(s.buttons.$download);
      return false;
    })

    //Download Cropper Image
    s.buttons.$download.on('click', function() {
      s.buttons.$download.attr('href', s.$cropper.cropper('getCroppedCanvas').toDataURL());
    })

    // Remove Cropper Image
    s.buttons.$remove.on('click', function() {
      s.$file.val('');
      s.data.$removed.val('1');
      s.$cropper.attr('src', s.placeholder);
      s.update();
    });
  }

  function showImage(a) {
    var data = a.attr('href');
    $.fancybox.open({ href: data });
  }

  function init() {
    if (validate()) {
      bindUIActions();
      s.update();
    }
  };

  init();

  return s;
};
