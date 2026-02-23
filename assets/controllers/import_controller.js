import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'label', 'spinner'];

    submit() {
        this.buttonTarget.disabled = true;
        this.labelTarget.classList.add('d-none');
        this.spinnerTarget.classList.remove('d-none');
    }

    connect() {
        document.addEventListener('turbo:fetch-request-error', this._handleError);
        document.addEventListener('turbo:frame-missing', this._handleError);
    }

    disconnect() {
        document.removeEventListener('turbo:fetch-request-error', this._handleError);
        document.removeEventListener('turbo:frame-missing', this._handleError);
    }

    _handleError = () => {
        this.buttonTarget.disabled = false;
        this.labelTarget.classList.remove('d-none');
        this.spinnerTarget.classList.add('d-none');
    };
}
