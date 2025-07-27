<?php

error_reporting(0);
$saldo = 9999;

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="https://telegra.ph/file/daff0a64a1c6dbf019a4c.jpg">
    <title>PladixCentral | Checker Validador Itaú</title>
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
                        <h4 class="text-center text-white"><i class="fa fa-hashtag"></i> <b> CHECKER - STRIPE </b></h4>
                        <p class="text-center text-white">
                            <i class="fa fa-code"></i> <a href="https://t.me/pladixoficial"> <font class="text-center text-white"> @perryzin <i class="fa fa-code"></i></font></a>
                        </p>
                        <hr>
                        <p class="text-white mb-4">
                            STATUS: <span id="status" class="float-right"><font class="badge badge-dark">Não Iniciado!</font></span>
                        </p>
                        <p class="text-white mb-4">
                            CUSTO: <font class="text-warning float-right">1 CRÉDITO POR LIVE</font>
                        </p>
                        <p class="text-white mb-4">
                            LIMITE: <font class="text-warning float-right">800 LINHAS</font>
                        </p>
                        <p class="text-white mb-4">
                            CRÉDITOS: <font class="text-warning float-right"><?php echo $saldo ?></font>
                        </p>
                        <button id="start" class="btn btn-info float-left" style="width: 48%;"><i class="fa fa-play"></i> INICIAR</button>
                        <button id="stop" class="btn btn-info float-right" style="width: 48%;" disabled=""><i class="fa fa-stop"></i> PARAR</button>
                    </div>
                </div>
            </div>

            <div class="col-md-9" style="margin: auto;">
                <div class="card ">
                    <ul class="nav nav-tabs" id="myTab" role="tablist" style="border: none;">
                        <li class="nav-item">
                            <a class="nav-link active text-white" style="border: none;" id="home-tab" data-toggle="tab" href="#tab-list" role="tab" aria-controls="tab-list" aria-selected="true"><i class="fa fa-cogs "></i> <b><span id="testado">0</span>/<span id="total">0</span></b></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" style="border: none;" id="profile-tab" data-toggle="tab" href="#tab-lives" role="tab" aria-controls="tab-lives" aria-selected="false"><i class="fa fa-thumbs-up fa-lg "></i> <b id="lives">0</b></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" style="border: none;" id="contact-tab" data-toggle="tab" href="#tab-dies" role="tab" aria-controls="tab-dies" aria-selected="false"><i class="fa fa-thumbs-down fa-lg "></i> <b id="dies">0</b></a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active px-3 pt-4 pb-3 text-center" id="tab-list" role="tabpanel" aria-labelledby="home-tab">
                            <div class="container-fluid p-0 mt-2">
                                <textarea id="list" rows="8"  id="list" limite="800" class="form-control text-center" style="resize: none;" placeholder="Insira Sua Lista!"></textarea>
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
</body>
</html>