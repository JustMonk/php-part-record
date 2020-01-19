<div id="side-navbar" class="z-depth-1">
   <ul style="margin: 0;">
      <li class="side-logo white-text" style="background: #546e7a ; margin-bottom: 30px">
         <div style="display: flex; align-items: center; justify-content: center;">
            <h4>Part Record</h4> <span style="font-size: 16px; background: #41b2f4; color: #fff; padding: 2px 5px; margin-left: 5px; border-radius: 3px; text-shadow: 1px 1px 1px black;">2.0</span>
         </div>

         <p style="text-align: center"><?php echo ("$_SESSION[name] $_SESSION[lastname]") ?></p>
         <p style="text-align: center">@<?php echo ("$_SESSION[login]") ?></p>

         <div style="display: flex; justify-content: center; flex-direction: column; padding: 10px;">
            <a id="user-settings-1" style="height: 36px; padding: 0; margin-bottom: 10px; text-align: center;" class="waves-effect waves-light btn grey lighten-4">Настройки</a>
            <a id="logout" style="height: 36px; padding: 0; margin-bottom: 10px; text-align: center;" class="waves-effect waves-light btn grey lighten-4">Выход</a>
         </div>

      </li>
      <li><a class="nav-button" href="index.php"><i class="fas fa-home"></i>Главная</a></li>
      <li>
         <p style="padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Операции:</p>
      </li>
      <li><a class="nav-button" href="add.php"><i class="fas fa-plus fa-fw"></i>Приход</a></li>
      <li><a class="nav-button" href="sell.php"><i class="fas fa-dollar-sign fa-fw"></i>Продажа</a></li>
      <li><a class="nav-button" href="make.php"><i class="fas fa-industry fa-fw"></i>Производство</a></li>
      <li>
         <p style="padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Переучет:</p>
      </li>
      <li><a class="nav-button" href="inventory.php"><i class="fas fa-dolly-flatbed"></i>Инвентаризация</a></li>
      <li>
         <p style="padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Детализация:</p>
      </li>
      <li><a class="nav-button" href="operation_history.php"><i class="fas fa-code"></i>История операций</a></li>
      <li>
         <p style="padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Движения продукции:</p>
      </li>
      <li><a class="nav-button" href="add_history.php"><i class="fas fa-code"></i>Приходы</a></li>
      <li><a class="nav-button" href="sell_history.php"><i class="fas fa-code"></i>Продажи</a></li>
      <li><a class="nav-button" href="inv_history.php"><i class="fas fa-code"></i>Инвентаризации</a></li>
      <!--<li><a class="nav-button" href="add_history.php"><i class="fas fa-code"></i>Производства<span class="new badge red lighten-2" data-badge-caption="TODO"></span></a></li>-->
      <li><a class="nav-button" href="product_registry.php"><i class="fas fa-code"></i>Реестр продукции</a></li>
      <li>
         <p style="padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Справочники:</p>
      </li>
      <li><a class="nav-button" href="product_list.php"><i class="fas fa-code"></i>Номенклатуры</a></li>
      <li><a class="nav-button" href="partners.php"><i class="fas fa-code"></i>Таблица контрагенты</a></li>
      <li><a class="nav-button" href="users.php"><i class="fas fa-code"></i>Пользователи</a></li>
      <li><a class="nav-button" href="units_list.php"><i class="fas fa-code"></i>Таблица ед.изм.</a></li>
      </li>

   </ul>
</div>

<script>
   //выделяем текущую вкладку
   let sidebarLinks = document.querySelector(`#side-navbar a[href="${window.location.pathname.slice(1)}"]`);
   sidebarLinks.parentElement.classList.add('current-tab');
</script>