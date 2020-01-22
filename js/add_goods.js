//state
let globalState = {
   incomeTable: new Map()
};

//data init
function getUserState() {

   fetch('./action/get_add_state.php').then(response => {
      return response.json()
   }).then(data => {
      //очищаем текущий буфер
      delete globalState.partners;
      delete globalState.goods;
      //мерджим полученные с сервера данные в состояние
      Object.assign(globalState, data);
      //диспатчим эвент после получения данных
      let event = new Event("fetchComplete", {bubbles: true});
      document.dispatchEvent(event);

      var partnerSelect = document.querySelector('#partner-select');
      var instances = M.Autocomplete.init(partnerSelect, {
         data: globalState.partners,
         minLength: 0,
         onAutocomplete: function () {
            console.log('gg');
         }
      });

      var goodsSelect = document.querySelector('#goods-select');
      var instances = M.Autocomplete.init(goodsSelect, {
         data: globalState.goods,
         minLength: 0,
         onAutocomplete: function (elem) {
            if (globalState.goods[elem].extended_milk_fields) document.getElementById('extended-fields').style.display = 'block';
            else document.getElementById('extended-fields').style.display = 'none';
            document.querySelector('#valid-until').value = globalState.goods[elem].valid_days;
         }
      });

      incomeTableRender();
      console.log(globalState);
   });
}

document.addEventListener('DOMContentLoaded', e => getUserState());

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
   //обработчик для кнопки, которая закрывает уведомление
   if (e.target.classList.contains('close-message') || e.target.parentElement.classList.contains('close-message')) {
      e.target.closest('.message-card').remove();
   }

   let modal = M.Modal.getInstance(document.getElementById('add-modal'));
   //обработчик добавления новой позиции
   if (e.target.id == 'add-product-button') {
      if (!modalValidation()) return;

      let id = Math.floor(Math.random() * Math.floor(9999));
      while (globalState.incomeTable.has(id)) {
         id = Math.floor(Math.random() * Math.floor(9999));
      }

      if (e.target.hasAttribute('data-id')) id = +e.target.getAttribute('data-id');

      //проверка на дублирование
      for (let entry of globalState.incomeTable) { // то же что и recipeMap.entries()
         let key = entry[0];
         let obj = entry[1];

         if (obj.name == document.getElementById('goods-select').value && obj.createDate == document.getElementById('goods-create-date').value) {
            globalState.incomeTable.delete(id);
            id = key;
            break;
         }
      }

      globalState.incomeTable.set(id, {
         product_id: globalState.goods[document.getElementById('goods-select').value].id,
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

   //запрос
   if (e.target.id == 'create-record') {
      if (!formValidation()) return;
      //+ ' UTC' timezone fix

      let data = {
         docNum: document.getElementById('doc-number').value,
         operationDate: new Date(document.getElementById('operation-date').value + ' UTC').toISOString().split('T')[0],
         partner: document.getElementById('partner-select').value,
         productList: [...globalState.incomeTable.values()].map(val => {
            let changedObj = val;
            changedObj.createDate = new Date(changedObj.createDate + ' UTC').toISOString().split('T')[0];
            return changedObj;
         })
      }

      console.log(JSON.stringify(data));

      fetch('action/add_operation_confirm.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(result => {
         console.log(result);
         return result.json();
      }).then(json => {
         console.log(json);
         showMessage(json);
         if (json.type == 'success') clearForm();
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

//полностью очищает форму и состояние
function clearForm() {
   let wrapper = document.getElementById('main-wrapper');
   let inputs = wrapper.querySelectorAll('input');
   inputs.forEach(val => {
      val.value = '';
   });
   M.updateTextFields();
   //очистка клиентского состояния
   globalState.incomeTable.clear();
   incomeTableRender();
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
   let parentNode = document.querySelector('#main-wrapper .content-wrapper');
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