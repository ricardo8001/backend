<?php
session_start();
error_reporting(0);

// Definir o fuso horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Caminho para o arquivo JSON de usuários
$users_file = 'users.json';

// Função para carregar usuários do arquivo JSON
function load_users() {
    global $users_file;
    if (file_exists($users_file)) {
        $json = file_get_contents($users_file);
        return json_decode($json, true) ?: [];
    }
    return [];
}

// Função para salvar usuários no arquivo JSON
function save_users($users) {
    global $users_file;
    file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
}

// Carregar usuários
$users = load_users();

// Credenciais do administrador
$admin_user = "admin";
$admin_password = "admin123";

// Verificar login
if (isset($_POST['usuario'], $_POST['senha'])) {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Verificar se é o administrador
    if ($usuario === $admin_user && $senha === $admin_password) {
        $_SESSION['logado'] = true;
        $_SESSION['is_admin'] = true;
        header("Location: index.php");
        exit;
    } 
    // Verificar se é um usuário comum
    elseif (isset($users[$usuario]) && $users[$usuario]['password'] === $senha) {
        if ($users[$usuario]['expiration'] > time()) {
            $_SESSION['logado'] = true;
            $_SESSION['is_admin'] = false;
            $_SESSION['username'] = $usuario;
            header("Location: index.php");
            exit;
        } else {
            $erro = "Conta expirada!";
        }
    } else {
        $erro = "Usuário ou senha inválidos.";
    }
}

