console.log(globalState);
//тут обработчик события для модалки

//он будет каждый раз открывать модал и переинициализовать поля, которые заполняются сервером
//если HTML модалки заранее определен, можно так же заранее и инициализировать
//но суть в том, что для каждой позиции будет своя модалка, поэтому заранее выполненная инициализация это только обложка

//одна модалка для добавления (константа, захардкоженная)
//одна для редактирования (обложка, динамическая)

//КАК ФИКСИТЬ ВЫБОР ДАТЫ В МОДАЛКЕ!!!!!!!!!
//modal-conent - position relative
//modal-datepicker - height auto
//можно внутри родительской модалке overwrite !important'ы прописать

function incomeTableRender() {
   let num = 1;
   let tableBody = document.getElementById('income-table').tBodies[0];
   tableBody.innerHTML = '';
   for (let entry of globalState.incomeTable) {
      let tr = document.createElement('tr');
      tr.setAttribute('data-id', entry[0]);
      tr.setAttribute('data-action', 'product-edit');
      tr.innerHTML = `<tr>
         <td>${num}</td>
         <td>${entry[1].name}</td>
         <td>${entry[1].count}</td>
         <td>${entry[1].createDate}</td>
         <td>${globalState.goods[entry[1].name].valid_days}</td>
         <td><a class="delete-row-button"><i class="material-icons">delete_forever</i></a></td>
      </tr>`;
      tableBody.append(tr);
      num++;
   }

   //сообщение о пустой таблице
   if (!globalState.incomeTable.size) document.getElementById('empty-message').style.display = 'block';
   else document.getElementById('empty-message').style.display = 'none';
}

document.addEventListener('click', (e) => {
   let modal = M.Modal.getInstance(document.getElementById('add-modal'));
   //обработчик добавления новой позиции
   if (e.target.id == 'add-product-button') {
      if (!modalValidation()) return;

      let id = Math.floor(Math.random() * Math.floor(9999));
      while (globalState.incomeTable.has(id)) {
         id = Math.floor(Math.random() * Math.floor(9999));
      }

      if (e.target.hasAttribute('data-id')) id = +e.target.getAttribute('data-id');

      globalState.incomeTable.set(id, {
         name: document.getElementById('goods-select').value,
         count: document.getElementById('goods-count').value,
         createDate: document.getElementById('goods-create-date').value,
         extFat: document.getElementById('ext-fat').value,
         extSolidity: document.getElementById('ext-solidity').value,
         extAcidity: document.getElementById('ext-acidity').value
      });
      console.log('добавлена строка в map');
      incomeTableRender();
      modal.close();
   }

   //удаление ячейки из состояния и таблицы
   if (e.target.closest('a') && e.target.closest('a').classList.contains('delete-row-button')) {
      console.log('вошли в deleterow');
      let row = e.target.closest('tr');
      globalState.incomeTable.delete(+row.getAttribute('data-id'));
      incomeTableRender();
      return;
   }

   //модальное окно для добавления новой позиции
   if (e.target.id == 'add-new-product') {
      let modal = M.Modal.getInstance(document.getElementById('add-modal'));
      clearAddModal();

      modal.el.querySelector('#modal-title').innerText = 'Добавление товара';
      modal.el.querySelector('#modal-desc').innerText = 'Выберите номенклатуру из списка ниже и заполните информацию о товаре';
      let footer = modal.el.querySelector('.modal-footer');
      footer.innerHTML = `
      <a href="#!" id="add-product-button" class="waves-effect waves-green btn blue">Добавить</a>
      <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      `;

      modal.open();
   }

   //модальное окно для редактирования существующей позиции
   if (e.target.closest('tr') && e.target.closest('tr').getAttribute('data-action') == 'product-edit') {
      let rowId = +e.target.closest('tr').getAttribute('data-id');
      let modal = M.Modal.getInstance(document.getElementById('add-modal'));
      clearAddModal();

      //общие поля
      modal.el.querySelector('#goods-select').value = globalState.incomeTable.get(rowId).name;
      modal.el.querySelector('#goods-count').value = globalState.incomeTable.get(rowId).count;
      modal.el.querySelector('#goods-create-date').value = globalState.incomeTable.get(rowId).createDate;
      modal.el.querySelector('#valid-until').value = globalState.goods[globalState.incomeTable.get(rowId).name].valid_days;
      //дополнительные поля для сырого молока
      modal.el.querySelector('#ext-fat').value = globalState.incomeTable.get(rowId).extFat;
      modal.el.querySelector('#ext-solidity').value = globalState.incomeTable.get(rowId).extSolidity;
      modal.el.querySelector('#ext-acidity').value = globalState.incomeTable.get(rowId).extAcidity;
      //показать доп. поля, если редактируем сырое молоко
      if (globalState.goods[globalState.incomeTable.get(rowId).name].extended_milk_fields) modal.el.querySelector('#extended-fields').style.display = 'block';

      modal.el.querySelector('#modal-title').innerText = 'Редактирование товара';
      modal.el.querySelector('#modal-desc').innerText = 'Внесите необходимые изменения и нажмите сохранить';
      let footer = modal.el.querySelector('.modal-footer');
      footer.innerHTML = `
      <a href="#!" id="add-product-button" data-id="${rowId}" class="waves-effect waves-green btn blue">Сохранить</a>
      <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      `;

      modal.open();
      M.updateTextFields();
   }

   //заглушка под добавление
   if (e.target.id == 'create-record') {
      if (!formValidation()) return;
      M.toast({ html: 'Приход создан! (но записи в БД пока нет, тестовая версия)' });
   }
})

