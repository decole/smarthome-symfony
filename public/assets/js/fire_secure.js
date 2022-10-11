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
                });
            });

            setTimeout(sensorsRefrash, 5000);
        }
    }

    sensorsRefrash();
});