$(function() {

  $( "#polldialog" ).dialog({
    autoOpen: false,
    width: 450
  });

  $('#polllink').click(function(){
    $('#polldialog').dialog('open');
    return false;
  });
});

$(document).ready(function(){
    $('#topmenu li.sublnk').hover(
    function() {
      $(this).addClass("selected");
      $(this).find('ul').stop(true, true);
      $(this).find('ul').show('fast');
    },
    function() {
      $(this).find('ul').hide('fast');
      $(this).removeClass("selected");
    }
  );
});













    $(document).ready(function() {
      
      $('.carousel').elegantcarousel({
          delay:50,
          fade:300,
          slide:500,
          effect:'fade',            
          orientation:'horizontal',
          loop: false,
          autoplay: false,
          time: 5000      });
      
      $('.open_config').click(function() {
                       
        var display = $('.config_inner').css('display');
        if(display == 'none') { $('.config_inner').fadeIn(200); }                 
        if(display == 'block') { $('.config_inner').fadeOut(200); }  
        return(false);
       });

      
      function center_main() {
        var window_height = $(window).height();
        var main_height = parseInt($('#main').css('height'));
        var main_height_margin = (window_height - main_height) / 2;
        $('#main').css('top',Math.floor(main_height_margin));
      }
      center_main();
  
    });




(function($) {
$(function() {
  $('ul.tabs').delegate('li:not(.current)', 'click', function() {
    $(this).addClass('current').siblings().removeClass('current')
      .parents('div.section').find('div.box').hide().eq($(this).index()).fadeIn(150);
  })
})
})(jQuery);


$(function () {
	$(".lcomment:odd").addClass("even");
	$(".lcomment").hover(function(){ $(this).addClass("hover");},function(){$(this).removeClass("hover");});
	$('.lcomment').click(function(){window.location=$(this).find("a").attr("href"); return false;});
	});