function modalValidation() {
   let isValid = true;
   let modal = M.Modal.getInstance(document.getElementById('add-modal'));

   if (modal.el.querySelector('#goods-select').value.length < 1) {
      isValid = false;
      modal.el.querySelector('#goods-select').className = 'validate invalid';
   }

   if (modal.el.querySelector('#goods-count').value.length < 1) {
      isValid = false;
      modal.el.querySelector('#goods-count').className = 'validate invalid';
   }

   if (modal.el.querySelector('#goods-create-date').value.length < 1) {
      isValid = false;
      modal.el.querySelector('#goods-create-date').className = 'validate invalid';
   }

   //проверка на доп.поля (если требуется)
   if (globalState.goods[modal.el.querySelector('#goods-select').value].extended_milk_fields) {
      if (modal.el.querySelector('#ext-fat').value.length < 1) {
         isValid = false;
         modal.el.querySelector('#ext-fat').className = 'validate invalid';
      }

      if (modal.el.querySelector('#ext-solidity').value.length < 1) {
         isValid = false;
         modal.el.querySelector('#ext-solidity').className = 'validate invalid';
      }

      if (modal.el.querySelector('#ext-acidity').value.length < 1) {
         isValid = false;
         modal.el.querySelector('#ext-acidity').className = 'validate invalid';
      }
   }

   return isValid;
}

function formValidation() {
   let isValid = true;

   if (document.getElementById('first_name').value.length < 1) {
      isValid = false;
      document.getElementById('first_name').className = 'validate invalid';
   }

   if (document.getElementById('operation-date').value.length < 1) {
      isValid = false;
      document.getElementById('operation-date').className = 'validate invalid';
   }

   if (document.getElementById('partner-select').value.length < 1) {
      isValid = false;
      document.getElementById('partner-select').className = 'validate invalid';
   }

   //не пустая таблица (состояние)
   if (!globalState.incomeTable.size) {
      isValid = false;
      M.toast({ html: 'Необходимо добавить хотя бы один товар' });
   }

   return isValid;
}

//ощищает модальную форму добавления и скрывает доп.поля
function clearAddModal() {
   let modalWrapper = document.getElementById('add-modal');
   document.getElementById('extended-fields').style.display = 'none';
   modalWrapper.querySelectorAll('input').forEach(val => {
      val.value = '';
      val.className = '';
   });
   M.updateTextFields();
}

//callback, чтобы убирать значение если введенного нет в стэйте, либо возвращать предыдущее
document.addEventListener('focusout', (e) => {
   if (e.target.tagName != 'INPUT' && !e.target.hasAttribute('data-autocomplete-object')) return;
   if (!globalState[e.target.getAttribute('data-autocomplete-object')].hasOwnProperty([e.target.value])) {
      e.target.value = '';
      M.updateTextFields();
      clearAddModal();
   }
});