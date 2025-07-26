<script type="text/javascript">
	var custo = "0";
	var descricao_chk = "Checker AMAZON US";
	var audio = new Audio('live.mp3');
</script>
<!DOCTYPE html>
<html>
<head>
	<title>Checker | AMAZON US</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!-- bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<!-- fontawesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
	<!-- toastr -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<style type="text/css">
		.nav-tabs{
			background-color:#181A1E;
			border-radius: 5px;
			border: 1px solid #20f000;
		}
		.nav-tabs li a{
			color: #fff;
		}
		.tab-content{
			background-color:#181A1E;
			color:#fff;
			padding:5px
			border-radius: 5px;
			border: 1px solid #20f000;
		}
		.nav-tabs > li > a{
			border: medium none;
		}
		.nav-tabs > li > a:hover{
			background-color: #181A1E !important;
			border: medium none;
			border-radius: 0;
			color:#fff;
			border-radius: 5px;
			border: 1px solid #20f000;
		}
		.active {
			background-color: #181A1E !important;
		}
		textarea{
			background: #0F1116;
			color: #fff;
			width: 100%;
			border: none;
			padding: 10px;
			resize: none;
			border: none;
			border-radius: 5px;
			border: 1px solid #20f000;
		}
		textarea:focus{
			box-shadow: 0 0 0 0;
			border: 0 none;
			outline: 0;
		}
		.cookie-input {
			background:#0F1116;
			color: #fff;
			border-radius: 5px;
			border: 1px solid #20f000;
			width: 100%;
			padding: 10px;
			resize: none;
		}
		.cookie-input:focus {
			box-shadow: 0 0 0 0;
			border: 0 none;
			outline: 0;
		}
		.cookie-submit-btn {
			background-color: #181A1E;
			border-radius: 5px;
			border: 1px solid #20f000;
		}
		.cookie-submit-btn:hover {
			background-color: #181A1E;
			border-radius: 5px;
			border: 1px solid #20f000;
		}
		
		button {
  padding: 10px 20px;
  background-color: #181A1E;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

button:hover {
  background-color: #181A1E;
  border-radius: 5px;
  border: 1px solid #20f000;
}
	</style>
</head>
<body class="p-3">
    
    <input type="hidden" value="<?php echo $base64Value; ?>" name="token_api" id="token_api">
    
	<div class="container p-0">
		<a href="../../dash" class="btn btn-dark shadow" style="background: #20f000;"><i class="fas fa-sign-out-alt"></i> Voltar</a>
	</div>
	<div class="container text-white rounded shadow p-3 my-4" style="background: #181A1E; border-radius: 5px; border: 1px solid #20f000;">
		<!-- descrição -->
		<div class="container-fluid">
			<h3><i class="fas fa-cogs"></i> AMAZON US</h3>
		</div>
		<!-- botoes de ação -->
		<div class="container-fluid mt-3">
			<div class="buttons">
				<button class="btn btn-dark" style="background: #20f000;" id="chk-start"><i class="fas fa-play"></i> Iniciar</button>
				<button class="btn btn-dark" style="background: #20f000;" id="chk-pause" disabled><i class="fas fa-pause"></i> Pausar</button>
				<button class="btn btn-dark" style="background: #20f000;" id="chk-stop" disabled><i class="fas fa-stop"></i> Parar</button>
				<button class="btn btn-dark" style="background: #20f000;" id="chk-clean"><i class="fas fa-trash-alt"></i> Limpar</button>
			</div>
		</div>
		<!-- status do checker -->
		<div class="container-fluid mt-3">
			<span class="badge badge-warning" id="estatus">Aguardando inicio...</span>
		</div>
	</div>

	<!-- tabs -->
	<div class="container p-0 shadow">
		<ul class="nav nav-tabs" id="myTab" role="tablist" style="border: none;">
			<li class="nav-item">
				<a class="nav-link active" style="border: none;" id="home-tab" data-toggle="tab" href="#chk-home" role="tab" aria-controls="home" aria-selected="true"><i class="far fa-credit-card" style="color: #fff;"></i></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" style="border: none;" id="profile-tab" data-toggle="tab" href="#chk-lives" role="tab" aria-controls="profile" aria-selected="false"><i class="fa fa-thumbs-up fa-lg" style="color: #fff;"></i></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" style="border: none;" id="contact-tab" data-toggle="tab" href="#chk-dies" role="tab" aria-controls="contact" aria-selected="false"><i class="fa fa-thumbs-down fa-lg" style="color: #fff;"></i></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" style="border: none;" id="contact-tab" data-toggle="tab" href="#chk-errors" role="tab" aria-controls="contact" aria-selected="false"><i class="fas fa-times fa-lg" style="color: #fff;"></i></a>
			</li>
		</ul>
		<div class="tab-content" id="myTabContent">
		
			<!-- HOME DO CHECKER -->
			<div class="tab-pane fade show active px-3 pt-4 pb-3" id="chk-home" role="tabpanel" aria-labelledby="home-tab">
				<div class="my-2">
					Aprovadas: <span class="val-lives" style="font-weight: bold;">0</span>
					Reprovadas: <span class="val-dies" style="font-weight: bold;">0</span>
					Errors: <span class="val-errors" style="font-weight: bold;">0</span>
					Testadas: <span class="val-tested" style="font-weight: bold;">0</span>
					Total: <span class="val-total" style="font-weight: bold;">0</span>
				</div>
				<div class="container-fluid p-0 mt-2 d-flex align-items-center">
					<input type="text" id="cookie-input-1" placeholder="Cookie 1 (cookie1): AMAZON.COM OU AUDIBLE.COM" class="cookie-input rounded shadow mr-2">
					<button class="btn btn-dark cookie-submit-btn" id="validate-cookie-1" style="background: #20f000;">Validar</button>
				</div>
				<div class="container-fluid p-0 mt-2 d-flex align-items-center">
					<input type="text" id="cookie-input-2" placeholder="Cookie 2 (cookie2): (Opcional)" class="cookie-input rounded shadow mr-2">
					<button class="btn btn-dark cookie-submit-btn" id="validate-cookie-2" style="background: #20f000;">Validar</button>
				</div>
				<div class="container-fluid p-0 mt-2 d-flex align-items-center">
					<input type="text" id="cookie-input-3" placeholder="Cookie 3 (cookie3): (Opcional)" class="cookie-input rounded shadow mr-2">
					<button class="btn btn-dark cookie-submit-btn" id="validate-cookie-3" style="background: #20f000;">Validar</button>
				</div>
				<div class="container-fluid p-0 mt-2 d-flex align-items-center">
					<input type="text" id="cookie-input-4" placeholder="Cookie 4 (cookie4): (Opcional)" class="cookie-input rounded shadow mr-2">
					<button class="btn btn-dark cookie-submit-btn" id="validate-cookie-4" style="background: #20f000;">Validar</button>
				</div>
				<div class="container-fluid p-0 mt-2">
					<textarea id="lista_cartoes" placeholder="Insira sua lista..." rows="10" cols="rounded shadow"></textarea>
				</div>
			</div>
			
			<script>
function apagarValoresLives() {
  var tabela = document.getElementById("lives");
  tabela.innerHTML = "";
}
</script>
			
			<!-- LIVES DO CHECKERS -->
			<div class="tab-pane fade show px-3 pt-4 pb-3" id="chk-lives" role="tabpanel" aria-labelledby="home-tab">
				<h5>Aprovadas</h5>
				<span>Total: <span class="val-lives">0</span></span>
				<br>
				<button class="btn btn-dark" style="background: #0d0e24;" id="copyButton"><i class="fas fa-copy"></i></button>
				<button class="btn btn-dark" style="background: #0d0e24;" onclick="apagarValoresLives()"><i class="fas fa-trash-alt"></i></button>
				<br>
				<div id="lives" style="overflow:auto;">
				</div>
			</div>
			
				<script>
        const copyButton = document.getElementById('copyButton');
        const livesDiv = document.getElementById('lives');

        copyButton.addEventListener('click', () => {
            const range = document.createRange();
            range.selectNode(livesDiv);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);

            try {
                const successful = document.execCommand('copy');
                const message = successful ? 'Copiado para a área de transferência!' : 'Não foi possível copiar.';
                console.log(message);
            } catch (err) {
                console.error('Erro ao copiar: ', err);
            }

            window.getSelection().removeAllRanges();
        });
    </script>
			
			<script>
