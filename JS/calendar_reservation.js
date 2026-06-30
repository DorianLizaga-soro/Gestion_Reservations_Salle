document.addEventListener('DOMContentLoaded', function () {

  // Si tu as des événements, on prend le premier
  const nextEventDate = calendarEvents.length > 0
    ? new Date(calendarEvents[0].start)
    : new Date();

  const year = nextEventDate.getFullYear();
  const month = nextEventDate.getMonth() + 1;

  // Mois avant
  const prevMonth = month - 1;
  const prevYear = prevMonth < 1 ? year - 1 : year;
  const prevMonthValue = prevMonth < 1 ? 12 : prevMonth;

  createMiniCalendar('cal-mai', `${prevYear}-${String(prevMonthValue).padStart(2, '0')}-01`);

  // Mois de l'événement
  createMiniCalendar('cal-juin', `${year}-${String(month).padStart(2, '0')}-01`);

  // Mois suivant
  const nextMonth = month + 1;
  const nextYear = nextMonth > 12 ? year + 1 : year;
  const nextMonthValue = nextMonth > 12 ? 1 : nextMonth;

  createMiniCalendar('cal-juillet', `${nextYear}-${String(nextMonthValue).padStart(2, '0')}-01`);
});



function createMiniCalendar(id, date) {
  const el = document.getElementById(id);

  const calendar = new FullCalendar.Calendar(el, {
    initialView: 'dayGridMonth',
    initialDate: date,
    locale: 'fr',
    firstDay: 1,
    eventDisplay: 'block',   // 🔥 cache les événements
    height: 370,
    contentHeight: 330,
    expandRows: true,
    fixedWeekCount: true,
    showNonCurrentDates: true,

    headerToolbar: {
      left: '',
      center: 'title',
      right: ''
    },

    events: calendarEvents,

dayCellDidMount: function (info) {
  // Ne rien faire pour les jours hors-mois
  if (info.isOther) return;

  const d = info.date;
  const date = d.getFullYear() + '-' +
    String(d.getMonth() + 1).padStart(2, '0') + '-' +
    String(d.getDate()).padStart(2, '0');

  const frame = info.el.querySelector('.fc-daygrid-day-frame');

  // Trouver les événements du jour
  const eventsDuJour = calendarEvents.filter(ev =>
    ev.start.startsWith(date)
  );

  // Tooltip sur toute la case
  if (eventsDuJour.length > 0) {
    let tooltip = "";
    eventsDuJour.forEach(ev => {
      const association = ev.association || ev.title || "Association inconnue";
      const heure = new Date(ev.start).toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit'
      });
      tooltip += `${association} - ${heure}\n`;
    });
    frame.setAttribute("title", tooltip.trim());
  }

  // Bordures
  const hasRecurrent = eventsDuJour.some(ev => ev.type === "recurrente");
  const hasPonctuelle = eventsDuJour.some(ev => ev.type === "ponctuelle");

  if (hasRecurrent) frame.classList.add('border-recurrente');
  if (hasPonctuelle) frame.classList.add('border-ponctuelle');

  if (reservedDays.includes(date)) {
    frame.classList.add('reserved-day');
  }
},









  });

  calendar.render();
}
