import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    static targets = ['countdown'];

    async initialize() {
        this.component = await getComponent(this.element);

        window.addEventListener('countdownUpdate', () => {
            this.startCountdown();
        });
        window.addEventListener('removeCountdown', () =>{
            this.removeCountdown();
        })
        this.countdownTarget.addEventListener('click',()=>{
            this.toggleCountdown();
        })
        this.audio = new Audio('../media/bell.mp3');

    }

    connect() {
        this.startCountdown(); // lancer au chargement

    }

    startCountdown() {
        // Si un timer existe déjà → on le stoppe
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
        this.countdownTarget.classList.remove('hidden');

        // 60 secondes
        this.remainingSeconds = 60;
        this.updateDisplay();

        this.resumeCountdown();
    }

    updateDisplay() {
        const minutes = Math.floor(this.remainingSeconds / 60);
        const seconds = this.remainingSeconds % 60;

        this.countdownTarget.textContent =
            `${this.#pad(minutes)}:${this.#pad(seconds)}`;
    }

    #pad(n) {
        return n < 10 ? '0' + n : n;
    }

    stopCountdown(){
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
        this.countdownTarget.ariaLabel = "Relancer le chronomètre";
        this.applyPauseStyle();
    }
    resumeCountdown()
    {
        this.timerInterval = setInterval(() => {
            this.remainingSeconds--;

            if (this.remainingSeconds <= 0) {
                clearInterval(this.timerInterval);
                this.remainingSeconds = 0;
                this.updateDisplay();

                // prévenir le composant Live
                this.audio.play();
                this.component.emit('countdownASEnded');
            } else {
                this.updateDisplay();
            }
        }, 1000);
        this.countdownTarget.ariaLabel = "Mettre le chronomètre en pause";
        this.applyRunningStyle()
    }

    removeCountdown() {
        // arrêter le timer
        this.stopCountdown();

        // cacher l'élément
        this.countdownTarget.classList.add('hidden');
    }
    toggleCountdown(){
        if (this.timerInterval) {
            this.stopCountdown();
        }else{
            this.resumeCountdown();
        }
    }



    /* ---------- 🎨 Styles Tailwind ---------- */

    applyPauseStyle() {
        this.countdownTarget.classList.add(
            'bg-gray-200',
            'opacity-60',
            'border-gray-400',
            'text-gray-500'
        );

        this.countdownTarget.classList.remove(
            'border-primary',
            'text-primary'
        );
    }

    applyRunningStyle() {
        this.countdownTarget.classList.remove(
            'bg-gray-200',
            'opacity-60',
            'border-gray-400',
            'text-gray-500'
        );

        this.countdownTarget.classList.add(
            'border-primary',
            'text-primary'
        );
    }
}
