/**
 * Auth pages module.
 * @module pages/auth
 * @author AdminBootstrap.com
 */

App.page.auth = function() {

  /**
   * @const {object} Constant css-selectors to link up with HTML markup.
   */
  var c = {
    LOGIN: {
      FORM: '#login-form',
      CONTROLS: {
        EMAIL:    '#login-email',
        PASSWORD: '#login-password',
        REMEMBER: '#login-remember'
      }
    },
    REGISTER: {
      FORM: '#register-form',
      CONTROLS: {
        EMAIL:    '#register-email',
        PASSWORD: '#register-password',
        TERMS:    '#register-terms input[type="checkbox"]'
      },
      TERMS: '#register-terms'
    },
    PASSWORD: {
      FORM:   '#password-form',
      SUBMIT: '#password-submit',
      MSG:    '#password-msg'
    }
  }

  /**
   * @var {object} Module settings object.
   */
  var s = {
    widgets: {}
  };

  function initPlugins() {
    // Init bootstrap-validator plugin
    $(c.LOGIN.FORM + ', ' + c.REGISTER.FORM + ', ' + c.PASSWORD.FORM).validator({
      disable: false
    });

    // Terms popover
    $(c.REGISTER.TERMS + ' a').popover({
      html: true,
      trigger: 'focus',
      placement: 'bottom',
      title: 'Terms and Conditions',
      content: '<p style="margin-bottom: 5px;">Please, make sure you have read the <a class="text-nowrap" href="index.html" target="_blank">Terms and Conditions</a> of  our service.</p>'
    })
  }

  function bindUIActions() {
    $(c.LOGIN.FORM).on('submit', function(e) {
      // The 'isDefaultPrevented' function, in fact,
      // returns the inverted result of validation.
      return !e.isDefaultPrevented();
    })

    $(c.REGISTER.TERMS + ' input').on('focus', function() {
      $(c.REGISTER.TERMS + ' label').popover('show');
    })

    $(c.REGISTER.TERMS + ' input').on('blur', function() {
      $(c.REGISTER.TERMS + ' label').popover('hide');
    })

    // Restore Password handling
    $(c.PASSWORD.FORM).on('submit', function(e) {
      $(c.PASSWORD.MSG).addClass('hide');

      // if validation passed
      if (!e.isDefaultPrevented()) {
        $(c.PASSWORD.SUBMIT).button('sending').addClass('disabled');

        setTimeout(function() {
          $(c.PASSWORD.SUBMIT).button('reset').removeClass('disabled');
          // show message
          $(c.PASSWORD.MSG).removeClass('hide');
        }, 1500);
      }

      return false;
    })
  }


  function init() {
    initPlugins();
    bindUIActions();
  }

  init();

  return s;

}();
