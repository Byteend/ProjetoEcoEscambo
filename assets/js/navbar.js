// Load the navbar fragment into #site-navbar
(async function(){
    const placeholder = document.getElementById('site-navbar');
    if (!placeholder) return;
    try{
        const r = await fetch('/assets/navbar.html', { credentials: 'same-origin' });
        if (!r.ok) return;
        const html = await r.text();
        placeholder.innerHTML = html;
    }catch(e){ console.warn('Navbar load failed', e); }
})();
