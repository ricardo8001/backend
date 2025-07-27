$(document).ready(function() {



			// variaveis de informação

			var testadas = [];

			var total = 0;

			var tested = 0;

			var lives = 0;

			var dies = 0;

			var errors = 0;

			var stopped = true;

			var paused = true;

			var ap = 0, rp = 0, sd = 0;


			function removelinha(){

				var lines = $("#list").val().split('\n');

                lines.splice(0, 1);

                $("#list").val(lines.join("\n"));

			}



			



			function testar(tested,total){

				// verifica se nao está parado o checker

				if (stopped == true) {

					return false;

				}

				// verifica se nao está pausado o checker

				if (paused == true) {

					return false;

				}

				// verifica se ja terminou de testar

				if (tested >= total) {

					console.log('finalizado '+tested+" de "+total);

					$("#status").attr("class","badge badge-success").text("Teste finalizado!");

					$.toast({
                            text: 'Teste Finalizado!',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 6
                        });

					$("#start").removeAttr('disabled');

					$("#clear").removeAttr('disabled');

					$("#stop").attr("disabled","true");

					$("#pause").attr("disabled","true");

					return false;

				}

				// conteudo que será testado

				var conteudo =  $("#list").val().trim().split('\n')[0];



				$.ajax({

					url: 'api.php?id=c4b44941b7e21ca78cd6f63a949d8bce&checker=allbins',

					type: 'GET',

					data: {lista: conteudo},

				})

				.done(function(response) {



					// verifica se nao está parado o checker

					if (stopped == true) {

						return false;

					}

					// verifica se nao está pausado o checker

					if (paused == true) {

						return false;

					}



					tested++;



					// verifica o retorno

					// <span class="badge badge-success">Aprovada</span>

					if (response.indexOf('<span class="badge badge-success">Aprovada</span>') >= 0) {

						//lives++

						$("#status").attr("class","badge badge-success").text(conteudo+" -> Transação autorizada");

						$.toast({
                            text: 'Transação autorizada -> ' +conteudo,
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 6
                        });

						$("#aprovadas").append(response+"<br>");

						ap = ap + 1;
						audio.play();

						/// <span class="badge badge-danger">Reprovada</span>

					}else if (response.indexOf('<span class="badge badge-danger">Reprovada</span>') >= 0) {

						dies++

						$("#status").attr("class","badge badge-danger").text(conteudo+" -> Cartão inválido");

						$.toast({
                            text: 'Cartão inválido -> ' +conteudo,
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 3000,
                            stack: 6
                        });

                        rp = rp + 1;

						$("#reprovadas").append(response+"<br>");

					}else{

						errors++;

						$("#status").attr("class","badge badge-warning").text(conteudo+" -> ERROR");

						$.toast({
                            text: 'Ocorreu um erro -> ' +conteudo,
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 3000,
                            stack: 6
                        });

						$("#errors").append(response+"<br>");

					}



					// atualiza resultados

					$("#total").text(total);

					$("#lives").text(lives);

	                $("#dies").text(dies);

	                $("#erros").text(errors);

	                $("#testado").text(tested);

	                // remove linha

	                removelinha();



					console.log(response);



					// executa a função novamente

					testar(tested,total);

				})

				.fail(function() {

					return false;

				})

			}





			// ========== START ========== //

			function start() {

				var lista =  $("#list").val().trim().split('\n');

				var total = lista.length;

				$("#total").text(total);

				stopped = false;

				paused = false;


            // if (total > 1000) { 
			// 		return $.toast({
            //                 text: 'O limite de linhas é 1000, por favor coloque menos cartão.',
            //                 position: 'top-right',
            //                 loaderBg: '#ff6849',
            //                 icon: 'warning',
            //                 hideAfter: 3000,
            //                 stack: 6
            //             });
			// 	}


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

				$("#status").attr("class","badge badge-success").text("O seu teste foi iniciado com sucesso.");



				// libera os botoes

				$("#stop").removeAttr('disabled');

				$("#pause").removeAttr('disabled');

				$("#start").attr("disabled","true");

				$("#clear").attr("disabled","true");



				// inicia o teste

				testar(tested,total);

			}

			$("#start").click(function() {

				if ($('#list').val().trim() == "") {

					$('#list').focus();

				}else{

					start();

				}

			});

			// ========== PAUSE ========== //

			function pause(){

				$("#start").removeAttr('disabled');

				$("#pause").attr("disabled","true");

				paused = true;

				console.log('checker pausado');

				$.toast({
                            text: 'O checker foi pausado, aguarde para iniciar novamente.',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 3000,
                            stack: 6
                        });

				$("#status").attr("class","badge badge-info").text("Checker pausado...");

			}

			$("#pause").click(function() {

				pause();

			});

			// ========== STOP ========== //

			function stop() {

				stopped = true;

				$("#start").removeAttr('disabled');

				$("#clear").removeAttr('disabled');

				$("#stop").attr("disabled","true");

				$("#pause").attr("disabled","true");

				console.log('checker parado');

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

				$("#status").attr("class","badge badge-secondary").text("Checker parado...");

			}

			$("#stop").click(function() {

				stop();

			});

			// ========== CLEAN ========== //

			function clean(){

				testadas = [];

				total = 0;

				tested = 0;

				lives = 0;

				dies = 0;

				errors = 0;

				stopped = true;

				// atualiza resultados

				$("#total").text(total);

				$("#lives").text(lives);

                $("#dies").text(dies);

                $("#erros").text(errors);

                $("#testado").text(tested);

                $("#list").val("");



                ajaxCall.abort();

                if (sd < 1) {
                    $.toast({
                        text: 'A lista foi limpada!',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 3000,
                        stack: 6
                    });
                }

			}

			$("#clear").click(function() {

				clean();

			});



		});