function apagarValoresDies() {
  var tabela = document.getElementById("dies");
  tabela.innerHTML = "";
}
</script>

			<script>
function apagarValoresErrors() {
  var tabela = document.getElementById("errors");
  tabela.innerHTML = "";
}
</script>
			
			<!-- DIES DO CHECKER -->
			<div class="tab-pane fade fade show px-3 pt-4 pb-3" id="chk-dies" role="tabpanel" aria-labelledby="home-tab">
				<h5>Reprovadas</h5>
				<span>Total: <span class="val-dies">0</span></span>
				<br>
				<button class="btn btn-dark" style="background: #0d0e24;" onclick="apagarValoresDies()"><i class="fas fa-trash-alt"></i></button>
				<br>
				<div id="dies" style="overflow:auto;">
				</div>
			</div>
			<!-- ERRORS DO CHECKER -->
			<div class="tab-pane fade show px-3 pt-4 pb-3" id="chk-errors" role="tabpanel" aria-labelledby="home-tab">
				<h5>Erros</h5>
				<span>Total: <span class="val-errors">0</span></span>
				<br>
				<button class="btn btn-dark" style="background: #0d0e24;" onclick="apagarValoresErrors()"><i class="fas fa-trash-alt"></i></button>
				<br>
				<div id="errors" style="overflow:auto;">
				</div>
			</div>
			<!-- INFO DO CHECKER -->
		</div>	
	</div>
	<!-- jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<!-- bootstrap -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
	<!-- toastr -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	
