$(document).ready(function() {
    function sensorsRefrash() {
        if($('div').is($('.sensor-control'))) {
            let $this = $('.sensor-control[data-sensor-topic]');
            let sensors = [];

            $this.map(function (key, value) {
                sensors.push($(value).data('sensor-topic'));
            });

            $.get('/api/device/topics?topics='+sensors, function (data) {
                $.each(data, function( index, value ) {
                    if (value === null) {
                        $("span[data-sensor-value='"+index+"']").parent().parent().parent().addClass('warrios');
                        value = '--';
                    } else {
                        $("span[data-sensor-value='"+index+"']").parent().parent().parent().removeClass('warrios');
                    }

                    $("span[data-sensor-value='"+index+"']").text(value);
                });
            })
            .done(function() {
                $('span[data-sensor-value]').parent().parent().parent().removeClass('connection-alert');
            })
            .fail(function() {
                $('span[data-sensor-value]').parent().parent().parent().addClass('connection-alert');
            });

            setTimeout(sensorsRefrash, 10000);
        }
    }

    sensorsRefrash();
});
