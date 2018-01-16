$(document).ready(function() {
  $('.side-cont').scrollbar({ disableBodyScroll: true, duration: 150 })

  $('pre').each(function() {
    hljs.highlightBlock($(this)[0]);
  })

  $('body:not(.side-open) .side').on('click', function() {
    $('body').addClass('side-open');

    $(document).on('click', handleSidebarClick);
  })

  function handleSidebarClick(e) {
    if (!$.contains($('.side')[0], e.target)) {
      $('body').removeClass('side-open');
      $(document).off('click', handleSidebarClick);
    }
  }
})
