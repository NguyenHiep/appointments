$(function() {
  indexPage();
  showHideTitle();

  $(".burger-menu").click(function () {
    $(this).toggleClass("menu-on");
    $('.menu-container').toggleClass('active');
  });
});
function indexPage() {
  var indexTitle = $('.index-text');

  indexTitle.on('mouseover', function(e) {
    $('.background').removeClass('active');
    var target = '.'+e.currentTarget.dataset.background;
    $(target).addClass('active');
  });
}

function showHideTitle() {
  var pageTitle = $('.page-title');
  pageTitle.on('click', function(){
    hideTitleLayer(pageTitle);
  });
  $(window).scroll(function() {
    var top = $(window).scrollTop();
    if (top == 0) {
      pageTitle.addClass('active');
      pageTitle.css('visibility', 'visible');
    } else {
      hideTitleLayer(pageTitle);
    }
  });
}
function hideTitleLayer(pageTitle) {
  if(pageTitle.hasClass('active')) {
    pageTitle.removeClass('active');
    pageTitle.delay(300)
        .queue(function (next) {
          $(this).css('visibility', 'hidden');
          next();
        });
  };
}