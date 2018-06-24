var ScriptPKP = function (conf_pkp) {
    var pStart = false,
        pStStart = false,
        tio = 1,
        news_id = 0,
        parsing_data = null,
        form_is_ww = false, //Форма wysywyg
        not_status_proc = false,
        _self = this,
        imageLoaderMaxLI = 5, //Максимальное кол. загружаемых изображений одновременно
        imageLoaderListTimeout = [];

    /**
     * ID счетчика интервала для запроса статуса парсинга
     * @type {mix}
     */
    var statusIntervalId = false;

    this.init = function () {
        tio = Math.floor(Math.random() * (999 - 2) + 2);
        news_id = conf_pkp.news_id;
        not_status_proc = conf_pkp.not_status_proc;

        $("input[name='title']").parent('td').append();

        if ($("input[name='title']").parent('td').length) {

            var tbtn = '&ensp;<input class="edit btn btn-mini" type="button" onClick="PKP.getList(false); return false;" style="width:160px;" value="' + conf_pkp.btn_name + '">' +
                '<div id="pkinopoisk_list" style="background:none repeat scroll 0 0 #FFFFCC;border:1px solid #9E9E9E;margin-right:10px;margin-top:7px;max-height:150px;overflow:auto;padding:5px;width:600px;display: none;"></div>' +
                '<div id="pkinopoisk_result" style="position:relative;background:none repeat scroll 0 0 #FFFFCC;border:1px solid #9E9E9E;margin-right:10px;margin-top:7px;padding:5px;width:600px;max-height:600px;overflow:auto;padding:5px;display:none;"></div>' +
                '<div id="pkinopoisk_status" style="background:none repeat scroll 0 0 #FFFFCC;border:1px solid #9E9E9E;margin-right:10px;margin-top:7px;padding:5px;width:600px;padding:5px;display:none;"></div>';

            $("input[name='title']").parent('td').append(tbtn);

        } else if ($("input[name='title']").parent('div').length) {

            var tbtn = '&ensp;<button class="btn btn-sm btn-black" type="button" onClick="PKP.getList(false); return false;">' + conf_pkp.btn_name + '</button>' +
                '<div id="pkinopoisk_list" style="background:none repeat scroll 0 0 #FFFFCC;border:1px solid #9E9E9E;margin-right:10px;margin-top:7px;max-height:150px;overflow:auto;padding:5px;width:600px;display: none;"></div>' +
                '<div id="pkinopoisk_result" style="position:relative;background:none repeat scroll 0 0 #FFFFCC;border:1px solid #9E9E9E;margin-right:10px;margin-top:7px;padding:5px;width:600px;max-height:600px;overflow:auto;padding:5px;display:none;"></div>' +
                '<div id="pkinopoisk_status" style="background:none repeat scroll 0 0 #FFFFCC;border:1px solid #9E9E9E;margin-right:10px;margin-top:7px;padding:5px;width:600px;padding:5px;display:none;"></div>';

            $("input[name='title']").parent('div').append(tbtn);

        }

        if (conf_pkp.keyEPS) {
            $("input[name='title']").keypress(function (event) {
                if (event.keyCode == 13) {
                    _self.getList('list');
                    return false;
                }
            });
        }

        if (typeof(create_editor) == 'function' || typeof(tinyMCE) == 'object') {
            form_is_ww = true;
        }
    };

    this.getList = function (fullList) {
        var id = fullList ? 'list_all' : 'list';

        var title = $("input[name='title']").val();
        title = $.trim(title);

        var container = '#pkinopoisk_list';

        if (title == '') {
            insertToCont(container, '<span style="color: red;">Для поиска введите название фильма в заголовок статьи!</span>');
            return false;
        }

        var data = {
            id: id,
            title: title
        };

        parsing(data, container);
    };

    this.getUp = function (id) {

        var data = {
            id: id,
            title: 'get_up',
            sact: 'get_up'
        };

        var container = '#pkinopoisk_result';

        parsing(data, container);

    };

    this.choosePoster = function (id) {

        chooseImageAction(id, 'poster_choose', '#poster_choose', '#poster_choose_list', 'poster_choose_list');

    };

    this.chooseKadr = function (id) {

        chooseImageAction(id, 'kadr_choose', '#kadr_choose', '#kadr_choose_list', 'kadr_choose_list');

    };

    function chooseImageAction(id, sact, mainCont, listCont, inputNameList) {

        if ($(mainCont).attr('data-load') == 1) {
            $(listCont).toggle();

            if ($(listCont).is(':visible')) {
                startLoadImage();
            }

            return false;
        }

        var data = {
            id: id,
            title: sact,
            sact: sact
        };

        parsing(data, '', choose);

        $(mainCont).attr('data-load', '1');

        /**
         * Вызывается после загруски списка изображений
         */
        function choose() {
            $(listCont + ' li').click(function () {
                var id = $(this).attr('data-image_id');
                var choosed = $(this).attr('data-choosed');

                if (choosed == 1) {
                    $(this).removeClass('choosed');
                    $(mainCont + ' input[name="' + inputNameList + '[' + id + ']"]').remove();
                    $(this).attr('data-choosed', '0');
                } else {
                    $(this).addClass('choosed');
                    $(mainCont).append('<input type="hidden" name="' + inputNameList + '[' + id + ']" value="' + id + '">');
                    $(this).attr('data-choosed', '1');
                }

                updateAllNum();
            });

            startLoadImage();
        }

        /**
         * Подсчет выделенных изображений
         */
        function updateAllNum() {
            var i = 0;
            var id = 0;

            $(mainCont).find('input[name^="' + inputNameList + '["]').each(function () {

                i++;
                id = $(this).val();
                $(mainCont).find('[data-image_id="' + id + '"] .num').html(i);

            });

            $(mainCont + ' .num_all').html(i);
        }

        var curL = 0;

        /**
         * Загрузка превью изображения
         * @param el
         */
        function loadImage(el) {

            var kp_id = $(el).attr('data-kp_id'),
                type = $(el).attr('data-type'),
                page_num = $(el).attr('data-page_num'),
                link = $(el).attr('data-link');

            if (curL >= imageLoaderMaxLI) {

                var tId = setTimeout(function () {
                    loadImage(el)
                }, 500);

                imageLoaderListTimeout.push(tId);

            } else {

                curL++;

                var data = {
                    'title': 'load_image',
                    'sact': 'load_image',
                    'kp_id': kp_id,
                    'type': type,
                    'page_num': page_num,
                    'link': link
                };

                $.ajax({
                    url: '/engine/ajax/pkinopoisk.php',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (r) {
                        if (r.text) {
                            $(el).attr({
                                'src': r.text,
                                'data-loaded': '1'
                            });
                        }

                        curL--;

                        checkFullLoadImage()
                    }
                });

            }
        }

        /**
         * Старт процесса загруски изображений
         */
        function startLoadImage() {

            if ($(mainCont).find('img[data-loaded="0"]').length == 0) {
                return;
            }

            $(mainCont).find('.stop_load_image').show();

            $(mainCont).find('.stop_load_image').click(function () {
                stopLoadImage();
            });

            $(mainCont).find('img[data-loaded="0"]').each(function () {

                if ($(this).attr('data-loaded') == '0' && $(this).attr('data-link')) {
                    loadImage(this);
                }

            });
        }

        /**
         * Сток загрузки изображений
         * @returns {boolean}
         */
        function stopLoadImage() {
            $(mainCont).find('.stop_load_image').hide();

            var tl = imageLoaderListTimeout.length;

            for (var i = 0; i < tl; i++) {
                var tId = imageLoaderListTimeout.shift()
                clearTimeout(tId);
            }

            return false;
        }

        function checkFullLoadImage() {
            if ($(mainCont).find('img[data-loaded="0"]').length == 0) {
                stopLoadImage();
            }
        }
    }

    this.getUpMovei = function (id) {

        var data = {
            id: id,
            title: 'getUpMovei'
        };

        var name = null;
        var value = null;

        $('#pkinopoisk_result :input').each(function () {
            name = $(this).attr('name');
            value = $(this).val();

            data[name] = value;
        });

        parsing(data, '#pkinopoisk_result');

    };

    function parsing(data, container, callFunc) {
        if (pStart) {
            return false;
        }

        data.tio = tio;
        data.news_id = news_id;

        $.ajax({
            url: '/engine/ajax/pkinopoisk.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (result) {

                if (!result) {
                    return false;
                }

                if (result.container) {
                    container = result.container;
                }

                if (result.is_get_up) {
                    parsing_data = result;
                }

                var append = false;
                if (result.append) {
                    append = true;
                }

                insertToCont(container, result.text, append);

                if (callFunc) {
                    callFunc();
                }

            },
            beforeSend: function () {
                pStart = true;
                statusProc();
                if (typeof(ShowLoading) == 'function') {
                    ShowLoading('')
                }
            },
            complete: function () {
                pStart = false;
                if (typeof(HideLoading) == 'function') {
                    HideLoading('');
                }
            },
            error: function (obj) {
                pStart = false;
                if (typeof(HideLoading) == 'function') {
                    HideLoading('');
                }

                var errstr = $.trim(obj.responseText);

                if (errstr) {
                    insertToCont(container, '<span style="color: red;">' + errstr + '</span>');
                }
            }
        });
    }

    /**
     * При необходимости заменяем тег <br> на перенос строки
     * @param text
     * @param a Делать замену всегда
     * @returns {*}
     */
    function reBrtoN(text, a) {
        if ((!form_is_ww || a) && text) {
            text = text.replace(/(<br \/>|<br\/>|<br>)/gi, "\n");
        }
        return text;
    }

    /**
     * Оформляем пост
     * @param fd
     * @param w Способ оформления: r - заменить; s - добавить в начало; e - добавить в конец;
     * @returns {boolean}
     */
    this.fill_form = function (fd, w) {
        var setInputValue = function (e, nval, w) {
            if (w == 'e') {
                nval = $(e).val() + nval;
            } else if (w == 's') {
                nval += $(e).val();
            }

            $(e).val(nval);
        };

        var setInputTokenField = function (el, val) {
            if (typeof($(el).tagsInput) == "function") {
                $(el).importTags(val);
            } else if (typeof($(el).tokenfield) == "function"){
                $(el).tokenfield('setTokens', val);
            }
        };

        var setInputLinks = function (key, nval, w) {

            var exrl = '[name="xfield[' + key + ']"][data-rel="links"]';

            if ($('select[name="xfield[' + key + ']"]').length > 0) {

                nval = nval.toLowerCase();

                $('select[name="xfield[' + key + ']"] option').removeAttr('selected');
                $('select[name="xfield[' + key + ']"] option').each(function () {
                    $(this).selected = false;
                });

                $('select[name="xfield[' + key + ']"] option').each(function () {

                    var v = $(this).html();
                    v = v.toLowerCase();

                    if (nval == v) {
                        $(this).attr('selected', 'selected');
                        $(this).selected = true;

                        return false;
                    }

                });

                if ($('select[name="xfield[' + key + ']"].uniform').length) {
                    $('select[name="xfield[' + key + ']"].uniform').uniform();
                }

            } else if ($(exrl).length > 0 && (typeof($(exrl).tagsInput) == "function" || typeof($(exrl).tokenfield) == "function")) {

                if (w == 'e') {
                    nval = $(exrl).val() + ',' + nval;
                } else if (w == 's') {
                    nval += ',' + $(exrl).val();
                }

                setInputTokenField(exrl, nval);

            } else {

                setInputValue('[name="xfield[' + key + ']"],[id="xfield[' + key + ']"]', nval, w);

            }

        };

        var fdata = {},
            val = null;

        if (fd) {
            fdata = fd;
        } else {
            fdata = parsing_data;
        }

        if (fdata.fill_main) {
            for (var key in fdata.fill_main) {
                val = fdata.fill_main[key];
                val = reBrtoN(val);

                if (val != '') {
                    setInputValue("[name='" + key + "']", val, w);
                }
            }
        }


        if (fdata.fill_tags && fdata.fill_tags.length > 0) {
            var nval = fdata.fill_tags;

            if (w == 'e') {
                nval = $('[name="tags"]').val() + ',' + nval;
            } else if (w == 's') {
                nval += ',' + $('[name="tags"]').val();
            }

            if ($('[name="tags"]').length > 0 && (typeof($('[name="tags"]').tagsInput) == "function" || typeof($('[name="tags"]').tokenfield) == "function")) {
                setInputTokenField('[name="tags"]', nval);
            } else {
                setInputValue('[name="tags"]', nval, w);
            }
        }


        if (fdata.fill_category && fdata.fill_category.length > 0) {
            var cat = $("[name='category[]'], [name='catlist[]']").val();
            cat = $.isArray(cat) ? $.merge(cat, fdata.fill_category) : fdata.fill_category;

            var cat_tn = cat;
            var cat_tn_l = cat_tn.length;
            cat = [];

            for (var i = 0; i < cat_tn_l; i++) {
                if (cat_tn[i] > 0) {
                    cat.push(cat_tn[i]);
                }
            }

            $("[name='category[]'], [name='catlist[]']").val(cat);

            if ($("[name='category[]'], [name='catlist[]']").hasClass("chzn-done")) {
                $("[name='category[]'], [name='catlist[]']").trigger("liszt:updated");
            }
        }

        if (fdata.fill_xfield) {
            for (var key in fdata.fill_xfield) {
                val = fdata.fill_xfield[key];
                val = reBrtoN(val);

                if (val != '') {

                    setInputLinks(key, val, w);

                }
            }
        }

        if (fdata.fill_person) {
            for (var key in fdata.fill_person) {
                val = fdata.fill_person[key];
                val = reBrtoN(val, true);

                if (val != '') {
                    setInputValue("#" + key, val, w);
                }
            }
        }

        if (typeof(create_editor) == 'function') {
            create_editor();
        }

        //Для модуля connect_person_movie
        if (typeof(CPM) == 'object' && typeof(CPM.add_cpm_all) == 'function' && fdata.cpm) {
            CPM.add_cpm_all('movie', fdata.cpm);
        }

        return false;
    };

    /**
     * До оформить
     * @param f Массив состоящий из названия и подназвания данных. Например ['fill_main', 'short_story']
     * @param w Способ оформления: r - заменить; s - добавить в начало; e - добавить в конец;
     */
    this.to_make_form = function (f, w) {
        var data = {},
            f_l = f.length;

        if (f_l == 2 && parsing_data[f[0]] && parsing_data[f[0]][f[1]]) {

            data[f[0]] = {};
            data[f[0]][f[1]] = parsing_data[f[0]][f[1]];

        } else if (f_l == 1 && f[0] == 'all') {

            data = parsing_data;

        } else if (f_l == 1 && parsing_data[f[0]]) {

            data[f[0]] = parsing_data[f[0]];

        }

        _self.fill_form(data, w);

        return false;
    };

    function insertToCont(el, html, append) {
        if (append) {
            $(el).show().append(html);
        } else {
            $(el).show().html(html);
        }
    }

    function statusProc() {
        if (not_status_proc) {
            return false;
        }

        var statusCont = '#pkinopoisk_status';

        function getStatus() {

            if (!pStart) {
                stopProc();
                return false;
            } else if (pStStart) {
                return false;
            }

            $.ajax({
                url: '/engine/ajax/pkinopoisk.php',
                type: 'POST',
                data: 'tio=' + tio + '&id=tio',
                global: false,
                dataType: 'json',
                success: function (result) {
                    if (result.text == 'finish') {
                        stopProc();
                        return;
                    }

                    insertToCont(statusCont, result.text);
                },
                beforeSend: function () {
                    pStStart = true;
                },
                complete: function () {
                    pStStart = false;
                }
            });
        }

        function startProc() {
            if (pStart && statusIntervalId === false) {
                statusIntervalId = setInterval(function () {
                    getStatus();
                }, 750);
            }
        }

        function stopProc() {
            $(statusCont).hide();
            clearInterval(statusIntervalId);
            statusIntervalId = false;
        }

        setTimeout(startProc, 500);
    }

};

var PKP = new ScriptPKP(config_pkp);

setTimeout(function () {
    if (typeof(jQuery) == "undefined") {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'http://code.jquery.com/jquery-latest.min.js';
        document.getElementsByTagName('head')[0].appendChild(script);
        if (script.addEventListener) {
            script.addEventListener('load', PKP.init, false);
        } else if (script.attachEvent) {
            script.attachEvent('onload', PKP.init)
        }
    } else {
        PKP.init();
    }
}, 250);