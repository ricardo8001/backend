<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AMAZON.COM | Checker AMAZON</title>
    <link rel="stylesheet" href="./assets/css/vendors_css.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/skin_color.css">
    <style type="text/css">
        ::-webkit-scrollbar {
            width: 2px;
            height: 2px;
        }
        ::-webkit-scrollbar-button {
            width: 0px;
            height: 0px;
        }
        ::-webkit-scrollbar-thumb {
            background: #71199a;
            border: 0px none #ffffff;
            border-radius: 50px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #013137;
        }
        ::-webkit-scrollbar-thumb:active {
            background: #000000;
        }
        ::-webkit-scrollbar-track {
            background: #666666;
            border: 0px none #ffffff;
            border-radius: 50px;
        }
        ::-webkit-scrollbar-track:hover {
            background: #666666;
        }
        ::-webkit-scrollbar-track:active {
            background: #333333;
        }
        ::-webkit-scrollbar-corner {
            background: transparent;
        }
    </style>
</head>
<body class="dark-skin theme-primary">
    <section class="content">
        <div class="row">
            <div class="col-md-9" style="margin: auto;">
                <div class="box">
                    <div class="box-body">
                        <h4 class="text-center text-white"><i class="fa fa-hashtag"></i> <b> CHECKER - AMAZON </b></h4>
                        <p class="text-center text-white"></p>
                        <hr>
                        <div class="form-group">
                            <div class="input-group">
                                <textarea id="cookies1" class="form-control text-center" style="resize: none;" rows="4" placeholder="Cole o COOKIE 1 aqui (Obrigatório)"></textarea>
                            </div>
                            <div class="input-group mt-2">
                                <textarea id="cookies2" class="form-control text-center" style="resize: none;" rows="4" placeholder="Cole o COOKIE 2 aqui (Opcional)"></textarea>
                            </div>
                            <button id="toggle-api" class="btn btn-info btn-block mt-2"><i class="fa fa-exchange"></i> API: USA</button>
                        </div>
                        <p class="text-white mb-4">
                            STATUS: <span id="status" class="float-right"><font class="badge badge-dark">Não Iniciado!</font></span>
                        </p>
                        <p class="text-white mb-4"></p>
                        <p class="text-white mb-4">
                            LIMITE: <font class="text-warning float-right">800 LINHAS</font>
                        </p>
                        <p class="text-white mb-4"></p>
                        <button id="start" class="btn btn-info float-left" style="width: 48%;"><i class="fa fa-play"></i> INICIAR</button>
                        <button id="stop" class="btn btn-info float-right" style="width: 48%;" disabled><i class="fa fa-stop"></i> PARAR</button>
                    </div>
                </div>
            </div>
            <div class="col-md-9" style="margin: auto;">
                <div class="card">
                    <ul class="nav nav-tabs" id="myTab" role="tablist" style="border: none;">
                        <li class="nav-item">
                            <a class="nav-link active text-white" style="border: none;" id="home-tab" data-toggle="tab" href="#tab-list" role="tab" aria-controls="tab-list" aria-selected="true"><i class="fa fa-cogs"></i> <b><span id="testado">0</span>/<span id="total">0</span></b></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" style="border: none;" id="profile-tab" data-toggle="tab" href="#tab-lives" role="tab" aria-controls="tab-lives" aria-selected="false"><i class="fa fa-thumbs-up fa-lg"></i> <b id="lives">0</b></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" style="border: none;" id="contact-tab" data-toggle="tab" href="#tab-dies" role="tab" aria-controls="tab-dies" aria-selected="false"><i class="fa fa-thumbs-down fa-lg"></i> <b id="dies">0</b></a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active px-3 pt-4 pb-3 text-center" id="tab-list" role="tabpanel" aria-labelledby="home-tab">
                            <div class="container-fluid p-0 mt-2">
                                <textarea id="list" rows="8" limite="800" class="form-control text-center" style="resize: none;" placeholder="Insira Sua Lista (formato: numero|mes|ano|cvv)"></textarea>
                            </div>
                        </div>
                        <div class="tab-pane fade show px-3 pt-4 pb-3" role="tabpanel" id="tab-lives" aria-labelledby="home-tab">
                            <div id="aprovadas" style="overflow:auto; max-height: 500px;"></div>
                        </div>
                        <div class="tab-pane fade show px-3 pt-4 pb-3" role="tabpanel" id="tab-dies" aria-labelledby="home-tab">
                            <div id="reprovadas" style="overflow:auto; max-height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="./assets/js/vendors.min.js"></script>
    <script src="./assets/vendors/icons/feather-icons/feather.min.js"></script>
    <script src="./assets/vendors/vendor_components/toastr/src/jquery.toast.js"></script>
    <script src="./assets/js/pages/checkers.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            const toggleApiBtn = $('#toggle-api');
            let currentApi = 'api1.php'; // Inicia com API 1 (USA)
            let stopped = false;

            // Alternar entre APIs ao clicar no botão
            toggleApiBtn.click(function() {
                if (currentApi === 'api1.php') {
                    currentApi = 'api2.php';
                    toggleApiBtn.html('<i class="fa fa-exchange"></i> API: Itália');
                } else {
                    currentApi = 'api1.php';
                    toggleApiBtn.html('<i class="fa fa-exchange"></i> API: USA');
                }
                console.log('API selecionada:', currentApi);
            });

            // Processar lista linha por linha ao clicar em INICIAR
            $('#start').click(function() {
                const cookies1 = $('#cookies1').val().trim();
                const cookies2 = $('#cookies2').val().trim();
                const lista = $('#list').val().trim();

                console.log('Botão Iniciar clicado');
                console.log('Cookie 1:', cookies1);
                console.log('Cookie 2:', cookies2);
                console.log('Lista:', lista);

                if (!lista) {
                    $.toast({
                        heading: 'Erro',
                        text: 'Por favor, insira uma lista de cartões.',
                        position: 'top-right',
                        icon: 'error'
                    });
                    console.log('Erro: Lista vazia');
                    return false;
                }
                if (!cookies1) {
                    $.toast({
                        heading: 'Erro',
                        text: 'Por favor, insira pelo menos o COOKIE 1.',
                        position: 'top-right',
                        icon: 'error'
                    });
                    console.log('Erro: Cookie 1 vazio');
                    return false;
                }

                let array = lista.split('\n').filter(line => line.trim() !== '');
                let total = array.length;

                if (total > 800) {
                    $.toast({
                        heading: 'Erro',
                        text: 'Limite de 800 linhas excedido!',
                        position: 'top-right',
                        icon: 'warning'
                    });
                    console.log('Erro: Limite de 800 linhas excedido');
                    return false;
                }

                // Validar formato da lista
                for (let i = 0; i < array.length; i++) {
                    const parts = array[i].split('|');
                    if (parts.length !== 4 || !/^\d{15,16}$/.test(parts[0]) || !/^\d{1,2}$/.test(parts[1]) || !/^\d{2,4}$/.test(parts[2]) || !/^\d{3,4}$/.test(parts[3])) {
                        $.toast({
                            heading: 'Erro',
                            text: `Formato inválido na linha ${i + 1}: ${array[i]}. Use o formato numero|mes|ano|cvv.`,
                            position: 'top-right',
                            icon: 'error'
                        });
                        console.log('Erro: Formato inválido na linha', i + 1, ':', array[i]);
                        return false;
                    }
                }

                $('#total').text(total);
                $('#testado').text(0);
                $('#lives').text(0);
                $('#dies').text(0);
                $('#start').prop('disabled', true);
                $('#stop').prop('disabled', false);
                $('#status').html('<font class="badge badge-dark">Processando...</font>');
                console.log('Iniciando processamento. Total de linhas:', total);

                let lives = 0, dies = 0, testadas = 0;

                function processLine(index) {
                    if (stopped || index >= total) {
                        $.toast({
                            heading: 'Sucesso',
                            text: 'Teste finalizado!',
                            position: 'top-right',
                            icon: 'success'
                        });
                        $('#status').html('<font class="badge badge-dark">Processamento concluído!</font>');
                        $('#start').prop('disabled', false);
                        $('#stop').prop('disabled', true);
                        console.log('Processamento concluído');
                        return;
                    }

                    let data = array[index];
                    let bin = data.split('|')[0].substring(0, 6);
                    console.log('Processando linha', index + 1, ':', data);

                    // Faz uma requisição AJAX para obter informações do BIN
                    $.ajax({
                        url: 'get_bin_info.php',
                        method: 'POST',
                        data: { bin: bin },
                        dataType: 'json',
                        success: function(response) {
                            let cardInfo = response.cardInfo || 'Informações do BIN não disponíveis';
                            if (response.error) {
                                console.log('Erro ao obter BIN:', response.message);
                                cardInfo = response.message;
                            }
                            console.log('Informações do BIN recebidas:', cardInfo);

                            // Preparar dados para enviar ao API
                            const formData = new FormData();
                            formData.append('lista', data);
                            if (cookies1) formData.append('cookies1', btoa(cookies1));
                            if (cookies2) formData.append('cookies2', btoa(cookies2));
                            formData.append('ativarCookies', 'false');

                            console.log('Enviando dados para', currentApi, ':', {
                                lista: data,
                                cookies1: cookies1 ? btoa(cookies1) : 'não enviado',
                                cookies2: cookies2 ? btoa(cookies2) : 'não enviado'
                            });

                            let callBack = $.ajax({
                                url: currentApi,
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                success: function(retorno) {
                                    console.log('Resposta da API:', retorno);
                                    let apiIdentifier = (currentApi === 'api1.php') ? 'usa' : 'ita';
                                    let formattedRetorno = JSON.stringify(retorno.dados || ['Erro', 'Resposta inválida', cardInfo])
                                        .replace('Aprovada', '<font color="green">Aprovada</font>')
                                        .replace('Authorised (00)', '<font color="green">Authorised (00)</font>')
                                        .replace(' | Retorno:', ' | ' + cardInfo + ' | Retorno:');
                                    if (retorno.erro === "false" && retorno.dados && retorno.dados[2].indexOf('Aprovada') >= 0) {
                                        $.toast({
                                            heading: 'Sucesso',
                                            text: '+1 Aprovada!',
                                            position: 'top-right',
                                            icon: 'success'
                                        });
                                        $('#aprovadas').append(formattedRetorno + ' ' + apiIdentifier + '<br>');
                                        lives++;
                                        $('#lives').text(lives);
                                    } else {
                                        $('#reprovadas').append(formattedRetorno + ' ' + apiIdentifier + '<br>');
                                        dies++;
                                        $('#dies').text(dies);
                                    }
                                    testadas++;
                                    $('#testado').text(testadas);
                                    removelinha();
                                    setTimeout(function() {
                                        processLine(index + 1);
                                    }, 2000); // Delay de 2 segundos
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.log('Erro na requisição para a API:', textStatus, errorThrown);
                                    $.toast({
                                        heading: 'Erro',
                                        text: 'Erro ao processar a linha!',
                                        position: 'top-right',
                                        icon: 'error'
                                    });
                                    $('#reprovadas').append(JSON.stringify([data, 'Erro na API: ' + textStatus, cardInfo]) + ' ' + (currentApi === 'api1.php' ? 'usa' : 'ita') + '<br>');
                                    testadas++;
                                    dies++;
                                    $('#testado').text(testadas);
                                    $('#dies').text(dies);
                                    removelinha();
                                    setTimeout(function() {
                                        processLine(index + 1);
                                    }, 2000); // Delay de 2 segundos
                                }
                            });

                            $('#stop').off('click').on('click', function() {
                                stopped = true;
                                $.toast({
                                    heading: 'Aviso',
                                    text: 'Teste parado!',
                                    position: 'top-right',
                                    icon: 'warning'
                                });
                                $('#status').html('<font class="badge badge-dark">Processamento interrompido!</font>');
                                $('#start').prop('disabled', false);
                                $('#stop').prop('disabled', true);
                                callBack.abort();
                                console.log('Processamento interrompido pelo usuário');
                            });
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('Erro na requisição para get_bin_info.php:', textStatus, errorThrown);
                            $.toast({
                                heading: 'Aviso',
                                text: 'Erro ao obter informações do BIN! Continuando com BIN desconhecido.',
                                position: 'top-right',
                                icon: 'warning'
                            });
                            let cardInfo = 'BIN desconhecido';

                            // Preparar dados para enviar ao API
                            const formData = new FormData();
                            formData.append('lista', data);
                            if (cookies1) formData.append('cookies1', btoa(cookies1));
                            if (cookies2) formData.append('cookies2', btoa(cookies2));
                            formData.append('ativarCookies', 'false');

                            console.log('Enviando dados para', currentApi, ':', {
                                lista: data,
                                cookies1: cookies1 ? btoa(cookies1) : 'não enviado',
                                cookies2: cookies2 ? btoa(cookies2) : 'não enviado'
                            });

                            let callBack = $.ajax({
                                url: currentApi,
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                success: function(retorno) {
                                    console.log('Resposta da API:', retorno);
                                    let apiIdentifier = (currentApi === 'api1.php') ? 'usa' : 'ita';
                                    let formattedRetorno = JSON.stringify(retorno.dados || ['Erro', 'Resposta inválida', cardInfo])
                                        .replace('Aprovada', '<font color="green">Aprovada</font>')
                                        .replace('Authorised (00)', '<font color="green">Authorised (00)</font>')
                                        .replace(' | Retorno:', ' | ' + cardInfo + ' | Retorno:');
                                    if (retorno.erro === "false" && retorno.dados && retorno.dados[2].indexOf('Aprovada') >= 0) {
                                        $.toast({
                                            heading: 'Sucesso',
                                            text: '+1 Aprovada!',
                                            position: 'top-right',
                                            icon: 'success'
                                        });
                                        $('#aprovadas').append(formattedRetorno + ' ' + apiIdentifier + '<br>');
                                        lives++;
                                        $('#lives').text(lives);
                                    } else {
                                        $('#reprovadas').append(formattedRetorno + ' ' + apiIdentifier + '<br>');
                                        dies++;
                                        $('#dies').text(dies);
                                    }
                                    testadas++;
                                    $('#testado').text(testadas);
                                    removelinha();
                                    setTimeout(function() {
                                        processLine(index + 1);
                                    }, 2000); // Delay de 2 segundos
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.log('Erro na requisição para a API:', textStatus, errorThrown);
                                    $.toast({
                                        heading: 'Erro',
                                        text: 'Erro ao processar a linha!',
                                        position: 'top-right',
                                        icon: 'error'
                                    });
                                    $('#reprovadas').append(JSON.stringify([data, 'Erro na API: ' + textStatus, cardInfo]) + ' ' + (currentApi === 'api1.php' ? 'usa' : 'ita') + '<br>');
                                    testadas++;
                                    dies++;
                                    $('#testado').text(testadas);
                                    $('#dies').text(dies);
                                    removelinha();
                                    setTimeout(function() {
                                        processLine(index + 1);
                                    }, 2000); // Delay de 2 segundos
                                }
                            });

                            $('#stop').off('click').on('click', function() {
                                stopped = true;
                                $.toast({
                                    heading: 'Aviso',
                                    text: 'Teste parado!',
                                    position: 'top-right',
                                    icon: 'warning'
                                });
                                $('#status').html('<font class="badge badge-dark">Processamento interrompido!</font>');
                                $('#start').prop('disabled', false);
                                $('#stop').prop('disabled', true);
                                callBack.abort();
                                console.log('Processamento interrompido pelo usuário');
                            });
                        }
                    });
                }

                function removelinha() {
                    let lines = $('#list').val().split('\n');
                    lines.splice(0, 1);
                    $('#list').val(lines.join('\n'));
                    console.log('Linha removida. Linhas restantes:', lines.length);
                }

                processLine(0);
            });
        });
    </script>
</body>
</html>