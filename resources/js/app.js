

import Alpine from 'alpinejs';
import './calendar';

window.Alpine = Alpine;

Alpine.data('plantTheme', () => ({
    theme: document.documentElement.dataset.plantTheme === 'dark' ? 'dark' : 'light',

    toggleTheme() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.plantTheme = this.theme;

        try {
            localStorage.setItem('mizuyari.theme', this.theme);
        } catch {
            // The selected theme still applies when storage is unavailable.
        }
    },
}));

Alpine.data('wateringEntryForm', (initialMemo) => ({
    memo: initialMemo ?? '',

    resetEntry(form) {
        form.reset();
        this.memo = '';

        const formatter = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Tokyo',
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit', hourCycle: 'h23',
        });
        const parts = Object.fromEntries(formatter.formatToParts().map(({ type, value }) => [type, value]));
        const { year, month, day, hour, minute } = parts;
        this.$refs.wateredAt.value = year + '-' + month + '-' + day + 'T' + hour + ':' + minute;
    },
}));

Alpine.start();