<script type="text/javascript">
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
        var token_api = document.getElementById("token_api").value;

		function removelinha() {
			var lines = $("textarea").val().split('\n');
			lines.splice(0, 1);
			$("textarea").val(lines.join("\n"));
		}

		function testar(tested, total, lista, tentativas = 3) {
    if (stopped == true) {
        return false;
    }

    if (paused == true) {
        return false;
    }

    if (tested >= total) {
        console.log('finalizado ' + tested + " de " + total);
        $("#estatus").attr("class", "badge badge-success").text("Teste finalizado");
        toastr["success"]("Teste de " + total + " itens finalizado");
        $("#chk-start").removeAttr('disabled');
        $("#chk-clean").removeAttr('disabled');
        $("#chk-stop").attr("disabled", "true");
        $("#chk-pause").attr("disabled", "true");
        return false;
    }

    var conteudo = lista[tested];
    var token_api = document.getElementById("token_api").value;
    var cookie1 = $("#cookie-input-1").val().trim();
    var cookie2 = $("#cookie-input-2").val().trim();
    var cookie3 = $("#cookie-input-3").val().trim();
    var cookie4 = $("#cookie-input-4").val().trim();
    var cookies = [];
    if (cookie1) cookies.push(cookie1);
    if (cookie2) cookies.push(cookie2);
    if (cookie3) cookies.push(cookie3);
    if (cookie4) cookies.push(cookie4);

    if (cookies.length === 0) {
        $("#estatus").attr("class", "badge badge-danger").text("Nenhum cookie fornecido!");
        toastr["error"]("Insira pelo menos um cookie!");
        return false;
    }

    $.ajax({
        url: 'api.php',
        type: 'GET',
        data: { lista: conteudo, token_api: token_api, cookie1: cookie1, cookie2: cookie2, cookie3: cookie3, cookie4: cookie4, tries: tested % cookies.length },
    })
    .done(function(response) {
        if (response.indexOf("ERROR") >= 0) {
            retry();
        } else if (response.trim() === "") {
            retry();
        } else {
            handleResponse(response);
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        retry();
    });

    function retry() {
        if (tentativas > 0) {
            $("#estatus").attr("class", "badge badge-warning").text("Tentando novamente... (" + (4 - tentativas) + "/3)");
            toastr["warning"]("Tentando novamente para: " + conteudo);
            setTimeout(function() {
                testar(tested, total, lista, tentativas - 1);
            }, 1000);
        } else {
            handleRetryFailure();
        }
    }

    function handleResponse(response) {
        tested++;

        if (response.indexOf("Aprovada") >= 0) {
            lives++;
            $("#estatus").attr("class", "badge badge-success").text(conteudo + " -> LIVE");
            toastr["success"]("Aprovada! " + conteudo);
            $("#lives").append(response + "<br>");
            removelinha();
        } else if (response.indexOf("Reprovada") >= 0) {
            dies++;
            $("#estatus").attr("class", "badge badge-danger").text(conteudo + " -> DIE");
            toastr["error"]("Reprovada! " + conteudo);
            $("#dies").append(response + "<br>");
            removelinha();
        } else {
            errors++;
            $("#estatus").attr("class", "badge badge-warning").text(conteudo + " -> ERROR");
            toastr["warning"]("Ocorreu um erro! " + conteudo);
            $("#errors").append(response + "<br>");
        }

        $(".val-total").text(total);
        $(".val-lives").text(lives);
        $(".val-dies").text(dies);
        $(".val-errors").text(errors);
        $(".val-tested").text(tested);

        setTimeout(function() {
            testar(tested, total, lista);
        }, 1000);
    }

    function handleRetryFailure() {
        errors++;
        $("#estatus").attr("class", "badge badge-warning").text(conteudo + " -> ERROR");
        toastr["warning"]("Ocorreu um erro! " + conteudo);
        $("#errors").append('Erro na tentativa de teste: ' + conteudo + '<br>');
        
        $(".val-total").text(total);
        $(".val-lives").text(lives);
        $(".val-dies").text(dies);
        $(".val-errors").text(errors);
        $(".val-tested").text(tested);
        
        removelinha();
        testar(tested + 1, total, lista);
    }
}



		// ========== START ========== //
		function start() {
			var lista = $("textarea").val().trim().split('\n');
			var total = lista.length;

			$(".val-total").text(total);
			stopped = false;
			paused = false;
			toastr["success"]("Checker Iniciado.");
			$("#estatus").attr("class", "badge badge-success").text("Checker iniciado, aguarde...");

			// Libera os botões
			$("#chk-stop").removeAttr('disabled');
			$("#chk-pause").removeAttr('disabled');
			$("#chk-start").attr("disabled", "true");
			$("#chk-clean").attr("disabled", "true");

			// Inicia o teste
			testar(tested, total, lista);
		}


		$("#chk-start").click(function() {
			if ($('textarea').val().trim() == "") {
				$('textarea').focus();
			} else {
				start();
			}
		});

		// ========== PAUSE ========== //
		function pause() {
    $("#chk-start").removeAttr('disabled');
    $("#chk-pause").attr("disabled", "true");
    paused = true;
    console.log('checker pausado');
    toastr["info"]("Checker Pausado!");
    $("#estatus").attr("class", "badge badge-info").text("Checker pausado...");
}

		$("#chk-pause").click(function() {
			pause();
		});

		// ========== STOP ========== //
		function stop() {
			stopped = true;
			$("#chk-start").removeAttr('disabled');
			$("#chk-clean").removeAttr('disabled');
			$("#chk-stop").attr("disabled", "true");
			$("#chk-pause").attr("disabled", "true");
			console.log('checker parado');
			toastr["info"]("Checker Parado!");
			$("#estatus").attr("class", "badge badge-secondary").text("Checker parado...");
		}

		$("#chk-stop").click(function() {
			stop();
		});

		// ========== CLEAN ========== //
		function clean() {
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
			(".val-dies").text(dies);
			(".val-errors").text(errors);
			(".val-tested").text(tested);
			$("textarea").val("");
			$("#cookie-input-1").val("");
			$("#cookie-input-2").val("");
			$("#cookie-input-3").val("");
			$("#cookie-input-4").val("");
			toastr["info"]("Checker Limpo!");
		}

		$("#chk-clean").click(function() {
			clean();
		});

		// Validação dos cookies
		$("#validate-cookie-1").click(function() {
			var cookie = $("#cookie-input-1").val().trim();
			if (cookie) {
				$.ajax({
					url: 'api.php',
					type: 'GET',
					data: { validate_cookie: cookie, token_api: token_api },
					success: function(response) {
						if (response.includes("Cookie ativo")) {
							toastr["success"]("Cookie 1 (cookie1): Cookie ativo");
						} else {
							toastr["error"]("Cookie 1 (cookie1): Erro no cookie");
						}
					},
					error: function() {
						toastr["error"]("Cookie 1 (cookie1): Erro na validação");
					}
				});
			} else {
				toastr["warning"]("Insira um cookie para validar!");
			}
		});

		$("#validate-cookie-2").click(function() {
			var cookie = $("#cookie-input-2").val().trim();
			if (cookie) {
				$.ajax({
					url: 'api.php',
					type: 'GET',
					data: { validate_cookie: cookie, token_api: token_api },
					success: function(response) {
						if (response.includes("Cookie ativo")) {
							toastr["success"]("Cookie 2 (cookie2): Cookie ativo");
						} else {
							toastr["error"]("Cookie 2 (cookie2): Erro no cookie");
						}
					},
					error: function() {
						toastr["error"]("Cookie 2 (cookie2): Erro na validação");
					}
				});
			} else {
				toastr["warning"]("Insira um cookie para validar!");
			}
		});

		$("#validate-cookie-3").click(function() {
			var cookie = $("#cookie-input-3").val().trim();
			if (cookie) {
				$.ajax({
					url: 'api.php',
					type: 'GET',
					data: { validate_cookie: cookie, token_api: token_api },
					success: function(response) {
						if (response.includes("Cookie ativo")) {
							toastr["success"]("Cookie 3 (cookie3): Cookie ativo");
						} else {
							toastr["error"]("Cookie 3 (cookie3): Erro no cookie");
						}
					},
					error: function() {
						toastr["error"]("Cookie 3 (cookie3): Erro na validação");
					}
				});
			} else {
				toastr["warning"]("Insira um cookie para validar!");
			}
		});

		$("#validate-cookie-4").click(function() {
			var cookie = $("#cookie-input-4").val().trim();
			if (cookie) {
				$.ajax({
					url: 'api.php',
					type: 'GET',
					data: { validate_cookie: cookie, token_api: token_api },
					success: function(response) {
						if (response.includes("Cookie ativo")) {
							toastr["success"]("Cookie 4 (cookie4): Cookie ativo");
						} else {
							toastr["error"]("Cookie 4 (cookie4): Erro no cookie");
						}
					},
					error: function() {
						toastr["error"]("Cookie 4 (cookie4): Erro na validação");
					}
				});
			} else {
				toastr["warning"]("Insira um cookie para validar!");
			}
		});
	});
	
</script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    <script src="script.js"></script>

  <style>
    body {
      background: url('1/3.jpg') no-repeat center center fixed;
      background-size: cover;
    }
  </style>

</body>
</html>