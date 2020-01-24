<?php
$access_query = $mysqli->query("SELECT title FROM account_types
WHERE ID = (SELECT access_id FROM users WHERE login = '$_SESSION[login]')
LIMIT 1");
$access = ($access_query->fetch_assoc())['title']; //admin or user
?>

<?php $link_path = 'http://' . $_SERVER['HTTP_HOST']; ?>
<div id="mobile-nav">
   <a id="openSideMenu"><i class="fas fa-bars"></i></a>
   <div style='display: flex; align-items: center; justify-content: center; margin-left: 1.5rem; color: #fff;'>
      <h4>Part Record</h4> <span style='font-size: 16px; background: #41b2f4; color: #fff; padding: 2px 5px; margin-left: 5px; border-radius: 3px; text-shadow: 1px 1px 1px black;'>2.1</span>
   </div>
</div>

<div id="side-navbar" class="z-depth-1">
   <ul style="margin: 0;">
      <li class="side-logo white-text" style="background: #546e7a ; margin-bottom: 30px">

         <?php
         echo "
         <div style='display: flex; align-items: center; justify-content: center;'>
            <h4>Part Record</h4> <span style='font-size: 16px; background: #41b2f4; color: #fff; padding: 2px 5px; margin-left: 5px; border-radius: 3px; text-shadow: 1px 1px 1px black;'>2.1</span>
         </div>

         <p style='text-align: center'>$_SESSION[name] $_SESSION[lastname]</p>
         <p style='text-align: center'>@$_SESSION[login]</p>

         <div style='display: flex; justify-content: center; flex-direction: column; padding: 10px;'>
            <a id='userSettings' href='$link_path/settings/user_settings.php' style='height: 36px; padding: 0; margin-bottom: 10px; text-align: center;' class='waves-effect waves-light btn grey lighten-4 header-button'>Настройки</a>
            <a id='logout' style='height: 36px; padding: 0; margin-bottom: 10px; text-align: center;' class='waves-effect waves-light btn grey lighten-4 header-button'>Выход</a>
         </div>

      </li>
      <li><a class='nav-button' href='$link_path/index.php'><i class='fas fa-home'></i>Главная</a></li>
      <li>
         <p style='padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;'>Операции:</p>
      </li>
      <li><a class='nav-button' href='$link_path/add.php'><i class='fas fa-plus fa-fw'></i>Приход</a></li>
      <li><a class='nav-button' href='$link_path/sell.php'><i class='fas fa-dollar-sign fa-fw'></i>Продажа</a></li>
      <li><a class='nav-button' href='$link_path/make.php'><i class='fas fa-industry fa-fw'></i>Производство</a></li>
      <li>
         <p style='padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;'>Переучет:</p>
      </li>
      <li><a class='nav-button' href='$link_path/inventory.php''><i class=' fas fa-dolly-flatbed'></i>Инвентаризация</a></li>
      ";
         ?>

         <?php
         if ($access == 'admin') {
            echo "
         <li>
            <p style='padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;'>Детализация:</p>
         </li>
         <li><a class='nav-button' href='$link_path/operation_history.php'><i class='fas fa-code'></i>История операций</a></li>
         <li>
            <p style='padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;'>Движения продукции:</p>
         </li>
         <li><a class='nav-button' href='$link_path/add_history.php'><i class='fas fa-code'></i>Приходы</a></li>
         <li><a class='nav-button' href='$link_path/sell_history.php'><i class='fas fa-code'></i>Продажи</a></li>
         <li><a class='nav-button' href='$link_path/inv_history.php'><i class='fas fa-code'></i>Инвентаризации</a></li>
         <li><a class='nav-button' href='$link_path/product_registry.php'><i class='fas fa-code'></i>Реестр продукции</a></li>
         <li>
            <p style='padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;'>Справочники:</p>
         </li>
         <li><a class='nav-button' href='$link_path/product_list.php'><i class='fas fa-code'></i>Номенклатуры</a></li>
         <li><a class='nav-button' href='$link_path/partners.php'><i class='fas fa-code'></i>Таблица контрагенты</a></li>
         <li><a class='nav-button' href='$link_path/users.php'><i class='fas fa-code'></i>Пользователи</a></li>
         <li><a class='nav-button' href='$link_path/units_list.php'><i class='fas fa-code'></i>Таблица ед.изм.</a></li>
         </li>
         ";
         }
         ?>

   </ul>
</div>

<script>
   //выделяем текущую вкладку
   let sidebarLinks = document.querySelector(`#side-navbar a[href="${window.location.origin + '/' + window.location.pathname.slice(1)}"]:not(.header-button)`);
   if (sidebarLinks) sidebarLinks.parentElement.classList.add('current-tab');

   document.addEventListener('click', e => {
      if(e.target.id == 'openSideMenu' || e.target.parentElement.id == 'openSideMenu') {
         //выкатываем сайдменю
         let sidemenu = document.getElementById('side-navbar');
         sidemenu.classList.add('menu-open');

         //создаем и добавляем оверлей
         let overlay = document.createElement('div');
         overlay.id = 'body-overlay';
         overlay.style.cssText = 'background: rgba(150, 150, 150, 0.5); position: fixed; width: 100%; height: 100%; top: 0; z-index: 999;';
         overlay.onclick = () => {
            overlay.remove();
            sidemenu.classList.remove('menu-open');
            document.body.classList.remove('modal-overlay')
         };
         document.body.append(overlay);
      }
   });
</script>