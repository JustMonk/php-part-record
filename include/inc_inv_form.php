<div id="add-form">
   <div id="test1" class="col s12" style="padding: 20px">
      <div class="row" style="margin-bottom: 0;">
         <div class="input-field col s12">
            <a class="waves-effect waves-light btn blue-grey lighten-4 z-depth-0" style="width: 100%; margin-top: 5px;" id="add-new-product">добавить позицию</a>
            <table id="income-table" class="product-table">
               <thead>
                  <tr>
                     <th>№</th>
                     <th>Номенклатура</th>
                     <th>Количество</th>
                     <th>Дата изготовления</th>
                     <th>Срок годности</th>
                     <th></th>
                  </tr>
               </thead>

               <tbody>
                  <tr>

                  </tr>
               </tbody>
            </table>
            <p id="empty-message" style="display: none;">Список продукции пуст, нажмите «добавить позицию».</p>

         </div>
      </div>

   </div>
   <div class="divider" style="margin: 10px 0px;"></div>
   <a id="create-record" class="waves-effect waves-light btn-large" style="width: 100%">Начать инвентаризацию</a>
</div>

<div id="compare-form" style="display: none;">
   <div id="test1" class="col s12" style="padding: 20px">
      <div class="row" style="margin-bottom: 0;">
         <div class="info-message">Проверьте данные и подтвердите операцию</div>
         <div class="col s12">
            <table id="compare-table" class="product-table">
               <thead>
                  <tr>
                     <th>№</th>
                     <th>Номенклатура</th>
                     <th>Дата изготовления</th>
                     <th>Ед.изм</th>
                     <th>Количество в журнале</th>
                     <th>Количество по факту</th>
                     <th>Разница</th>
                     <th></th>
                  </tr>
               </thead>

               <tbody>
                  <tr>

                  </tr>
               </tbody>
            </table>
         </div>
      </div>

   </div>
   <div class="divider" style="margin: 10px 0px;"></div>
   <div class="row" style="margin-bottom: 0;">
      <div class="col s6">
         <a id="inventory-confirm" class="waves-effect waves-light btn-large" style="width: 100%">Скорректировать остатки</a>
      </div>
      <div class="col s6">
         <a id="return-to-add" class="waves-effect waves-light btn-large blue-grey lighten-2" style="width: 100%">Вернуться назад</a>
      </div>
   </div>
</div>