import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    insert(event) {
        const field = this.element.closest('.form-widget')?.querySelector('textarea, input[type="text"]');

        if (!field) {
            return;
        }

        const placeholder = event.currentTarget.dataset.placeholder;
        const start = field.selectionStart ?? field.value.length;
        const end = field.selectionEnd ?? field.value.length;

        field.value = field.value.slice(0, start) + placeholder + field.value.slice(end);

        const cursorPosition = start + placeholder.length;
        field.focus();
        field.setSelectionRange(cursorPosition, cursorPosition);

        field.dispatchEvent(new Event('input', { bubbles: true }));
    }
}
