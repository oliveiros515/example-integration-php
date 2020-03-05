// Essa variavel, representa um service http, com os header
var api = axios.create({
  // Alterar a URL do domínio da memed, quando for para produção
  baseURL: 'http://sandbox.api.memed.com.br/v1',
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
      // Aqui enviará para a base do Tasy.
      // Pode ser enviado para um webservice alguma aplicação web com direto de escrita.
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
        removePatient: false
      });

      // Define o pacientes
      MdHub.command.send('plataforma.prescricao', 'setPaciente', {
        nome: document.getElementById('namePatient').value,
        endereco: document.getElementById('enderecoPatient').value,
        cidade: document.getElementById('cidadePatient').value,
        telefone: document.getElementById('cellPhonePatient').value,
        idExterno: document.getElementById('externalId').value
      }).then(function success() {
        MdHub.module.show(module.name);
      });

      MdHub.event.add('prescricaoSalva', function prescricaoSalvaCallback (idPrescription) {
        // Aqui é possível enviar esse ID para seu back-end obter mais informações
        getPrescription(idPrescription);
      });
    }
  });
}

initialize();
