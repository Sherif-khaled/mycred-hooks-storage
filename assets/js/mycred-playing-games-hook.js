/**
 * myCRED Points for Playing Game jQuery Scripts
 * @author Sherif Khaled
 */

let object_interval = 0;
let game_interval = 0;

setTimeout(() => {

    jQuery(document).ready(function ($) {

        jQuery.each(elementorFrontend.documentsManager.documents, (id, document) => {
            if (document.getModal) {

                document.getModal().on('show', () => {
                    timer(true);
                });

                document.getModal().on('hide', () => {


                    let active_iframe = $(this).find('iframe').contents().find("body");

                    let game_title = active_iframe.find('.title').text();
                    let best_score = active_iframe.find('.best-container').text();
                    let score = active_iframe.find('.score-container').clone()
                        .children()
                        .remove()
                        .end().text();

                    let game_data = {
                        'title': game_title,
                        'id': id,
                        'best_score': best_score,
                        'current_score': score,
                        'interval': game_interval
                    };

                    let data = {
                        'action': 'playing_game',
                        'token': myCREDgame.token,
                        'ctype': 'mycred_default',
                        game_data: game_data
                    };

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: myCREDgame.ajaxurl,
                        data: data,
                        success: function (response) {
                            console.log(
                                "اسم اللعبة :" + response.game_title + "\n" +
                                "الرقم المعرف :" + response.game_id + "\n" +
                                "المدة الزمنية :" + response.game_interval + "\n" +
                                "السكور الحالى :" + response.current_score + "\n" +
                                "افضل أسكور :" + response.game_best_score + "\n" +
                                "نقاط المكسب :" + response.eared_points
                            );
                        },
                        error: function (response) {
                            console.log(response);
                        },
                        complete: function () {
                            timer(false);
                            location.reload();

                        }
                    });

                });
            }
        });

        function timer(start = false) {
            let start_date = new Date;

            if (start === false) {
                clearInterval(object_interval);
                // start_date = null;
                game_interval = 0;
                return 0;
            }

            object_interval = setInterval(function () {
                game_interval = Math.floor((new Date - start_date) / 1000);
            }, 1000);
        }

    });

}, 3000);