import { Controller } from "@hotwired/stimulus";
import Swal from 'sweetalert2';

export default class extends Controller {
  static values = {
    textTitle: String,
    textMessage: String,
  };

  messageDialog() {
    Swal.fire({
      title: this.textTitleValue,
      html: this.textMessageValue,
      confirmButtonColor: 'rgb(18, 71, 18)',
      showClass: {
        popup: 'animate__animated animate__backInDown'
      },
      hideClass: {
        popup: 'animate__animated animate__backOutDown'
      }
    })
  }
}
