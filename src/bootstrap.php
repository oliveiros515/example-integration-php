<?php
require_once(__DIR__ . '/Helpers.php');

// Precisará alterar os domínios e as chaves de acesso.
$baseUrl = 'https://sandbox.api.memed.com.br/v1/sinapse-prescricao/usuarios';
$apiKey = 'iJGiB4kjDGOLeDFPWMG3no9VnN7Abpqe3w1jEFm6olkhkZD6oSfSmYCm';
$secretKey = 'Xe8M5GvBGCr4FStKfxXKisRo3SfYKI7KrTMkJpCAstzu2yXVN4av5nmL';

try {
    // Essa variavel "$attendance" representa o atendimento, estamos usando dados fictícios.
    // Aqui precisará buscar na base de dados ou consultar em algum webservice externo
    // Para isso precisará passar na url aguma informação que identifique o atendimento
    // e pegar com a variável super-global $_GET
   $attendance = [
       'paciente' => [
           'ddd'        => '11',
           'celular'    => '11956765676',
           'rg'         => '123433439',
           'nome'       => 'José da Silva',
           'cidade'     => 'São Paulo',
           'endereco'   => 'Rua  Jacarandá, 491  - Martim de Sá',
           'externalId' => 'codigo_ou_hash_id'
       ],
       'medico'   => [
           'dataNascimento' => '23/09/1992',
           'cpf'            => '83344388002',
           'email'          => 'example-integration@memed.com.br',
           'estado'         => 'SP',
           'cidade'         => 'São Paulo',
           'sexo'           => 'M',
           'nome'           => 'Nome do medico',
           'crm'            => '33221100',
           'especialidade'  => 62,
       ],
   ];

    // Primeiro eu tento pegar o médico na memed.
    // https://ajuda.memed.com.br/pt-BR/articles/2519460-recuperar-o-token-do-usuario
    $urlGetDoctor = $baseUrl . '/' . urlencode($attendance['medico']['crm'].$attendance['medico']['estado']) . '?api-key=' . $apiKey . '&secret-key=' . $secretKey;
    $user = execRequest($urlGetDoctor);

    // Se não retornar usuário, mandamos a request para criar
    if ($user === false) {
        // https://ajuda.memed.com.br/pt-BR/articles/2519457-cadastrar-um-usuario-medico
        $url = $baseUrl . '?api-key=' . $apiKey . '&secret-key=' . $secretKey;
        $user = createDoctor($attendance, $url);

        if ($user === false) {
            echo 'Não conseguimos cadastrar o médico. <br> Verifique as informações enviadas ou entre em contato pelo email: <b>suporte@memed.com.br</b>.';
            die();
        }
    }

    $token = $user['data']['attributes']['token'];
} catch (Exception $e) {
    echo 'Ocorreu um erro na aplicação: ' . $e->getMessage();
    die();
}
