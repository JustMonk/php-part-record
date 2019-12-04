document.addEventListener('click', e => {
   if (e.target.tagName != 'A' || e.target.id != 'logout') return;

   let data = {
      logout: true
   };

   //TODO: обработка ошибок (try/catch)
   fetch('include/logout.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(result => {
      console.log(result);
      return result.json();
   }).then(json => {
      console.log(json);
      if (json.response) {
         location.href = 'login.php';
      } else {
         console.log('logout not allowed');
      }
   });

});