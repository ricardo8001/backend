<?php
session_start();


error_reporting(0);
header_remove('X-Powered-By');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Accept: application/json');
header('Accept-Encoding: gzip');
date_default_timezone_set('America/Sao_Paulo');
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . '/client03/';
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($userAgent, 'Android') !== false || strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false || strpos($userAgent, 'iPod') !== false || strpos($userAgent, 'Tablet') !== false) {
    $android = "<br>";
} else {
    $android = "<br><br><br><br>";
}


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title id="title">China - Developer</title>
    <link rel="icon" href="images/logo-4.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://www.amcharts.com/lib/3/plugins/export/export.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Righteous" rel="stylesheet">
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="plugins/revolution/css/settings.css" rel="stylesheet" type="text/css">
    <link href="plugins/revolution/css/layers.css" rel="stylesheet" type="text/css">
    <link href="plugins/revolution/css/navigation.css" rel="stylesheet" type="text/css">
    <!-- External CSS libraries -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/vendors.css">
    <link rel="stylesheet" type="text/css" href="css/app-lite.css">
    <link rel="stylesheet" type="text/css" href="css/core/colors/palette-gradient.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style type="text/css">
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/main-bg.jpg') center center no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }

        .mb-2 {
            text-align: center;
            justify-content: center;
            color: #fff;
        }

        .altdate {
            margin-top: 30px;
        }

        h5 {
            font-weight: bold;
            font-size: 16px;
        }

        h4 {
            font-weight: bold;
            font-size: 16px;
        }

        .card .card-title {
            width: 50%;
        }
    </style>
</head>

