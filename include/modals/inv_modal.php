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
             <div class="col s12 flex-col">
                <div class="input-field">
                   <input autocomplete="off" placeholder="Введите количество товара" id="goods-count" type="number" min="1" class="">
                   <label for="goods-count">Количество</label>
                </div>
                <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
             </div>
          </div>

          <div class="row">
             <div class="col s6 flex-col">
                <div class="input-field">
                   <input id="goods-create-date" type="text" class="datepicker">
                   <label for="goods-create-date" class="active">Дата изготовления</label>
                </div>
                <i class="material-icons help-icon" data-tooltip="Введите дату изготовления">help_outline</i>
             </div>

             <div class="col s6 flex-col">
                <div class="input-field">
                   <input disabled id="valid-until" placeholder="Сначала выберите номенклатуру" type="text">
                   <label for="valid-until" class="active">Срок годности</label>
                </div>
                <i class="material-icons help-icon" data-tooltip="Срок годности вычисляется автоматически">help_outline</i>
             </div>
          </div>
       </div>

    </div>
    <div class="modal-footer">
       <!-- js controls -->
    </div>
 </div>