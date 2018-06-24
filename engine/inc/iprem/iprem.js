//
// Модуль    iPrem
// =======================================================
// Версия:   2.0.3.12
// =======================================================
// Файл:     iprem.js
// =======================================================
// Автор:    Sistemos
// Telegram: @Sistemos
// E-mail:   sistemos-art@yandex.ru
// Skype:    Sistemos
// =======================================================
//

$(document).ready(function(){

	// получения списка по чекбоксам
	$("#getids").click(function(){
		$("#kpids").html("");
		$("#kpids").prop("rows",parseInt($(".kp-filmid:checked").length)+1);
		$(".kp-filmid:checked").each(function(key,item){
		   $("#kpids").append($(item).prop("value") + "\r\n");
		});
	});

	// снятие\установка галки на все чекбоксы
	$("#check-all").click(function(){
		$checkin = $(this).is( ":checked" );
		if ($checkin) {
			$("input[type='checkbox']").prop("checked", true);
		} else {
			$("input[type='checkbox']").prop("checked", false);
		}
	});
	
	// проверка наличия в moonwalk и hdgo
   $("#mw-btn").click( function(){ 
   	$("#load-gif").css( "display", "inline");
   	$(".fa-refresh").css( "display", "none");
		var kpids = [];

		// проверка только id с отмеченными чекбоксами
		$(".kp-filmid:checked").each(function(index, val){
		   kpids[index] = $(this).attr("data-check-id");		   
		});

		if ( parseInt(kpids.length) < 1) {			
			$("#btn-text").text( "Ничего не отмечено!" );
			setTimeout(function() {
			  $(location).attr("href", document.location);
			}, 1000);
		} else {
			$("#btn-text").text( " Ждем " + kpids.length + " сек." );
			console.log(parseInt(kpids.length));
			$.ajax({
			   type: "POST",
			   url: "engine/ajax/iprem_ajax.php",
			   data: "ids=" + kpids,
			   success: function(msg){
			     $("#btn-text").text( msg );
			     $("#load-gif").css( "display", "none");
			     $(".icon-refresh").css( "display", "inline");
			     $(location).attr("href", document.location);
			   }
			 });				
		}
   }); 
   
	$('#mw-btn').hover(
	function(){
	  $(this).css( "color", "#000");
	  $("#date-up").css( "display", "inline");
	},
	function(){
	  $(this).css( "color", "#636363");
	  $("#date-up").css( "display", "none");
	});
	
	
	// добавление в избранное
   $(".bookmarks").click( function(){  
		$(this).each(function(key,item){
				var id_film = $(this).attr("for"); 
				var favor_n = parseInt( $("#favor-n").text() ); 
				$.ajax({
			   type: "POST",
			   url: "engine/ajax/iprem_ajax.php",
			   data: "idbm=" + id_film,
			   success: function(msg){
			   	$("[for=" + msg + "]").css("display", "none");
					$("#favor-n").text(favor_n + 1);
			   }
			});            
		});        	  	  
   }); 

	// удаление из избранного
   $(".bookmarks-del").click( function(){  
		$(this).each(function(key,item){
			var id_film = $(this).attr("for"); 
			var favor_n = parseInt( $("#favor-n").text() );
			$.ajax({
			   type: "POST",
			   url: "engine/ajax/iprem_ajax.php",
			   data: "idbm=" + id_film +"&del=1",
			   success: function(msg){
			   	$("[for=" + msg + "]").parents("tr").remove();
					$("#favor-n").text(favor_n - 1);
			   }
			});            
		});        	  	  
   });

	// поиск фильма по id
	$("#poisk_btn").click(function(){		
		$("#load-gif-search").show();
		var idkp = $("#poisk_input").val();
		$.ajax({
		   type: "POST",
		   url: "engine/ajax/iprem_ajax.php",
		   data: "kp_query=" + idkp,
		   dataType: "html",
		   success: function(msg){
		   	$("#poisk_result").html(msg);
		   	$("#load-gif-search").hide();
		   	$("#poisk_result_title").slideDown(400);
		   }
		});				
	}); 

	// закрытие результата поиска
	$("#poisk-result-close").click(function(){
		$("#poisk_result_title").slideUp(400);
	});

	// добавление в избранное из результатов поиска
	$('body').on('click', '.btn-favorites', function(e) {
		var favor_n = parseInt( $("#favor-n").text() );
		var idkp = $(this).attr("data-id");
		var title = $(this).attr("data-title");
		var year = $(this).attr("data-year");
		var rating = $(this).attr("data-rating");		
		console.log(idkp + title + year + rating);          
 		$.ajax({
		   type: "POST",
		   //async: false,
		   url: "engine/ajax/iprem_ajax.php",
		   data: "id_search=" + idkp + "&title_s=" + title + "&year_s=" + year + "&rating_s=" + rating,
		   dataType: "html",
		   success: function(msg){
		   	$("#td-film-"+ idkp ).html(msg);
		   	$("#favor-n").text(favor_n + 1);
		   }
		});
	}); 



});