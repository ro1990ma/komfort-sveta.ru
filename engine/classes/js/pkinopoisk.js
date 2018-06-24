/* Настройки модуля */
var ScriptPKPConf = function () {
    this.init = function () {

        conf_load();

        $("#form_conf_pkp").submit(conf_save);

        $(":input").change(function () {
            $("#mess").html('');
        });

        $(".conf_tabs_btn").click(conf_tab_ch);

        $("#dop_name_btn").click(function () {
            $("#dop_name").toggle();
            return false;
        });

        $("#tampls_pers_btn").click(add_template_person);
    };

    function conf_load() {
        $.ajax({
            url: '/' + dle_admin_path + '?mod=pkinopoisk',
            type: 'POST',
            data: 'mod_act=get_conf',
            dataType: 'json',
            success: function (response) {

                if (!response.error) {

                    var tc = ['conf', 'template', 'template_xfields', 'template_person', 'template_user', 'cat_match'];
                    var tc_l = tc.length;

                    for (var i = 0; i < tc_l; i++) {
                        var ktc = tc[i];
                        var kc = response.success[ktc];

                        if (kc) {
                            for (var key in kc) {
                                $("[name^='config[" + ktc + "][" + key + "]']").val(kc[key]);
                            }
                        }
                    }

                    $("#info_mess").hide();

                    if (!$('.nav-tabs').length) {
                        $('.tab-pane').show();
                        initTabs('dle_tabView1', initTabsList, 0, '100%');
                    }

                    $("#form_conf_pkp_submit").show();

                } else {

                    $("#info_mess").html(response.error);

                }

            }
        });
    }

    function conf_save() {
        $("#mess").html('');

        var data = $(this).serialize();

        $.ajax({
            url: '/' + dle_admin_path + '?mod=pkinopoisk',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {

                if (!response.error) {
                    $("#mess").html(response.success);
                }

            }
        });

        return false;
    }

    function conf_tab_ch() {
        var name = $(this).attr('href');
        $(".conf_tabs").hide();
        $(name).show();

        return false;
    }

    //Template person
    var atp = 0;

    function add_template_person() {
        atp++;

        var pol = '<tr>' +
            '<td><input name="template_person_key[n' + atp + ']" type="text" value="" style="width: 250px;"></td>' +
            '<td style="padding: 5px 0;"><textarea name="config[template_person][n' + atp + ']" style="height:125px;width:550px;"></textarea></td>' +
            '</tr>';

        $("#tampls_pers_list").append(pol);
    }
};

var PKPConf = new ScriptPKPConf();

setTimeout(function () {
    if (typeof(jQuery) == "undefined") {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'http://code.jquery.com/jquery-latest.min.js';
        document.getElementsByTagName('head')[0].appendChild(script);
        if (script.addEventListener) {
            script.addEventListener('load', PKPConf.init, false);
        } else if (script.attachEvent) {
            script.attachEvent('onload', PKPConf.init)
        }
    } else {
        PKPConf.init();
    }
}, 100);
