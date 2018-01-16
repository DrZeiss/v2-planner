/**
 * Punels Plugin.
 * @param {string} element - Css-selector of Panel container
 * @param {object} options - Panel init options
 * @author AdminBootstrap.com
 */


(function ($) {
  'use strict';

  // PANEL CLASS DEFINITION
  // ======================

  var Panel = function (element, options) {
    this.options             = options
    this.$body               = $(document.body)
    this.$element            = $(element)
    this.isCollapsed         = $(element).is('.st-panel--collapsed')
    this.isShown             = $(element).is(':visible')
    this.isMaximized         = $(element).is('.st-panel--max')
  }

  Panel.DEFAULTS = {}

  Panel.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide(_relatedTarget) : this.show(_relatedTarget)
  }

  Panel.prototype.hide = function (_relatedTarget) {
    this.$element.hide();
    this.isShown = false;
  }

  Panel.prototype.show = function (_relatedTarget) {
    this.$element.show();
    this.isShown = true;
  }

  Panel.prototype.collapse = function(_relatedTarget) {
    var $collapsible = this.$element.find('.st-panel__content'),
        self = this;
    if (!this.isCollapsed) {
      $collapsible.css('display', 'block');
    }
    $collapsible.slideToggle(150, function() {
      self.$element.toggleClass('st-panel--collapsed');
    });
    this.isCollapsed = $collapsible.is(':visible')
  }

  Panel.prototype.resize = function(_relatedTarget) {
    return this.isMaximized ? this.minimize(_relatedTarget) : this.maximize(_relatedTarget);
  }

  Panel.prototype.maximize = function(_relatedTarget) {
    if (!this.isMaximized) {
      this.$element.addClass('st-panel--max')
      this.isMaximized = true
      this.$body.addClass('disable-scroll')
    }
  }

  Panel.prototype.minimize = function(_relatedTarget) {
    if (this.isMaximized) {
      this.$element.removeClass('st-panel--max')
      this.isMaximized = false
      this.$body.removeClass('disable-scroll')
    }
  }

  // PANEL PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('ab.panel')
      var options = $.extend({}, Panel.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('ab.panel', (data = new Panel(this, options)))
      if (typeof option == 'string') {
        data[option](_relatedTarget)
      } else if (typeof option == 'object' && option.tool != undefined) {
        data[option.tool](_relatedTarget)
      }
    })
  }

  var old = $.fn.panel

  $.fn.panel             = Plugin
  $.fn.panel.Constructor = Panel


  // PANEL NO CONFLICT
  // =================

  $.fn.panel.noConflict = function () {
    $.fn.panel = old
    return this
  }


  // PANEL TOOLS DATA-API
  // ==============

  $(document).on('click', '.st-panel__tools, .st-panel__tabs, .st-panel__form', function(e) {
    e.stopPropagation()
  })

  // Tool click event binding
  $(document).on('click.ab.panel.tools', '[data-tool]:not([data-tool=""])', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-tool-target') || (href && href.replace(/.*(?=#[^\s]+$)/, '')))
    var option  = $target.data('ab.panel') ? $this.data('tool') : $this.data()

    if (!$target.length) $target = $this.closest('.st-panel')

    Plugin.call($target, option, this)
  })

})(jQuery);
