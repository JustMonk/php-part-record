document.addEventListener('click', e => {
   if (e.target.tagName != 'A' || e.target.id != 'login-button') return;

   let loginInput = document.getElementById('login');
   let passwordInput = document.getElementById('password');

   let data = {
      username: loginInput.value,
      password: passwordInput.value
   };

   //TODO: обработка ошибок (try/catch)
   fetch('include/check_login.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(result => {
      console.log(result);
      return result.json();
   }).then(json => {
      console.log(json);
      if (json.success) {
         location.href = 'index.php';
      } else {
         loginInput.className = 'invalid';
         passwordInput.className = 'invalid';
      }
   });

});