// Processar logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Processar criação de novo usuário
if (isset($_POST['create_user'], $_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $new_user = $_POST['new_user'];
    $new_password = $_POST['new_password'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Converter horários para timestamp no fuso de Brasília
    $start_timestamp = strtotime($start_time . ' America/Sao_Paulo');
    $end_timestamp = strtotime($end_time . ' America/Sao_Paulo');

    // Validar horários
    if ($start_timestamp === false || $end_timestamp === false) {
        $admin_erro = "Formato de data/hora inválido!";
    } elseif ($end_timestamp <= time()) {
        $admin_erro = "Horário de expiração já passou!";
    } elseif ($end_timestamp <= $start_timestamp) {
        $admin_erro = "Horário de expiração deve ser posterior ao início!";
    } elseif (isset($users[$new_user])) {
        $admin_erro = "Usuário já existe!";
    } else {
        $users[$new_user] = [
            'password' => $new_password,
            'start_time' => date('Y-m-d H:i:s', $start_timestamp),
            'expiration' => $end_timestamp
        ];
        save_users($users);
        $admin_success = "Usuário $new_user criado com sucesso!";
    }
}

// Processar edição de usuário
if (isset($_POST['edit_user'], $_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $old_username = $_POST['old_username'];
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Converter horários para timestamp no fuso de Brasília
    $start_timestamp = strtotime($start_time . ' America/Sao_Paulo');
    $end_timestamp = strtotime($end_time . ' America/Sao_Paulo');

    // Validar horários
    if ($start_timestamp === false || $end_timestamp === false) {
        $admin_erro = "Formato de data/hora inválido!";
    } elseif ($end_timestamp <= time()) {
        $admin_erro = "Horário de expiração já passou!";
    } elseif ($end_timestamp <= $start_timestamp) {
        $admin_erro = "Horário de expiração deve ser posterior ao início!";
    } elseif (!isset($users[$old_username])) {
        $admin_erro = "Usuário não existe!";
    } elseif ($old_username !== $new_username && isset($users[$new_username])) {
        $admin_erro = "Novo nome de usuário já existe!";
    } else {
        // Atualizar usuário
        if ($old_username !== $new_username) {
            $users[$new_username] = $users[$old_username];
            unset($users[$old_username]);
        }
        $users[$new_username]['password'] = $new_password;
        $users[$new_username]['start_time'] = date('Y-m-d H:i:s', $start_timestamp);
        $users[$new_username]['expiration'] = $end_timestamp;
        save_users($users);
        $admin_success = "Usuário $new_username editado com sucesso!";
    }
}

// Processar exclusão de usuário
if (isset($_POST['delete_user'], $_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $delete_user = $_POST['delete_user'];
    if (isset($users[$delete_user])) {
        unset($users[$delete_user]);
        save_users($users);
        $admin_success = "Usuário $delete_user excluído com sucesso!";
    } else {
        $admin_erro = "Usuário não existe!";
    }
}

// Verificar expiração para usuário logado
if (isset($_SESSION['logado'], $_SESSION['username']) && !$_SESSION['is_admin']) {
    $username = $_SESSION['username'];
    if (isset($users[$username]) && $users[$username]['expiration'] <= time()) {
        session_destroy();
        header("Location: index.php?logout=1");
        exit;
    }
}

if (!isset($_SESSION['logado'])):
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CHECKER PREMIUM MEGGA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background: black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-size: cover;
        }
        .login-container {
            background-color: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
<div class="login-container text-center">
    <h3 class="mb-4">INSIRA SEUS DADOS</h3>
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="usuario" class="form-control mb-3" placeholder="Usuário" required />
        <input type="password" name="senha" class="form-control mb-3" placeholder="Senha" required />
        <button type="submit" class="btn btn-success btn-block">Entrar</button>
    </form>
</div>
</body>
</html>
<?php exit; endif; ?>

<?php if ($_SESSION['is_admin']): ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background: black;
            color: white;
            padding: 20px;
        }
        .admin-container {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 30px;
            border-radius: 10px;
            max-width: 800px;
            margin: auto;
        }
        table {
            color: white;
        }
        th, td {
            border: 1px solid #444;
            padding: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .modal-content {
            background-color: rgba(0, 0, 0, 0.9);
            color: white;
        }
        .modal-content .form-control {
            background-color: #333;
            color: white;
            border: 1px solid #444;
        }
        .modal-content .close {
            color: white;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <h3 class="mb-4 text-center">Painel de Administração</h3>
    <a href="index.php?logout=1" class="btn btn-danger mb-3">Sair</a>
    
    <?php if (isset($admin_erro)): ?>
        <div class="alert alert-danger"><?php echo $admin_erro; ?></div>
    <?php endif; ?>
    <?php if (isset($admin_success)): ?>
        <div class="alert alert-success"><?php echo $admin_success; ?></div>
    <?php endif; ?>

    <h4>Criar Novo Usuário</h4>
    <form method="POST">
        <div class="form-group">
            <input type="text" name="new_user" class="form-control mb-3" placeholder="Novo Usuário" required />
        </div>
        <div class="form-group">
            <input type="password" name="new_password" class="form-control mb-3" placeholder="Nova Senha" required />
        </div>
        <div class="form-group">
            <label>Início do Acesso (Horário de Brasília)</label>
            <input type="datetime-local" name="start_time" class="form-control mb-3" required />
        </div>
        <div class="form-group">
            <label>Fim do Acesso (Horário de Brasília)</label>
            <input type="datetime-local" name="end_time" class="form-control mb-3" required />
        </div>
        <button type="submit" name="create_user" class="btn btn-success btn-block">Criar Usuário</button>
    </form>

    <h4 class="mt-5">Usuários Cadastrados</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Início</th>
                <th>Expiração</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $username => $data): ?>
                <tr>
                    <td><?php echo htmlspecialchars($username); ?></td>
                    <td><?php echo htmlspecialchars($data['start_time']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s', $data['expiration']); ?></td>
                    <td><?php echo $data['expiration'] > time() ? 'Ativo' : 'Expirado'; ?></td>
                    <td class="action-buttons">
                        <button class="btn btn-warning btn-sm edit-btn" 
                                data-toggle="modal" 
                                data-target="#editUserModal"
                                data-username="<?php echo htmlspecialchars($username); ?>" 
                                data-password="<?php echo htmlspecialchars($data['password']); ?>" 
                                data-start="<?php echo htmlspecialchars($data['start_time']); ?>" 
                                data-end="<?php echo date('Y-m-d\TH:i', $data['expiration']); ?>">Renovar</button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir o usuário <?php echo htmlspecialchars($username); ?>?');">
                            <input type="hidden" name="delete_user" value="<?php echo htmlspecialchars($username); ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal para Edição de Usuário -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="edit-user-form">
                        <input type="hidden" name="old_username" id="edit_old_username" />
                        <div class="form-group">
                            <label>Usuário</label>
                            <input type="text" name="new_username" id="edit_username" class="form-control mb-3" required />
                        </div>
                        <div class="form-group">
                            <label>Senha</label>
                            <input type="password" name="new_password" id="edit_password" class="form-control mb-3" required />
                        </div>
                        <div class="form-group">
                            <label>Início do Acesso (Horário de Brasília)</label>
                            <input type="datetime-local" name="start_time" id="edit_start_time" class="form-control mb-3" required />
                        </div>
                        <div class="form-group">
                            <label>Fim do Acesso (Horário de Brasília)</label>
                            <input type="datetime-local" name="end_time" id="edit_end_time" class="form-control mb-3" required />
                        </div>
                        <button type="submit" name="edit_user" class="btn btn-warning btn-block">Salvar Alterações</button>
                        <button type="button" class="btn btn-secondary btn-block mt-2" data-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="./assets/vendors/icons/feather-icons/feather.min.js"></script>
<script>
    $(document).ready(function() {
        $('#editUserModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const username = button.data('username');
            const password = button.data('password');
            const start = button.data('start');
            const end = button.data('end');

            const modal = $(this);
            modal.find('#edit_old_username').val(username);
            modal.find('#edit_username').val(username);
            modal.find('#edit_password').val(password);
            modal.find('#edit_start_time').val(start.substring(0, 16));
            modal.find('#edit_end_time').val(end);
        });

        $('#editUserModal').on('hidden.bs.modal', function() {
            $(this).find('#edit_old_username').val('');
            $(this).find('#edit_username').val('');
            $(this).find('#edit_password').val('');
            $(this).find('#edit_start_time').val('');
            $(this).find('#edit_end_time').val('');
        });
    });
</script>
</body>
</html>
<?php exit; endif; ?>

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
        #countdown {
            color: #ff0000;
            font-size: 16px;
            margin-bottom: 20px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            border-radius: 5px;
        }
        #copy-all-approved {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        #copy-all-approved:hover {
            background-color: #138496;
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
                        <?php if (!$_SESSION['is_admin']): ?>
                            <div id="countdown">Tempo restante: <span id="timer"></span></div>
                        <?php endif; ?>
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
                        <div class="tab-pane fade show px-3 pt-4 pb-3" id="tab-lives" role="tabpanel" aria-labelledby="home-tab">
                            <button id="copy-all-approved" class="btn">Copiar Todas</button>
                            <div id="aprovadas" style="overflow:auto; max-height: 500px;"></div>
                        </div>
                        <div class="tab-pane fade show px-3 pt-4 pb-3" id="tab-dies" role="tabpanel" aria-labelledby="home-tab">
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
            // Inicializar Feather Icons
            feather.replace();

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

            // Função para copiar texto para a área de transferência
            function copyToClipboard(text) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        $.toast({
                            heading: 'Sucesso',
                            text: 'Texto copiado para a área de transferência!',
                            position: 'top-right',
                            icon: 'success'
                        });
                    }).catch(err => {
                        console.error('Erro ao copiar:', err);
                        $.toast({
                            heading: 'Erro',
                            text: 'Falha ao copiar o texto!',
                            position: 'top-right',
                            icon: 'error'
                        });
                    });
                } else {
                    // Fallback para navegadores mais antigos
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        document.execCommand('copy');
                        $.toast({
                            heading: 'Sucesso',
                            text: 'Texto copiado para a área de transferência!',
                            position: 'top-right',
                            icon: 'success'
                        });
                    } catch (err) {
                        console.error('Erro ao copiar:', err);
                        $.toast({
                            heading: 'Erro',
                            text: 'Falha ao copiar o texto!',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }
                    document.body.removeChild(textarea);
                }
            }

            // Copiar todas as aprovadas
            $('#copy-all-approved').click(function() {
                const approvedContent = $('#aprovadas').html();
                if (approvedContent.trim()) {
                    // Remover tags HTML e substituir <br> por \n
                    const cleanText = approvedContent
                        .replace(/<br\s*\/?>/gi, '\n')
                        .replace(/<font color="green">Aprovada<\/font>/gi, 'Aprovada')
                        .replace(/<font color="green">Authorised<\/font>/gi, 'Authorised')
                        .replace(/<[^>]+>/g, '')
                        .trim();
                    if (cleanText) {
                        copyToClipboard(cleanText);
                    } else {
                        $.toast({
                            heading: 'Aviso',
                            text: 'Nenhuma linha aprovada para copiar!',
                            position: 'top-right',
                            icon: 'warning'
                        });
                    }
                } else {
                    $.toast({
                        heading: 'Aviso',
                        text: 'Nenhuma linha aprovada para copiar!',
                        position: 'top-right',
                        icon: 'warning'
                    });
                }
            });

            // Cronômetro para tempo restante (apenas para usuários comuns)
            <?php if (!$_SESSION['is_admin'] && isset($_SESSION['username'], $users[$_SESSION['username']])): ?>
                const expirationTime = <?php echo $users[$_SESSION['username']]['expiration'] * 1000; ?>; // Em milissegundos
                function updateCountdown() {
                    const now = new Date().getTime();
                    const timeLeft = expirationTime - now;

                    if (timeLeft <= 0) {
                        $('#timer').text('Expirado');
                        // Redirecionar para logout
                        $.toast({
                            heading: 'Aviso',
                            text: 'Seu acesso expirou!',
                            position: 'top-right',
                            icon: 'warning'
                        });
                        setTimeout(() => {
                            window.location.href = 'index.php?logout=1';
                        }, 2000);
                        return;
                    }

                    const hours = Math.floor(timeLeft / (1000 * 60 * 60));
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                    $('#timer').text(`${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
                    setTimeout(updateCountdown, 1000);
                }
                updateCountdown();
            <?php endif; ?>

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

                function fetchBinInfo(bin, retries = 1) {
                    return new Promise((resolve) => {
                        $.ajax({
                            url: 'get_bin_info.php',
                            method: 'POST',
                            data: { bin: bin },
                            dataType: 'json',
                            timeout: 5000,
                            success: function(response) {
                                let cardInfo = response.cardInfo || 'Bandeira: DESCONHECIDA | Tipo: DESCONHECIDO | Nivel: DESCONHECIDO | Banco: DESCONHECIDO | Pais: DESCONHECIDO';
                                if (response.error) {
                                    console.log('Erro ao obter BIN:', response.message, 'BIN:', bin, 'Tentativas restantes:', retries);
                                    cardInfo = 'Bandeira: DESCONHECIDA | Tipo: DESCONHECIDO | Nivel: DESCONHECIDO | Banco: DESCONHECIDO | Pais: DESCONHECIDO';
                                }
                                console.log('Informações do BIN recebidas:', cardInfo);
                                resolve(cardInfo);
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log('Erro na requisição para get_bin_info.php:', textStatus, errorThrown, 'BIN:', bin, 'Tentativas restantes:', retries);
                                if (retries > 0) {
                                    console.log('Tentando novamente...');
                                    setTimeout(() => {
                                        fetchBinInfo(bin, retries - 1).then(resolve);
                                    }, 1000);
                                } else {
                                    $.toast({
                                        heading: 'Aviso',
                                        text: 'Erro ao obter informações do BIN! Continuando com BIN desconhecido.',
                                        position: 'top-right',
                                        icon: 'warning'
                                    });
                                    resolve('Bandeira: DESCONHECIDA | Tipo: DESCONHECIDO | Nivel: DESCONHECIDO | Banco: DESCONHECIDO | Pais: DESCONHECIDO');
                                }
                            }
                        });
                    });
                }

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
                    console.log('Processando linha', index + 1, ':', data, 'BIN:', bin);

                    fetchBinInfo(bin).then(cardInfo => {
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
                            dataType: 'text',
                            success: function(retorno) {
                                console.log('Resposta da API para linha', index + 1, ':', retorno);
                                let apiIdentifier = (currentApi === 'api1.php') ? 'usa' : 'ita';
                                let isAprovada = retorno.startsWith('Aprovada');
                                let formattedRetorno = retorno
                                    .replace('Aprovada', '<font color="green">Aprovada</font>')
                                    .replace('Authorised', '<font color="green">Authorised</font>')
                                    .replace('Reprovada', '<font color="red">Reprovada</font>');
                                
                                if (isAprovada) {
                                    $.toast({
                                        heading: 'Sucesso',
                                        text: '+1 Aprovada!',
                                        position: 'top-right',
                                        icon: 'success'
                                    });
                                    $('#aprovadas').append(formattedRetorno + '<br>');
                                    lives++;
                                    $('#lives').text(lives);
                                } else {
                                    $('#reprovadas').append(formattedRetorno + '<br>');
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
                                console.log('Erro na requisição para a API na linha', index + 1, ':', textStatus, errorThrown);
                                $.toast({
                                    heading: 'Erro',
                                    text: 'Erro ao processar a linha!',
                                    position: 'top-right',
                                    icon: 'error'
                                });
                                $('#reprovadas').append('<font color="red">Reprovada</font> ' + data + ' | Erro na API: ' + textStatus + ' | ' + cardInfo + ' ' + (currentApi === 'api1.php' ? 'usa' : 'ita') + '<br>');
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