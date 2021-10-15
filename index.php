<?php
require_once(__DIR__. '/src/bootstrap.php');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title> Exemplo Integração</title>
  <link rel="stylesheet" href="./src/css/styles.css">
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body>

  <div id="loader" class="loader">
    <div class="dots">
      <div class="center"></div>
    </div>
  </div>

  <input type="hidden" id="namePatient" value="<?= $attendance['paciente']['nome'] ?>">
  <input type="hidden" id="enderecoPatient" value="<?= $attendance['paciente']['endereco'] ?>">
  <input type="hidden" id="cellPhonePatient" value="<?= $attendance['paciente']['celular'] ?>">
  <input type="hidden" id="dddPatient" value="<?= $attendance['paciente']['ddd'] ?>">
  <input type="hidden" id="cidadePatient" value="<?= $attendance['paciente']['cidade'] ?>">
  <input type="hidden" id="externalId" value="<?= $attendance['paciente']['externalId'] ?>">
  <input type="hidden" id="token" value="<?= $token ?>">

  <script type="text/javascript" src="./src/js/main.js"></script>
</body>
</html>
