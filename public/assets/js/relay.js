$(document).ready(function() {

    $(".relay-control[data-swift-topic] > button").click(function () {
        let $this = $(this);
        let topic = $this.parent().data('swift-topic');
        let obj = $this.parent();

        $.post( "/api/device/send", { topic: topic, payload: $this.val() })
            .fail(function() { alert( "error posting to swift" ) })
            .done(function( data ) { console.log( data ); });

        obj.find('button').map(function (keybtn, valuebtn) {
            $(valuebtn).removeClass('active');
        });
        $this.addClass('active');
    });

    function swiftStateRefrash() {
        let $this = $(".relay-control[data-swift-topic]");
        if($this.length > 0) {
            let relays = [];

            $this.map(function (key, value) {
                relays.push($(value).data('swift-topic-check'));
            });
            if ($(relays)[0] !== '') {
                $.get("/api/device/topics?topics="+relays, function (data) {
                    $.each(data, function( index, value ) {
                        let sensor = $("div[data-swift-topic-check='"+index+"']");
                        $(sensor).find('button').map(function (keybtn, valuebtn) {
                            if (value === null) {
                                $(".relay-control").parent().parent().addClass('warrios');
                            } else {
                                $(".relay-control").parent().parent().removeClass('warrios');
                            }

                            if ($(valuebtn).data('swift-check') == value) {
                                $(valuebtn).addClass('active');
                            }
                            else {
                                $(valuebtn).removeClass('active');
                            }
                        });
                    });
                })
                .done(function() {
                    $(".relay-control").parent().parent().removeClass('connection-alert');
                })
                .fail(function() {
                    $(".relay-control").parent().parent().addClass('connection-alert');
                });
            }

            setTimeout(swiftStateRefrash, 10000);
        }
    }

    swiftStateRefrash();
});
