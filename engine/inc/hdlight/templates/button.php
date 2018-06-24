<?php

// Обрабатываем html id полей в зависимости от версии скрипта

// Title
if ($this->config['title']) {
	$_field = explode('|', $this->config['title']);
	if ($this->dle_config['version_id'] < 10.2 and $_field[2] and $_field[2] == 'text') {
		$field_title = "xfield\\\\[{$_field[0]}\\\\]";
	} elseif ($_field[1] and $_field[1] == 'xfield') {
		$field_title = "xf_{$_field[0]}";
	} else {
		$field_title = $_field[0];
	}
}

// Kinopoisk
if ($this->config['kinopoisk_id']) {
	$_field = explode('|', $this->config['kinopoisk_id']);
	if ($this->dle_config['version_id'] < 10.2 and $_field[2] and $_field[2] == 'text') {
		$field_kinopoisk = "xfield\\\\[{$_field[0]}\\\\]";
	} elseif ($_field[1] and $_field[1] == 'xfield') {
		$field_kinopoisk = "xf_{$_field[0]}";
	} else {
		$field_kinopoisk = $_field[0];
	}
}

// Worldart
if ($this->config['world_art_id']) {
	$_field = explode('|', $this->config['world_art_id']);
	if ($this->dle_config['version_id'] < 10.2 and $_field[2] and $_field[2] == 'text') {
		$field_worldart = "xfield\\\\[{$_field[0]}\\\\]";
	} elseif ($_field[1] and $_field[1] == 'xfield') {
		$field_worldart = "xf_{$_field[0]}";
	} else {
		$field_worldart = $_field[0];
	}
}

// Worldart
if ($this->config['pornolab_id']) {
	$_field = explode('|', $this->config['pornolab_id']);
	if ($this->dle_config['version_id'] < 10.2 and $_field[2] and $_field[2] == 'text') {
		$field_worldart = "xfield\\\\[{$_field[0]}\\\\]";
	} elseif ($_field[1] and $_field[1] == 'xfield') {
		$field_worldart = "xf_{$_field[0]}";
	} else {
		$field_worldart = $_field[0];
	}
}

// Output
if ($this->config['output']) {
	$_field = explode('|', $this->config['output']);
	if ($this->dle_config['version_id'] < 10.2 and $_field[2] and $_field[2] == 'text') {
		$field_output = "xfield\\\\[{$_field[0]}\\\\]";
	} elseif ($_field[1] and $_field[1] == 'xfield') {
		$field_output = "xf_{$_field[0]}";
	} else {
		$field_output = $_field[0];
	}
}

?>

<!-- HD Light CSS -->
<link type="text/css" href="/engine/skins/hdlight/css/button.css" rel="stylesheet" />

<div class="hdlight">
	<button type="button" id="hdlightFindButton" class="hdlight-btn hdlight-btn-sm hdlight-btn-primary" style="float: left;">Поиск на <b>HD Light</b></button>
	<button type="button" id="hdlightClearFindButton" class="hdlight-btn hdlight-btn-sm hdlight-btn-danger" style="float: left; display: none; margin-left: 4px">Очистить поиск</button>
	<button type="button" id="hdlightClearOutputButton" class="hdlight-btn hdlight-btn-sm hdlight-btn-warning" style="float: left; display: none; margin-left: 4px">Стереть вставленную ссылку</button>
	<div style="padding: 6px 0 0 7px; float: left;">
		<label>
			<input type="checkbox" id="hdlightFindInPornoDB" class="hdlight-replace-field" value="1"> Искать в pornodb
		</label>
	</div>
	<div style="clear: both"></div>
	<div id="hdlightFindResults"></div>
</div>

<!-- HDLight Modal -->
<div class="hdlight-modal fade" id="hdlightFindUrlModal" tabindex="-1" role="dialog" aria-labelledby="hdlightFindUrlModalLabel" aria-hidden="true">
	<div class="hdlight-modal-dialog">
		<div class="hdlight-modal-content">
			<div class="hdlight-modal-header">
				<button type="button" class="hdlight-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<h4 class="hdlight-modal-title" id="hdlightFindUrlModalLabel">Скопируйте ссылку</h4>
			</div>
			<div class="hdlight-modal-body" style="text-align: left;">
				<p style="margin-bottom: 5px;"><b>Сделать не доступным по ГЕО:</b></p>
				<label class="checkbox" style="margin-left: 10px;"><input type="checkbox" class="hdlight-geo" name="ru" value="1"> для <b>РФ</b></label>
				<label class="checkbox" style="margin-left: 10px;"><input type="checkbox" class="hdlight-geo" name="ua" value="1"> для <b>Украины</b></label>
			</div>
			<div class="hdlight-modal-footer" style="text-align: center;">
				<input type="text" class="form-control hdlight-find-url" style="width: 560px;" />
			</div>
			<div class="hdlight-modal-footer">
				<button type="button" class="hdlight-btn hdlight-btn-sm hdlight-btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<!-- Подключаем jQuery для старых версий скрипта -->
