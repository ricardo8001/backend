var audioStart = new Audio('http://4.228.108.166/dash/checkers/audios/start.mp3');
var audioLives = new Audio('http://4.228.108.166/dash/checkers/audios/live.mp3');

$(document).ready(function () {

    $('#start').click(function () {

        audioStart.play();

        $('#start').attr('disabled', true);
        $('#stop').attr('disabled', false);

        var line = $('#list').val().split('\n');
        var total = line.length;

        var ap = 0, rp = 0, sd = 0;

        if (total > $("#list").attr("limite")) {
            $.toast({
                text: 'O limite de linhas foi ultrapassado, tente um limite menor por favor.',
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'error',
                hideAfter: 3000,
                stack: 6
            });
            return false;
        }

        $.toast({
            text: 'O seu teste foi iniciado com sucesso.',
            position: 'top-right',
            loaderBg: '#ff6849',
            icon: 'success',
            hideAfter: 3000,
            stack: 6
        });

        $('#total').text(total);

        line.forEach(function (value) {
            var ajaxCall = $.ajax({
                url: 'api.php?verify=true&lista=' + value,
                type: 'GET',
                beforeSend: function () {
                    $('#status').html('<span class="badge badge-success">Testando!</span>');
                },
                success: function(data) {

                    if (data.indexOf("Aprovada") >= 0) {
                        $("#aprovadas").append('<font style="color: #fff;">' + data + '</font>');
                        $('#status').html('<span class="badge badge-success">Aprovada!</span>');

                        audioLives.play();

                        $.toast({
                            text: '+1 Aprovada!',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 6
                        });

                        ap = ap + 1;
                        removelinha();
                    } else {
                        $("#reprovadas").append('<font style="color: #fff;">' + data + '</font>');
                        $('#status').html('<span class="badge badge-danger">Reprovada!</span>');
                        rp = rp + 1;
                        removelinha();
                    }

                    var fila = parseInt(ap) + parseInt(rp);
                    $('#lives').text(ap);
                    $('#dies').text(rp);
                    $('#testado').text(fila);

                    if (fila == total) {

                        $.toast({
                            text: 'Teste Finalizado!',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 6
                        });

                        $('#start').attr('disabled', false);
                        $('#stop').attr('disabled', true);
                        $('#status').html('<span class="badge badge-info">Teste Finalizado!</span>');
                    }
                }
            });

            $('#stop').click(function() {
                ajaxCall.abort();

                if (sd < 1) {
                    $.toast({
                        text: 'Teste Parado!',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 3000,
                        stack: 6
                    });
                }

                $('#start').attr('disabled', false);
                $('#stop').attr('disabled', true);
                $('#status').html('<span class="badge badge-danger">Teste Parado!</span>');
                sd = 1;
            });
        });

    });
});
function removelinha() {
    var lines = $("#list").val().split('\n');
    lines.splice(0, 1);
    $("#list").val(lines.join("\n"));
}