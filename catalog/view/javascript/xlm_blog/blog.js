$(document).ready(function() {

  $('#blog-list-view').click(function() {
    $('#content .blog-grid > .clearfix').remove();

    $('#content .row > .blog-grid').attr('class', 'blog-layout blog-list col-xs-12');
    $('#blog-grid-view').removeClass('active');
    $('#blog-list-view').addClass('active');
    localStorage.setItem('blog_display', 'list');
  });

  // Product Grid
  $('#blog-grid-view').click(function() {
    // What a shame bootstrap does not take into account dynamically loaded columns
    var cols = $('#column-right, #column-left').length;

    if (cols == 2) {
      $('#content .blog-list').attr('class', 'blog-layout blog-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
    } else if (cols == 1) {
      $('#content .blog-list').attr('class', 'blog-layout blog-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
    } else {
      $('#content .blog-list').attr('class', 'blog-layout blog-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
    }


    $('#blog-list-view').removeClass('active');
    $('#blog-grid-view').addClass('active');

    localStorage.setItem('blog_display', 'grid');
  });

  if (localStorage.getItem('blog_display') == 'list') {
    $('#blog-list-view').trigger('click');
    $('#blog-list-view').addClass('active');
  } else {
    $('#blog-grid-view').trigger('click');
    $('#blog-grid-view').addClass('active'); 
  }
});
