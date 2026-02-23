import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'label', 'spinner'];

    submit() {
        this.buttonTarget.disabled = true;
        this.labelTarget.classList.add('d-none');
        this.spinnerTarget.classList.remove('d-none');
    }
}
