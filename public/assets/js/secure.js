$(document).ready(function() {

    function sensorsRefrash() {
        if($("div").is($(".secure-sensor-control"))) {
            let $this = $(".secure-sensor-control[data-secstate-topic]");
            let stateOn  = '<i class="fas fa-running"></i>';
            let stateOff = '<i class="fas fa-male"></i>';

            $this.map(function (key, value) {
                let topic = $(value).data('secstate-topic');
                console.log(topic);
                $.get("/api/secure/state?topic="+topic, function (data) {
                    console.log(data);
                    /*
                    if (data['trigger'] === true) {
                        $(value).find('.secure-trigger-on').removeClass('active').addClass('active');
                        $(value).find('.secure-trigger-off').removeClass('active');
                        // $(value).parent().parent().find('.secure-sensor-state-text').html('Взведен');
                    }
                    if (data['trigger'] === false) {
                        $(value).find('.secure-trigger-on').removeClass('active');
                        $(value).find('.secure-trigger-off').removeClass('active').addClass('active');
                        // $(value).parent().parent().find('.secure-sensor-state-text').html('Не взведен');
                    }
                    if (data['state'] == 0) {
                        $(value).find('.secure-state-info').html(stateOff);
                    }
                    if (data['state'] == 1) {
                        $(value).find('.secure-state-info').html(stateOn).removeClass('active').addClass('active');
                        function run() {
                            if ($(value).find('.secure-state-info').hasClass('active')) {
                                $(value).find('.secure-state-info').removeClass('active');
                            } else {
                                $(value).find('.secure-state-info').addClass('active');
                            }
                        }
                        setTimeout(run,1000);setTimeout(run,1500);
                        setTimeout(run,2000);setTimeout(run,2500);
                        setTimeout(run,3000);setTimeout(run,3500);
                        setTimeout(run,4000);setTimeout(run,4500);
                    }
                    */
                });
            });



            setTimeout(sensorsRefrash, 5000);
        }
    }

    sensorsRefrash();

    if($("div").is($(".secure-sensor-control"))) {
        let $this = $(".secure-sensor-control[data-secstate-topic]");
        $this.map(function (key, value) {
            $(value).find('.secure-trigger-on').on('click', function () {
                let $this = $(this).parent();
                $.post("/api/secure-command", { topic: $this.data('secstate-topic'), trigger: "on" })
                    .done(function(data) {
                        $(value).parent().parent().find('.secure-sensor-state-text').html('Запрос обрабатывается');
                    });
            });
            $(value).find('.secure-trigger-off').on('click', function () {
                let $this = $(this).parent();
                $.post("/api/secure-command", { topic: $this.data('secstate-topic'), trigger: "off" })
                    .done(function(data) {
                        $(value).parent().parent().find('.secure-sensor-state-text').html('Запрос обрабатывается');
                    });
            });
        });
    }

});