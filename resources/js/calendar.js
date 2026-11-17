import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import jaLocale from '@fullcalendar/core/locales/ja';

document.addEventListener('DOMContentLoaded', () => {
    const calendarElement = document.getElementById('watering-calendar');

    if (!calendarElement) {
        return;
    }

    const calendar = new Calendar(calendarElement, {
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        locale: jaLocale,
        firstDay: 0,
        height: 'auto',
        events: calendarElement.dataset.eventsUrl,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
    });

    calendar.render();
});