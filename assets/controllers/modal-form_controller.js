import { Controller } from "@hotwired/stimulus";
import { Modal } from "bootstrap";
import $ from "jquery";
//import { useDispatch } from 'stimulus-use'; //28-02-2022 No funciona useDispacth de Stimulus-Use con Stimulus 3.x (se deja el código documentado para cuando funcione)

export default class extends Controller {
  static targets = ["modal", "modalBody"];
  static values = {
    formUrl: String,
  };

  modal = null;

  connect() {
    //useDispatch(this, {debug:true}); //28-02-2022 No funciona useDispacth de Stimulus-Use con Stimulus 3.x (se deja el código documentado para cuando funcione)
  }

  async openModal() {
    this.modalBodyTarget.innerHTML = "Cargando...";
    this.modal = new Modal(this.modalTarget);
    this.modal.show();

    this.modalBodyTarget.innerHTML = await $.ajax(this.formUrlValue);

    // Agrega un * a todos los campos que son requeridos
    $("label.required").append("<sup><strong>&nbsp;*</strong></sup>");
    $("label.required").addClass("required");
    
    $('input[autofocus]').trigger('focus');
  }

  async submitForm(event) {
    event.preventDefault();

    const $form = $(this.modalBodyTarget).find("form");

    try {
      // Realiza efecto de opacidad sobre el Datatables mientras se impactan los datos
      $("#dt").css({ opacity: "0.5", filter: "alpha(opacity=50);" });

      await $.ajax({
        url: this.formUrlValue,
        method: $form.prop("method"),
        data: $form.serialize(),
      }).then((result) => {
        this.modal.hide();

        // Dispara un evento personalizado ("success") a nivel del DOM
        //this.dispatch('success'); //28-02-2022 No funciona useDispacth de Stimulus-Use con Stimulus 3.x (se deja el código documentado para cuando funcione)
        const e = new CustomEvent("success");
        window.dispatchEvent(e);

        // Recarga y restaura opacidad normal del Datatables
        $('#dt').DataTable().draw('page')
        $('#dt').css({ opacity: '1', filter: 'alpha(opacity=1);' });
      });
    } catch (e) {
      $("#dt").css({ opacity: "1", filter: "alpha(opacity=1);" });
      this.modalBodyTarget.innerHTML = e.responseText;
    }
  }

  modalHidden() {}
}
