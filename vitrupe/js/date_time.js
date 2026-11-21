function date_time(id) {
    const element = document.getElementById(id);
    if (!element) return;

    
    const loc = { lv: 'lv-LV', en: 'en-GB', fi: 'fi-FI' }[(document.documentElement.lang || 'lv').slice(0, 2)] || 'lv-LV';
    const formatter = new Intl.DateTimeFormat(loc, {
        weekday: 'long',  
        day: 'numeric',    
        month: 'long',    
        year: 'numeric',   
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });

    element.innerHTML = formatter.format(new Date());

    setTimeout(() => date_time(id), 1000);
}