<body class="vertical-layout" data-color="bg-gradient-x-purple-blue">
    <div class="page-wrapper">
        <div class="preloader"></div>
    </div>
    <div class="app-content content">
        <div class="content-wrapper">
            <!--<div class="content-wrapper-before mb-3"></div> -->
            <div class="content-body">
                <div class="mt-2"></div>
                <div class="row">
                    <!-- AREA 01 -->
                    <div class="col-md-8"><br><br><br><br>
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="mb-2"><strong style="color: #fff;">CHECKER AMAZON</strong></h4>
                                <textarea rows="5" style="resize: none; color: #fff;"
                                    class="form-control text-center form-checker mb-2"
                                    placeholder="Insira sua Lista aqui" title="Insira sua Lista aqui"></textarea>
                                <div class="input-group mb-1">
                                    <input type="text" style="text-align: center;" class="form-control" id="chavetoken"
                                        placeholder="COOKIE, TOKEN OU CHAVE">&nbsp;
                                </div>
                                <button class="btn btn-success btn-play text-white" style="width: 49%; float: left;"><i
                                        class="fa fa-play"></i> INICIAR</button>
                                <button class="btn btn-danger btn-stop text-white" style="width: 49%; float: right;"
                                    disabled><i class="fa fa-stop"></i> PARAR</button><br>
                            </div>
                        </div>
                    </div>
                    <!-- AREA 02 -->
                    <div class="col-md-4">
                        <?php echo $android; ?>
                        <div class="card mb-2">
                            <div class="card-body">
                                <h5>Aprovadas:<span class="badge badge-success float-right aprovadas">0</span></h5>
                                <hr>
                                <h5>Reprovadas:<span class="badge badge-danger float-right reprovadas">0</span></h5>
                                <hr>
                                <h5>Testadas:<span class="badge badge-info float-right testadas">0</span></h5>
                                <hr>
                                <h5>Carregadas:<span class="badge badge-primary float-right carregadas">0</span></h5>
                                <hr>
                            </div>
                        </div>
                    </div>
                    <!-- AREA 01 -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <button type="show" class="btn btn-primary btn-sm show-lives"><i
                                            class="fa fa-eye-slash"></i></button>
                                    <button class="btn btn-success btn-sm btn-copy"><i class="fa fa-copy"></i></button>
                                </div>
                                <h4 class="card-title mb-1"><i class="fa fa-check text-success"></i> Aprovadas</h4>
                                <div id='lista_aprovadas'></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <button type='show' class="btn btn-primary btn-sm show-dies"><i
                                            class="fa fa-eye"></i></button>
                                    <button class="btn btn-danger btn-sm btn-trash"><i class="fa fa-trash"></i></button>
                                </div>
                                <h4 class="card-title mb-1"><i class="fa fa-times text-danger"></i> Reprovadas</h4>
                                <div id='lista_reprovadas'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center" style="text-align: center; justify-content: center; margin-top: 35px;">
            <p style="text-align: center; justify-content: center; color: white; font-size: 17px; cursor: pointer;"
                id="link">
                <font color='yellow'>🌙 China</font>Developer
            </p>
        </div>
    </div>
    <div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
    <!-- External JS libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    <!-- js script -->
    <script src="js/jquery.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="plugins/revolution/js/jquery.themepunch.revolution.min.js"></script>
    <script src="plugins/revolution/js/jquery.themepunch.tools.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.actions.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.carousel.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.kenburn.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.layeranimation.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.migration.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.navigation.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.parallax.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.slideanims.min.js"></script>
    <script src="plugins/revolution/js/extensions/revolution.extension.video.min.js"></script>
    <script src="js/main-slider-script.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.fancybox.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/wow.js"></script>
    <script src="js/appear.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/swiper.min.js"></script>
    <script src="js/owl.js"></script>
    <script src="js/script.js"></script>
    <script type="text/javascript">
        link.addEventListener("click", function (event) {
            window.open('https://t.me/Suportechina', '_blank');
        });
        var iniciar = new Audio('audio/iniciar.mp3');
        var livesplay = new Audio('audio/blop.mp3');
        $('.show-lives').click(function () {
            var type = $('.show-lives').attr('type');
            $('#lista_aprovadas').slideToggle();
            if (type == 'show') {
                $('.show-lives').html('<i class="fa fa-eye"></i>');
                $('.show-lives').attr('type', 'hidden');
            } else {
                $('.show-lives').html('<i class="fa fa-eye-slash"></i>');
                $('.show-lives').attr('type', 'show');
            }
        });

        $('.show-dies').click(function () {
            var type = $('.show-dies').attr('type');
            $('#lista_reprovadas').slideToggle();
            if (type == 'show') {
                $('.show-dies').html('<i class="fa fa-eye"></i>');
                $('.show-dies').attr('type', 'hidden');
            } else {
                $('.show-dies').html('<i class="fa fa-eye-slash"></i>');
                $('.show-dies').attr('type', 'show');
            }
        });

        $('.btn-trash').click(function () {
            Swal.fire({ title: 'Lista de Reprovadas limpa!', icon: 'success', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000 });
            $('#lista_reprovadas').text('');
        });

        $('.btn-copy').click(function () {
            Swal.fire({ title: 'Lista de Aprovadas Copiada!', icon: 'success', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000 });
            var lista_lives = document.getElementById('lista_aprovadas').innerText;
            var textarea = document.createElement("textarea");
            textarea.value = lista_lives;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        });

        $('.btn-play').click(function () {
            iniciar.play();
            var lista = $('.form-checker').val().trim();
            var array = lista.split('\n');
            var lives = 0,
                dies = 0,
                testadas = 0,
                txt = '';
            if (!lista) {
                Swal.fire({
                    title: 'Erro: Lista Vazia!',
                    icon: 'error',
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timer: 3000
                });
                return false;
            } else {
                Swal.fire({
                    title: 'Teste Iniciado!',
                    icon: 'success',
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timer: 3000
                });

                var line = array.filter(function (value) {
                    if (value.trim() !== "") {
                        txt += value.trim() + '\n';
                        return value.trim();
                    }
                });

                var total = line.length;
                var token = $('#token').val();
                $('.form-checker').val(txt.trim());

                if (total > 2000) {
                    Swal.fire({
                        title: 'Limite de 200 Linhas Exedido!',
                        icon: 'warning',
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                        timer: 3000
                    });
                    return false;
                }

                $('.carregadas').text(total);
                $('.btn-play').attr('disabled', true);
                $('.btn-stop').attr('disabled', false);
                function processLine(index) {
                    if (index >= total) {
                        Swal.fire({
                            title: 'Teste Finalizado!',
                            icon: 'success',
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end',
                            timer: 3000
                        });
                        $('.btn-play').attr('disabled', false);
                        $('.btn-stop').attr('disabled', true);
                        return;
                    }

                    var data = line[index];
                    var callBack = $.ajax({
                        url: 'api.php',
                        type: "POST",
                        data: {
                            'lista': data,
                            'token': btoa(chavetoken.value),
                        },
                        success: function (retorno) {
                            console.log(retorno);
                            if (retorno.indexOf("Aprovada") >= 0) {
                                Swal.fire({
                                    title: '+1 Aprovada!',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end',
                                    timer: 3000
                                });
                                $('#lista_aprovadas').append(retorno + '<br>');
                                removelinha();
                                livesplay.play();
                                lives++;
                            } else {
                                $('#lista_reprovadas').append(retorno + '<br>');
                                dies++;
                                removelinha();
                            }
                            testadas = lives + dies;
                            $('.aprovadas').text(lives);
                            $('.reprovadas').text(dies);
                            $('.testadas').text(testadas);
                            setTimeout(function () {
                                processLine(index + 1);
                            }, 150);
                        }
                    });

                    $('.btn-stop').click(function () {
                        Swal.fire({
                            title: 'Teste Parado!',
                            icon: 'warning',
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end',
                            timer: 3000
                        });
                        $('.btn-play').attr('disabled', false);
                        $('.btn-stop').attr('disabled', true);
                        callBack.abort();
                        return false;
                    });
                } processLine(0);
            }
        });

        function removelinha() {
            var lines = $('.form-checker').val().split('\n');
            lines.splice(0, 1);
            $('.form-checker').val(lines.join("\n"));
        }
    </script>
</body>

</html>