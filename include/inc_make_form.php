<div class="row" style="margin-bottom: 0;">
   <div class="col s12 flex-col">
      <div class="input-field autocomplete-field">
         <select id="production_type">
            <option value="halfway" selected>Полуфабрикат</option>
            <option value="finished">Готовая продукция</option>
         </select>
         <label>Тип производимой продукции</label>
      </div>
      <i class="material-icons help-icon" data-tooltip="Выберите тип производимой продукции">help_outline</i>
   </div>
</div>

<div class="row" style="margin-bottom: 0;">
   <h5><i class="fas fa-cubes"></i> Сырье</h5>
   <a class="waves-effect waves-light btn blue-grey lighten-4 z-depth-0" style="width: 100%; margin-top: 5px;" id="add-new-material">добавить сырье</a>

   <div class="input-field col s12" style="overflow-x: scroll;">

      <table id="material-table" class="product-table">
         <thead>
            <tr>
               <th>№</th>
               <th>Номенклатура</th>
               <th>Количество</th>
               <th>Ед.изм</th>
               <th>Дата изготовления</th>
               <th>Годен до</th>
               <th></th>
            </tr>
         </thead>

         <tbody>
            <tr>

            </tr>
         </tbody>
      </table>
      <p id="empty-material-message" style="display: none;">Список сырья пуст, нажмите «добавить сырье».</p>

   </div>
</div>

<!--<div class="divider"></div>-->

<div class="row" style="margin-bottom: 0;">
   <h5><i class="fas fa-cheese"></i> Производимая продукция</h5>
   <a class="waves-effect waves-light btn blue-grey lighten-4 z-depth-0" style="width: 100%; margin-top: 5px;" id="add-new-product">добавить продукт</a>

   <div class="input-field col s12" style="overflow-x: scroll;">

      <table id="make-table" class="product-table">
         <thead>
            <tr>
               <th>№</th>
               <th>Номенклатура</th>
               <th>Количество</th>
               <th>Ед.изм</th>
               <th></th>
            </tr>
         </thead>

         <tbody>
            <tr>

            </tr>
         </tbody>
      </table>
      <p id="empty-make-message" style="display: none;">Список продукции пуст, нажмите «добавить позицию».</p>

   </div>
</div>