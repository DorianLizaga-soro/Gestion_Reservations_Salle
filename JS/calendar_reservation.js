document.addEventListener('DOMContentLoaded', function () {
  createMiniCalendar('cal-mai', '2025-05-01');
  createMiniCalendar('cal-juin', '2025-06-01');
  createMiniCalendar('cal-juillet', '2025-07-01');
});

function createMiniCalendar(id, date) {
  const el = document.getElementById(id);

  const calendar = new FullCalendar.Calendar(el, {
    initialView: 'dayGridMonth',
    initialDate: date,
    locale: 'fr',

    firstDay: 1,

    eventDisplay: 'block',



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

    eventContent: function () {
    return { html: '<div class="event-bar"></div>' };
    },

    events: [
      {
        title: 'Tennis',
        start: '2025-06-02',
        className: 'event-blue'
      },
      {
        title: 'Les Arts',
        start: '2025-06-03',
        className: 'event-green'
      },
      {
        title: 'Retraités',
        start: '2025-06-04',
        className: 'event-orange'
      },
      {
        title: 'Parents',
        start: '2025-06-06',
        className: 'event-cyan'
      },
      {
        title : 'Chorale',
        start: '2025-06-07',
        className: 'event-yellow'
      },
      {
        title : 'Tennis',
        start: '2025-06-10',
        className: 'event-blue'
      },
      {
        title : 'Pétanque',
        start: '2025-06-12',
        className: 'event-purple'
      },
      {
        title : 'Retraités',
        start: '2025-06-14',
        className: 'event-orange'
      },
      {
        title : 'Les Arts',
        start: '2025-06-16',
        className: 'event-green'
      },
      {
        title : 'Parents',
        start: '2025-06-14',
        className: 'event-cyan'
      },
      {
        title : 'Informatique',
        start: '2025-06-19',
        className: 'event-red'
      },
      {
        title : 'Danse',
        start: '2025-06-05',
        className: 'event-pink'
      }

    ],


  

  dayCellDidMount: function(info) {


    const reservedDays = [
    '2025-06-02',
    '2025-06-03',
    '2025-06-04',
    '2025-06-05',
    '2025-06-06',
    '2025-06-07',
    '2025-06-10',
    '2025-06-12',
    '2025-06-14',
    '2025-06-16',
    '2025-06-19'
  ];


    const conflictDays = [
        '2025-06-14'
    ];

    const d = info.date;
      const date = d.getFullYear() + '-'
        + String(d.getMonth() + 1).padStart(2, '0') + '-'
        + String(d.getDate()).padStart(2, '0');
 
      if (reservedDays.includes(date)) {
        info.el.classList.add('reserved-day');
      }
 
      if (conflictDays.includes(date)) {
        info.el.classList.add('conflict-day');
      }
    }
 
  });

  calendar.render();
}