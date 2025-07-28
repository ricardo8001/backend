	var custo = "Custo desabilitado.";

	var nome = "Checker Full - Gateway Cielo";

	var programador = "@PladixOficial";

	var descricao_chk = "O checker é de exclusivo uso para clientes do @PladixOficial, não caia em golpe comprando com terceiros.";

	var audio = new Audio('live.mp3');










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



			function removelinha(){

				var lines = $("textarea").val().split('\n');

                lines.splice(0, 1);

                $("textarea").val(lines.join("\n"));

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

					$("#estatus").attr("class","badge badge-success").text("Teste finalizado!");

					toastr["success"]("O teste de "+total+" foi finalizado com sucesso.");

					$("#start").removeAttr('disabled');

					$("#chk-clean").removeAttr('disabled');

					$("#stop").attr("disabled","true");

					$("#chk-pause").attr("disabled","true");

					return false;

				}

				// conteudo que será testado

				var conteudo =  $("textarea").val().trim().split('\n')[0];



				$.ajax({

					url: 'amo.php?id=c4b44941b7e21ca78cd6f63a949d8bce',

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

						lives++

						$("#estatus").attr("class","badge badge-success").text(conteudo+" -> LIVE");

						toastr["success"]("LIVE "+conteudo);

						$("#lives").append(response+"<br>");

						audio.play();

						/// <span class="badge badge-danger">Reprovada</span>

					}else if (response.indexOf('<span class="badge badge-danger">Reprovada</span>') >= 0) {

						dies++

						$("#estatus").attr("class","badge badge-danger").text(conteudo+" -> DIE");

						toastr["error"]("Die "+conteudo);

						$("#dies").append(response+"<br>");

					}else{

						errors++;

						$("#estatus").attr("class","badge badge-warning").text(conteudo+" -> ERROR");

						toastr["warning"]("Error "+conteudo);

						$("#errors").append(response+"<br>");

					}



					// atualiza resultados

					$(".val-total").text(total);

					$(".val-lives").text(lives);

	                $(".val-dies").text(dies);

	                $(".val-errors").text(errors);

	                $(".val-tested").text(tested);

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

				var lista =  $("textarea").val().trim().split('\n');

				var total = lista.length;

				$(".val-total").text(total);

				stopped = false;

				paused = false;


            if (total > 1000) { 
					return toastr["warning"]("O máximo permitido é 1000.");
				}


				toastr["success"]("Checker Iniciado.");

				$("#estatus").attr("class","badge badge-success").text("Checker iniciado, aguarde...");



				// libera os botoes

				$("#stop").removeAttr('disabled');

				$("#chk-pause").removeAttr('disabled');

				$("#start").attr("disabled","true");

				$("#chk-clean").attr("disabled","true");



				// inicia o teste

				testar(tested,total);

			}

			$("#start").click(function() {

				if ($('textarea').val().trim() == "") {

					$('textarea').focus();

				}else{

					start();

				}

			});

			// ========== PAUSE ========== //

			function pause(){

				$("#start").removeAttr('disabled');

				$("#chk-pause").attr("disabled","true");

				paused = true;

				console.log('checker pausado');

				toastr["info"]("Checker Pausado!");

				$("#estatus").attr("class","badge badge-info").text("Checker pausado...");

			}

			$("#chk-pause").click(function() {

				pause();

			});

			// ========== STOP ========== //

			function stop() {

				stopped = true;

				$("#start").removeAttr('disabled');

				$("#chk-clean").removeAttr('disabled');

				$("#stop").attr("disabled","true");

				$("#chk-pause").attr("disabled","true");

				console.log('checker parado');

				toastr["info"]("Checker Parado!");

				$("#estatus").attr("class","badge badge-secondary").text("Checker parado...");

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

				$(".val-total").text(total);

				$(".val-lives").text(lives);

                $(".val-dies").text(dies);

                $(".val-errors").text(errors);

                $(".val-tested").text(tested);

                $("textarea").val("");



                toastr["info"]("Checker Limpo!");

			}

			$("#chk-clean").click(function() {

				clean();

			});



		});