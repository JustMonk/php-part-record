<!-- Modal Structure (модалка для добавления, триггерится по нажатию кнопки "добавить позицию") -->
<div id="add-modal" class="modal modal-fixed-footer modal-form">
   <div class="modal-content">
      <h4 id="modal-title" style="font-weight: bold;"></h4>
      <p id="modal-desc"></p>
      <h5>Основные поля</h5>

      <div id="main-fields">
         <div class="row">
            <div class="col s12 flex-col">
               <div class="input-field">
                  <input autocomplete="off" data-autocomplete-object="goods" placeholder="Нажмите для выбора или поиска номенклатуры" id="goods-select" type="text" class="">
                  <label for="goods-select">Номенклатура</label>
               </div>
               <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
            </div>
         </div>


         <div class="row">
            <div class="col s12 m8 flex-col">
               <div class="input-field">
                  <input autocomplete="off" placeholder="Введите количество товара" id="goods-count" type="number" min="1" class="">
                  <label for="goods-count">Количество</label>
               </div>
               <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
            </div>
            <div class="col s6 m2 flex-col">
               <div class="input-field">
                  <input disabled autocomplete="off" placeholder="%ед.изм%" id="goods-unit" type="text" class="">
                  <label for="goods-unit">Ед.изм</label>
               </div>
            </div>
            <div class="col s6 m2 flex-col">
               <div class="input-field">
                  <input disabled autocomplete="off" placeholder="%доступно%" id="goods-avaliable-count" type="text" class="">
                  <label for="goods-avaliable-count">Доступно</label>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col s12 m6 flex-col">
               <div class="input-field">
                  <input disabled id="goods-create-date" placeholder="%дата.изг%" type="text" class="datepicker">
                  <label for="goods-create-date" class="active">Дата изготовления</label>
               </div>
            </div>

            <div class="col s12 m6 flex-col">
               <div class="input-field">
                  <input disabled id="goods-expire-date" placeholder="%годен_до%" type="text">
                  <label for="goods-expire-date" class="active">Годен до</label>
               </div>
            </div>
         </div>
      </div>

   </div>
   <div class="modal-footer">
      <!-- js controls -->
   </div>
</div>