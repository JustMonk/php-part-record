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
         <td>${globalState.goods[entry[1].name].expire_date}</td>
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
   //обработчик для кнопки, которая закрывает уведомление
   if (e.target.classList.contains('close-message') || e.target.parentElement.classList.contains('close-message')) {
      e.target.closest('.message-card').remove();
   }

   let modal = M.Modal.getInstance(document.getElementById('add-modal'));
   //обработчик добавления новой позиции
   if (e.target.id == 'add-product-button') {
      if (!modalValidation()) return;

      let id = globalState.goods[document.getElementById('goods-select').value].registry_id;

      //проверяет есть ли ID, чтобы сохранить изменения по этому ключу
      if (e.target.hasAttribute('data-id')) id = +e.target.getAttribute('data-id');

      globalState.incomeTable.set(id, {
         registry_id: id,
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
      modal.el.querySelector('#modal-desc').innerText = 'Выберите номенклатуру из списка ниже и введите количество';
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
      let goodsObj = globalState.goods[globalState.incomeTable.get(rowId).name];
      clearAddModal();

      //общие поля
      modal.el.querySelector('#goods-select').value = globalState.incomeTable.get(rowId).name;
      modal.el.querySelector('#goods-count').value = globalState.incomeTable.get(rowId).count;
      modal.el.querySelector('#goods-unit').value = goodsObj.unit;
      modal.el.querySelector('#goods-avaliable-count').value = goodsObj.count;
      modal.el.querySelector('#goods-create-date').value = globalState.incomeTable.get(rowId).createDate;
      modal.el.querySelector('#goods-expire-date').value = goodsObj.expire_date;
      //дополнительные поля для сырого молока
      modal.el.querySelector('#ext-fat').value = goodsObj.milk_fat;
      modal.el.querySelector('#ext-solidity').value = goodsObj.milk_solidity;
      modal.el.querySelector('#ext-acidity').value = goodsObj.milk_acidity;
      //показать доп. поля, если редактируем сырое молоко
      if (goodsObj.milk_fat != 0 || goodsObj.milk_solidity != 0 || goodsObj.milk_acidity != 0) modal.el.querySelector('#extended-fields').style.display = 'block';

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

   //отправка формы
   if (e.target.id == 'create-record') {
      if (!formValidation()) return;

      let data = {
         docNum: document.getElementById('doc-number').value,
         operationDate: new Date(document.getElementById('operation-date').value).toISOString().split('T')[0],
         partner: document.getElementById('partner-select').value,
         productList: [...globalState.incomeTable.values()]
      }

      console.log(JSON.stringify(data));

      fetch('action/sell_operation_confirm.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(result => {
         console.log(result);
         return result.json();
         //return result.text();
      }).then(json => {
         console.log(json);
         showMessage(json);
      });
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

   return isValid;
}

function formValidation() {
   let isValid = true;

   if (document.getElementById('doc-number').value.length < 1) {
      isValid = false;
      document.getElementById('doc-number').className = 'validate invalid';
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
   } else if (!isValid) {
      M.toast({ html: 'Заполните обязательные поля' });
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

//показывает в главной форме карточку с уведомлением (obj: {message: 'string', type: 'string'})
function showMessage(response) {
   //удалить старое сообщение
   let oldMessage = document.querySelector('.message-card');
   if (oldMessage) oldMessage.remove();

   let color;
   switch (response.type) {
      case 'message':
         color = 'blue';
         break;

      case 'error':
         color = 'red';
         break;

      case 'success':
         color = 'green';
         break;

      default:
         color = 'blue';
         break;
   }

   let messageNode = document.createElement('div');
   messageNode.className = `card-panel ${color} lighten-4 message-card fadeIn`;
   messageNode.innerHTML = `
   ${response.message}
   <a class="close-message"><i class="material-icons">close</i></a>
   `;
   let parentNode = document.querySelector('#main-wrapper .container');
   parentNode.insertAdjacentElement('afterbegin', messageNode);
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