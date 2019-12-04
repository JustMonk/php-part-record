<div id="side-navbar" class="z-depth-1">
   <ul style="margin: 0;">
      <li class="side-logo white-text" style="background: #546e7a ; margin-bottom: 30px">
         <h5 style="text-align: center; padding: 20px 0px; margin: 0">Управление</h5>
         
         <p style="text-align: center"><?php echo ("$_SESSION[name] $_SESSION[lastname]") ?></p>
         <p style="text-align: center">@<?php echo ("$_SESSION[login]") ?></p>
         <div style="display: flex; justify-content: center;">
            <a id="logout" style="width: 90%; height: 36px; padding: 0; margin: 10px 0px; text-align: center;" class="waves-effect waves-light btn grey lighten-4">Выход</a>
         </div>

      </li>
      <li><a class="nav-button" href="index.php"><i class="fas fa-home"></i>Главная</a></li>
      <li><p style="padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Операции:</p></li>
      <li><a class="nav-button" href="add.php"><i class="fas fa-plus fa-fw"></i>Приход</a></li>
      <li><p style="padding: 0px 30px; margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Разработка:</p></li>
      <li><a class="nav-button" href="product_list.php"><i class="fas fa-code"></i>Номенклатуры</a></li>
      <li><a class="nav-button" href="units_list.php"><i class="fas fa-code"></i>Таблица ед.изм.</a></li>
      <li><a class="nav-button" href="product_registry.php"><i class="fas fa-code"></i>Реестр продукции<span class="new badge red" data-badge-caption="проект"></span></a></li>
      <li><a class="nav-button" href="operation_history.php"><i class="fas fa-code"></i>История операций<span class="new badge red" data-badge-caption="проект"></span></a></li>
      <li><a class="nav-button" href="partners.php"><i class="fas fa-code"></i>Таблица контрагенты<span class="new badge red" data-badge-caption="проект"></span></a></li>
      <li><a class="nav-button" href="cross_key_test.php"><i class="fas fa-code"></i>Тестирование ключей<span class="new badge red" data-badge-caption="тест!"></span></a></li>
      <!--<li><a class="nav-button" data-select="rashod" href="#"><i class="fas fa-dollar-sign fa-fw"></i>Расход</a></li>
      <li><a class="nav-button" data-select="prodaji" href="#"><i class="fas fa-signal fa-fw"></i>Продажи</a></li>
      <li><a class="nav-button" data-select="statistika" href="#"><i class="fas fa-table fa-fw"></i>Статистика</a></li>
      <li><a class="nav-button" data-select="admin" href="#"><i class="fas fa-user-cog fa-fw"></i>Администрирование</a>-->
      </li>

   </ul>
</div>