$(document).ready(function() {

    function sensorsRefresh() {
        if($("div").is($(".secure-sensor-control"))) {
            let $this = $(".secure-sensor-control[data-secstate-topic]");
            let stateOn  = '<i class="fas fa-running"></i>';
            let stateOff = '<i class="fas fa-male"></i>';

            $this.map(function (key, value) {
                let topic = $(value).data('secstate-topic');
                $.get("/api/secure/state?topic="+topic, function (data) {
                    console.log(data);
                    if (data['isTriggered'] === true) {
                        $(value).find('.secure-trigger-on').removeClass('active').addClass('active');
                        $(value).find('.secure-trigger-off').removeClass('active');
                    }
                    if (data['isTriggered'] === false) {
                        $(value).find('.secure-trigger-on').removeClass('active');
                        $(value).find('.secure-trigger-off').removeClass('active').addClass('active');
                    }
                    if (data['state'] == false) {
                        $(value).find('.secure-state-info').html(stateOff);
                    }
                    if (data['state'] == true) {
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
                });
            });

            setTimeout(sensorsRefresh, 5000);
        }
    }

    sensorsRefresh();

    if($("div").is($(".secure-sensor-control"))) {
        let $this = $(".secure-sensor-control[data-secstate-topic]");
        $this.map(function (key, value) {
            $(value).find('.secure-trigger-on').on('click', function () {
                let $this = $(this).parent();
                $.post("/api/secure/trigger", { topic: $this.data('secstate-topic'), trigger: true })
                    .done(function(data) {
                        console.log(data);
                        $(value).find('.secure-trigger-on').removeClass('active').addClass('active');
                        $(value).find('.secure-trigger-off').removeClass('active');
                    });
            });
            $(value).find('.secure-trigger-off').on('click', function () {
                let $this = $(this).parent();
                $.post("/api/secure/trigger", { topic: $this.data('secstate-topic'), trigger: false })
                    .done(function(data) {
                        console.log(data);
                        $(value).find('.secure-trigger-on').removeClass('active');
                        $(value).find('.secure-trigger-off').removeClass('active').addClass('active');
                    });
            });
        });
    }
});