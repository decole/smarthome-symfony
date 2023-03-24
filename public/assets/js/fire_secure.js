$(document).ready(function() {
    function sensorsRefrash() {
        if($("div").is($(".fire-sensor-control"))) {
            let $this = $(".fire-sensor-control[data-secstate-topic]");
            $this.map(function (key, value) {
                let topic = $(value).data('secstate-topic');
                let normalState = $(value).data('state-normal');
                let alertState = $(value).data('state-alert');
                $.get("/api/device/topics?topics="+topic, function (data) {
                    if (data[topic] == normalState) {
                        $this.find('.btn-outline-success').addClass('active').show();
                        $this.find('.btn-outline-danger').removeClass('active').hide();
                    }
                    if (data[topic] == alertState) {
                        $this.find('.btn-outline-danger').addClass('active').show();
                        $this.find('.btn-outline-success').removeClass('active').hide();
                    }

                    if (data[topic] === null) {
                        $(".fire-sensor-control").parent().parent().addClass('warrios');
                    } else {
                        $(".fire-sensor-control").parent().parent().removeClass('warrios');
                    }
                })
                .done(function() {
                    $(".fire-sensor-control").parent().parent().removeClass('connection-alert');
                })
                .fail(function() {
                    $(".fire-sensor-control").parent().parent().addClass('connection-alert');
                });
            });

            setTimeout(sensorsRefrash, 5000);
        }
    }

    sensorsRefrash();
});