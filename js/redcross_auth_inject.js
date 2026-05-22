(async function(){
  try {
    const r=await fetch('php/check_auth.php');
    const d=await r.json();
    if(!d.authenticated||d.role!=='redcross'){window.location.href='login.html';}
  } catch { window.location.href='login.html'; }
})();
