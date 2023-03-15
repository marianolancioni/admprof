import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['messages', 'content'];
    static values = {
      url: String,
    };
   
    async refreshMessages(event) {
        //this.contentTarget.style.opacity = .3;
        const response = await fetch(this.urlValue);
        this.messagesTarget.innerHTML = await response.text();
        //this.contentTarget.style.opacity = 1;

        // Procesa aparición y ocultamiento mensajes tipo flash
        $(".flashes-messages").hide().delay(1000).fadeIn(150).delay(2000).slideUp(500);
        $(".flashes-messages-slow").hide().delay(1000).fadeIn(150).delay(10000).slideUp(1000);
        $(".flashes-messages-closable").hide().delay(1000).fadeIn(150);
    }
}