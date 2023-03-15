import { Controller } from "@hotwired/stimulus";
import $ from "jquery";
import Swal from 'sweetalert2';

export default class extends Controller {
  static values = {
    formUrl: String,
    textConfirm: String,
    message: String,
    icon: String,
    confirmButtonText: String,
    method: String,
    token: String,
  };

  //TODO: refactorizar nombre de este método a dialogConfirm()
  deleteUndeleteConfirm() {
    Swal.fire({
      title: this.textConfirmValue,
      text: this.messageValue,
      icon: this.iconValue,
      showCancelButton: true,
      confirmButtonColor: 'rgb(18, 71, 18)',
      cancelButtonColor: '#5c636a',
      confirmButtonText: this.confirmButtonTextValue,
      cancelButtonText: 'Cancelar',
      focusCancel: true,
    }).then((result) => {
      if (result.isConfirmed) {
          // Realiza efecto de opacidad sobre el Datatables mientras se impacta la acción
          $('#dt').css({ opacity: '0.5', filter: 'alpha(opacity=50);' });
    
          $.ajax({
            url: this.formUrlValue,
            method: 'POST',
            data: {_method: this.methodValue, _token: this.tokenValue}
          }).then((result) => {
            // Dispara un evento personalizado ("success") a nivel del DOM
            const e = new CustomEvent("success");
            window.dispatchEvent(e);

            // Restaura opacidad normal una vez recargado el Datatables
            $('#dt').DataTable().draw('page')
            $('#dt').css({ opacity: '1', filter: 'alpha(opacity=1);' });

          // TODO: gestionar excepción en el supuesto que algo falle en la petición ajax
          });
      }
    })
  }
}
