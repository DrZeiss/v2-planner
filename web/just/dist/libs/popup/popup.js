/* ========================================================================
 * AdminBootstrap: ab-popup.js v1.0.0
 * https://github.com/adminbootstrap/ab-popup
 * ========================================================================
 * Copyright 2017 adminbootstrap.com
 * Licensed under MIT (https://github.com/adminbootstrap/ab-popup/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // POPUP CLASS DEFINITION
  // =========================

  var toggle   = '[data-toggle="popup"]'
  var Popup = function (trigger, options) {
    this.$trigger      = trigger
    this.options       = $.extend({}, Popup.DEFAULTS, options)
    this.$element      = getTargetFromTrigger(this.$trigger)
    this.$container    = $(this.$trigger.data('container') || 'body')
  }

  Popup.VERSION = '1.0.0'

  Popup.DEFAULTS = {
    blur: true,
    method: 'toggle',
    topOffset: 15
  }

  function getTargetFromTrigger($trigger) {
    var href
    var target = $trigger.attr('data-target')
      || (href = $trigger.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') // strip for ie7

    if (target && $(target).length) {
      return $(target);
    } else {
      throw new Error('popup.js: Target popup not found.')
    }
  }

  function setPopupPostion() {
    this.$element
      .css({
        left: this.$trigger.offset().left + this.$trigger.outerWidth() - this.$element.outerWidth(),
        right: 'auto',
        top: this.$trigger.offset().top + this.$trigger.outerHeight() + Popup.DEFAULTS.topOffset
      })
  }


  Popup.prototype.isShown = function () {
    return this.$element.hasClass('open');
  }

  Popup.prototype.toggle = function () {
    this.isShown() ? this.hide() : this.show();
  }

  Popup.prototype.show = function () {
    if (this.$trigger.is('.disabled, :disabled')) return

    var event = { relatedTarget: this.$trigger }
    this.$element.trigger($.Event('show.bs.popup', event))

    this.$trigger.trigger('focus')

    this.$element
      .detach()
      .appendTo(this.$container)
      .css({ display: 'block' })
      .trigger($.Event('shown.bs.popup', event))

    setPopupPostion.apply(this);

    if (this.options.blur) {
      $(document).on('click', $.proxy(Popup.prototype.blur, this))
    }

    $(window).on('resize', $.proxy(setPopupPostion, this))
  }

  Popup.prototype.hide = function () {
    if (this.$trigger.is('.disabled, :disabled')) return

    var event = { relatedTarget: this.$trigger }
    this.$element.trigger($.Event('hide.bs.popup', event))

    this.$element
      .css({ display: 'none' })
      .trigger($.Event('hidden.bs.popup', event))

    if (this.options.blur) {
      $(document).off('click', $.proxy(Popup.prototype.blur, this))
    }

    $(window).off('resize', $.proxy(setPopupPostion, this))
  }

  Popup.prototype.blur = function(e) {
    if (!$.contains(this.$element[0], e.target)) {
      this.hide();
    }
  }


  // POPUP PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $trigger   = $(this)
      var data    = $trigger.data('bs.popup')
      var options = $.extend({}, Popup.DEFAULTS, $trigger.data(), typeof option == 'object' && option)

      if (!data) $trigger.data('bs.popup', (data = new Popup($trigger, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.popup

  $.fn.popup             = Plugin
  $.fn.popup.Constructor = Popup


  // POPUP NO CONFLICT
  // ====================

  $.fn.popup.noConflict = function () {
    $.fn.popup = old
    return this
  }


  // APPLY TO TOGGLE ELEMENTS
  // ===================================

  $(document)
    .on('click.bs.popup.data-api', toggle, function(e) {
      var $trigger   = $(this)

      if (!$trigger.attr('data-target')) e.preventDefault()

      var option  = $trigger.data('method') || Popup.DEFAULTS.method

      Plugin.call($trigger, option)
    })

}(jQuery);