<script type="text/javascript">
<!--
	if (!window.jQuery)
		document.write(unescape("<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.11.1.min.js\">%3C/script%3E"));
-->
</script>

<!-- HD Light JavaScript -->
<script type="text/javascript">
<!--
	function hdlight_show_loading(message) {
		if (message) {
			$("#hdlight-loading").html(message);
		}
		var setX = ($(window).width() - $("#hdlight-loading").width()) / 2;
		var setY = ($(window).height() - $("#hdlight-loading").height()) / 2;
		$("#hdlight-loading").css({
			left: setX + "px",
			top: setY + "px",
			position: "fixed",
			zIndex: "99",
		});
		$("#hdlight-loading").fadeTo("slow", 1.0);
	}
	function hdlight_hide_loading() {
		$("#hdlight-loading").fadeOut("slow");
	}
	$("body").append("<div id=\"hdlight-loading\">Пожалуйста, подождите...</div>");
	
	function hdlight_view_output(url) {
		$(".hdlight-find-url").val(url);

		$(".hdlight-geo").attr("checked", false);  
		
		$("#hdlightFindUrlModal").modal("show");
	}
	$(".hdlight-find-url").click(function() {
		$(this).select();
	});
	
	function hdlight_set_output(url) {
		hdlight_show_loading();
		
		var field_output = "#<?=$field_output?>";
		
		$(field_output).val(url);
		
		$("#hdlightClearOutputButton").show();
		
		hdlight_hide_loading();
	}
	if ($("#<?=$field_output?>").val()) {
		$("#hdlightClearOutputButton").show();
	}
	$("#<?=$field_output?>").keyup(function() {
		if ($(this).val())
			$("#hdlightClearOutputButton").show();
		else
			$("#hdlightClearOutputButton").hide();
	});
	
	$("#hdlightFindButton").click(function() {
		hdlight_show_loading();
		
		var field_kinopoisk_id = "#<?=$field_kinopoisk?>";
		var field_world_art_id = "#<?=$field_worldart?>";
		var field_title = "#<?=$field_title?>";
		
		var kinopoisk_id = $(field_kinopoisk_id).val();
		var world_art_id = $(field_world_art_id).val();
		var title = $(field_title).val();

		var pornodb = $("#hdlightFindInPornoDB").is(":checked") ? true : false;
		
		var find_url = "/engine/ajax/hdlight.php?action=find";
		
		$("#hdlightClearFindButton").hide();
		$("#hdlightFindResults").html("");
		
		if ((kinopoisk_id != undefined && kinopoisk_id != "") || (world_art_id != undefined && world_art_id != "") || (title != undefined && title != "")) {
			if (kinopoisk_id != undefined && kinopoisk_id != "") {
				find_url += "&out_field_id=kinopoisk_id&out_base_id=" + kinopoisk_id;
			} else if (world_art_id != undefined && world_art_id != "") {
				find_url += "&out_field_id=world_art_id&out_base_id=" + world_art_id;
			} else {
				find_url += "&title=" + title;
			}

			if (pornodb)
				find_url += "&pornodb=1";
			
			$.ajax({
				url: find_url,
				dataType: "json",
				cache: false,
				success: function(data) {
					if (data.status == "error" && data.error == "videos_not_found") {
						$("#hdlightFindResults").html("<ul class=\"hdlight-list-group\"><li class=\"hdlight-list-group-item\">По вашему запросу ничего не найдено.</li></ul>");
					}
					
					if (data.status == "ok") {
						var results = "<ul class=\"hdlight-list-group\">";
						$.each(data.result, function(key, item) {
							var num = key + 1;
							
							if (item.type == "movie") {
								var type = "<span class=\"hdlight-label hdlight-label-blue\" title=\"Фильм\">Фильм</span>";
							} else {
								var type = "<span class=\"hdlight-label hdlight-label-green\" title=\"Сериал\">Сериал</span>";
							}

							if (item.camrip) {
								var hd_video = " &nbsp;<span class=\"hdlight-label hdlight-label-gray\" title=\"Качество\">CAM</span>";
							} else {
								var hd_video = " &nbsp;<span class=\"hdlight-label hdlight-label-gray\" title=\"Качество\">HD</span>";
							}

							if (item.translator) {
								var translator = " <span class=\"hdlight-label hdlight-label-purple\" title=\"Озвучка\">" + item.translator + "</span>";
							} else {
								var translator = " <span class=\"hdlight-label hdlight-label-purple\" title=\"Озвучка\">n/a</span>";
							}
							
							results += "<li class=\"hdlight-list-group-item\"><button type=\"button\" class=\"hdlight-btn hdlight-btn-xs hdlight-btn-default hdlight-btn-right\" onclick=\"hdlight_set_output('" + item.iframe_url + "')\">Вставить ссылку</button><button type=\"button\" class=\"hdlight-btn hdlight-btn-xs hdlight-btn-default hdlight-btn-right\" onclick=\"hdlight_view_output('" + item.iframe_url + "')\">Копировать ссылку</button>" + type + "&nbsp;&nbsp;" + (item.title_ru ? "<b>" + item.title_ru + "</b>" : "") + (item.title_ru && item.title_en ? " / " : "") + (item.title_en ? "<b>" + item.title_en + "</b>" : "") + hd_video + translator + "</li>";
						});
						results += "</ul>";
						
						$("#hdlightFindResults").html(results);
						$("#hdlightClearFindButton").show();
					}
					
					setTimeout(function() {
						hdlight_hide_loading();
					}, 0);
				}
			});
		} else {
			hdlight_hide_loading();
		}
	});
	
	$("#hdlightClearFindButton").click(function () {
		hdlight_show_loading();
		
		$("#hdlightFindResults").html("");
		$(this).hide();
		
		hdlight_hide_loading();
	});
	
	$("#hdlightClearOutputButton").click(function () {
		hdlight_show_loading();
		
		var field_output = "#<?=$field_output?>";
		
		$(field_output).val("");
		$(this).hide();
		
		hdlight_hide_loading();
	});

	$(".hdlight-geo").change(function() {
		var find_url = $(".hdlight-find-url").val();
		var geo_name = $(this).attr("name");

		if ($(this).is(":checked")) {
			if (find_url.indexOf("?") + 1)
				find_url += "&block_" + geo_name + "=1";
			else
				find_url += "?block_" + geo_name + "=1";
		} else {
			find_url = find_url.replace(new RegExp("[&]*block_" + geo_name + "=1[&]*"), "");
		}

		if (find_url.substr(find_url.length - 1, find_url.length) == "?")
			find_url = find_url.replace("?", "");

		$(".hdlight-find-url").val(find_url);
	});
	
	if (!$.fn.modal) {
	
		/*!
		 * Bootstrap v3.2.0 (http://getbootstrap.com)
		 * Copyright 2011-2014 Twitter, Inc.
		 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
		 */

		/*!
		 * Generated using the Bootstrap Customizer (http://getbootstrap.com/customize/?id=23203afa2fca48b8eccd)
		 * Config saved to config.json and https://gist.github.com/23203afa2fca48b8eccd
		 */
		if (typeof jQuery === "undefined") { throw new Error("Bootstrap's JavaScript requires jQuery") }

		/* ========================================================================
		 * Bootstrap: modal.js v3.2.0
		 * http://getbootstrap.com/javascript/#modals
		 * ========================================================================
		 * Copyright 2011-2014 Twitter, Inc.
		 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
		 * ======================================================================== */


		+function ($) {
		  'use strict';

		  // MODAL CLASS DEFINITION
		  // ======================

		  var Modal = function (element, options) {
			this.options        = options
			this.$body          = $(document.body)
			this.$element       = $(element)
			this.$backdrop      =
			this.isShown        = null
			this.scrollbarWidth = 0

			if (this.options.remote) {
			  this.$element
				.find('.modal-content')
				.load(this.options.remote, $.proxy(function () {
				  this.$element.trigger('loaded.bs.modal')
				}, this))
			}
		  }

		  Modal.VERSION  = '3.2.0'

		  Modal.DEFAULTS = {
			backdrop: true,
			keyboard: true,
			show: true
		  }

		  Modal.prototype.toggle = function (_relatedTarget) {
			return this.isShown ? this.hide() : this.show(_relatedTarget)
		  }

		  Modal.prototype.show = function (_relatedTarget) {
			var that = this
			var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

			this.$element.trigger(e)

			if (this.isShown || e.isDefaultPrevented()) return

			this.isShown = true

			this.checkScrollbar()
			this.$body.addClass('modal-open')

			this.setScrollbar()
			this.escape()

			this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

			this.backdrop(function () {
			  var transition = $.support.transition && that.$element.hasClass('fade')

			  if (!that.$element.parent().length) {
				that.$element.appendTo(that.$body) // don't move modals dom position
			  }

			  that.$element
				.show()
				.scrollTop(0)

			  if (transition) {
				that.$element[0].offsetWidth // force reflow
			  }

			  that.$element
				.addClass('in')
				.attr('aria-hidden', false)

			  that.enforceFocus()

			  var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

			  transition ?
				that.$element.find('.modal-dialog') // wait for modal to slide in
				  .one('bsTransitionEnd', function () {
					that.$element.trigger('focus').trigger(e)
				  })
				  .emulateTransitionEnd(300) :
				that.$element.trigger('focus').trigger(e)
			})
		  }

		  Modal.prototype.hide = function (e) {
			if (e) e.preventDefault()

			e = $.Event('hide.bs.modal')

			this.$element.trigger(e)

			if (!this.isShown || e.isDefaultPrevented()) return

			this.isShown = false

			this.$body.removeClass('modal-open')

			this.resetScrollbar()
			this.escape()

			$(document).off('focusin.bs.modal')

			this.$element
			  .removeClass('in')
			  .attr('aria-hidden', true)
			  .off('click.dismiss.bs.modal')

			$.support.transition && this.$element.hasClass('fade') ?
			  this.$element
				.one('bsTransitionEnd', $.proxy(this.hideModal, this))
				.emulateTransitionEnd(300) :
			  this.hideModal()
		  }

		  Modal.prototype.enforceFocus = function () {
			$(document)
			  .off('focusin.bs.modal') // guard against infinite focus loop
			  .on('focusin.bs.modal', $.proxy(function (e) {
				if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
				  this.$element.trigger('focus')
				}
			  }, this))
		  }

		  Modal.prototype.escape = function () {
			if (this.isShown && this.options.keyboard) {
			  this.$element.on('keyup.dismiss.bs.modal', $.proxy(function (e) {
				e.which == 27 && this.hide()
			  }, this))
			} else if (!this.isShown) {
			  this.$element.off('keyup.dismiss.bs.modal')
			}
		  }

		  Modal.prototype.hideModal = function () {
			var that = this
			this.$element.hide()
			this.backdrop(function () {
			  that.$element.trigger('hidden.bs.modal')
			})
		  }

		  Modal.prototype.removeBackdrop = function () {
			this.$backdrop && this.$backdrop.remove()
			this.$backdrop = null
		  }

		  Modal.prototype.backdrop = function (callback) {
			var that = this
			var animate = this.$element.hasClass('fade') ? 'fade' : ''

			if (this.isShown && this.options.backdrop) {
			  var doAnimate = $.support.transition && animate

			  this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
				.appendTo(this.$body)

			  this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
				if (e.target !== e.currentTarget) return
				this.options.backdrop == 'static'
				  ? this.$element[0].focus.call(this.$element[0])
				  : this.hide.call(this)
			  }, this))

			  if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

			  this.$backdrop.addClass('in')

			  if (!callback) return

			  doAnimate ?
				this.$backdrop
				  .one('bsTransitionEnd', callback)
				  .emulateTransitionEnd(150) :
				callback()

			} else if (!this.isShown && this.$backdrop) {
			  this.$backdrop.removeClass('in')

			  var callbackRemove = function () {
				that.removeBackdrop()
				callback && callback()
			  }
			  $.support.transition && this.$element.hasClass('fade') ?
				this.$backdrop
				  .one('bsTransitionEnd', callbackRemove)
				  .emulateTransitionEnd(150) :
				callbackRemove()

			} else if (callback) {
			  callback()
			}
		  }

		  Modal.prototype.checkScrollbar = function () {
			if (document.body.clientWidth >= window.innerWidth) return
			this.scrollbarWidth = this.scrollbarWidth || this.measureScrollbar()
		  }

		  Modal.prototype.setScrollbar = function () {
			var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
			if (this.scrollbarWidth) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
		  }

		  Modal.prototype.resetScrollbar = function () {
			this.$body.css('padding-right', '')
		  }

		  Modal.prototype.measureScrollbar = function () { // thx walsh
			var scrollDiv = document.createElement('div')
			scrollDiv.className = 'modal-scrollbar-measure'
			this.$body.append(scrollDiv)
			var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
			this.$body[0].removeChild(scrollDiv)
			return scrollbarWidth
		  }


		  // MODAL PLUGIN DEFINITION
		  // =======================

		  function Plugin(option, _relatedTarget) {
			return this.each(function () {
			  var $this   = $(this)
			  var data    = $this.data('bs.modal')
			  var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

			  if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
			  if (typeof option == 'string') data[option](_relatedTarget)
			  else if (options.show) data.show(_relatedTarget)
			})
		  }

		  var old = $.fn.modal

		  $.fn.modal             = Plugin
		  $.fn.modal.Constructor = Modal


		  // MODAL NO CONFLICT
		  // =================

		  $.fn.modal.noConflict = function () {
			$.fn.modal = old
			return this
		  }


		  // MODAL DATA-API
		  // ==============

		  $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
			var $this   = $(this)
			var href    = $this.attr('href')
			var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
			var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

			if ($this.is('a')) e.preventDefault()

			$target.one('show.bs.modal', function (showEvent) {
			  if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
			  $target.one('hidden.bs.modal', function () {
				$this.is(':visible') && $this.trigger('focus')
			  })
			})
			Plugin.call($target, option, this)
		  })

		}(jQuery);
	
	}
-->
</script>