// Essa variavel, representa um service http, com os header
var api = axios.create({
  baseURL: 'http://' + environment + 'api.memed.com.br/v1',
  headers: {
    'Accept': 'application/vnd.api+json',
    'Content-Type': 'application/json'
  }
});

/**
 *  Faz uma request para api da memed, capturando a prescrição gerada.
 */
function getPrescription (idPrescription) {
  var token = document.getElementById('token').value;

  api.get('/prescricoes/' + idPrescription + '?token=' + token)
    .then(function (response) {
      // todo: implementar função que grava a prescrição.
    });
}

/**
 * Inicia os eventos de escuta e mostra o front da prescrição
 */
function initialize () {
  MdSinapsePrescricao.event.add('core:moduleInit', function moduleInitHandler (module) {

    if (module.name === 'plataforma.prescricao') {
      // Verificar quais features são relevantes
      MdHub.command.send('plataforma.prescricao', 'setFeatureToggle', {
        deletePatient: false,
        newPrescription: false,
        optionsPrescription: false,
        removePatient: false,
      });

      // Define o pacientes
      MdHub.command.send('plataforma.prescricao', 'setPaciente', {
        nome: document.getElementById('namePatient').value,
        endereco: document.getElementById('enderecoPatient').value,
        cidade: document.getElementById('cidadePatient').value,
        telefone: document.getElementById('cellPhonePatient').value,
        idExterno: document.getElementById('externalId').value,
      }).then(function success() {
        // Opcionalmente podemos definir as alergias.
        // https://github.com/MemedDev/sinapse/blob/master/doc/prescricao.md#definindo-alergias
        MdHub.command.send('plataforma.prescricao', 'setAllergy', [174]);
      });

      // Apresenta o front da prescrição
      MdHub.module.show('plataforma.prescricao');

      MdHub.event.add('prescricaoSalva', function prescricaoSalvaCallback (idPrescription) {
        // Aqui é possível enviar esse ID para seu back-end obter mais informações
        getPrescription(idPrescription);
      });
    }
  });
}

initialize();
