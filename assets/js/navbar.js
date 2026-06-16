
(async function(){
    const placeholder = document.getElementById('site-navbar');
    if (!placeholder) return;
    try{
        const r = await fetch('/assets/navbar.html', { credentials: 'same-origin' });
        if (!r.ok) return;
        const html = await r.text();
        placeholder.innerHTML = html;
        await updateNavbarAuth();
    }catch(e){ console.warn('Navbar load failed', e); }
})();

async function updateNavbarAuth(){
    try{
        const res = await fetch('/backend/api/my_products.php', { credentials: 'same-origin' });
        if (!res.ok) return;
        const loginLink = document.getElementById('nav-login');
        const logoutLink = document.getElementById('nav-logout');
        if (loginLink && logoutLink){
            loginLink.style.display = 'none';
            logoutLink.style.display = 'inline-block';
            logoutLink.addEventListener('click', async (e)=>{
                e.preventDefault();
                await fetch('/backend/api/logout.php', { method: 'POST', credentials: 'same-origin' });
                window.location.href = '/paginas/login/login.html';
            });
        }
    }catch(e){ console.warn('Navbar auth check failed', e); }
}
