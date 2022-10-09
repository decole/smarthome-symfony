$(document).ready(function() {

    function sensorsRefrash() {
        if($("div").is($(".fire-sensor-control"))) {
            let $this = $(".fire-sensor-control[data-secstate-topic]");
            $this.map(function (key, value) {
                let topic = $(value).data('secstate-topic');
                $.get("/api/mqtt?topic="+topic, function (data) {
                    if (data['payload'] == 0) {
                        $this.find('.btn-outline-success').addClass('active').show();
                        $this.find('.btn-outline-danger').removeClass('active').hide();
                    }
                    if (data['payload'] == 1) {
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