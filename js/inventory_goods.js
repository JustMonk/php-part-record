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
   //подтверждение инвентаризации
   if (e.target.id == 'inventory-confirm') {
      let data = globalState.compareTable;

      console.log(JSON.stringify(data));

      fetch('action/inv_operation_confirm.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(result => {
         console.log(result);
         return result.json();
      }).then(json => {
         console.log(json);
         showMessage(json);
         if (json.type == "success") {
            showAddForm();
            clearForm();
         }
         //showMessage(json);
         /*if (json.type == 'success') {
            clearForm();
         }*/
      });
   }

   //возврат с формы сравнения
   if (e.target.id == 'return-to-add') {
      showAddForm();
   }

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
         name: document.getElementById('goods-select').value,
         count: document.getElementById('goods-count').value,
         createDate: document.getElementById('goods-create-date').value,
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

      let data = {
         productList: [...globalState.incomeTable.values()].map(val => {
            let changedObj = val;
            changedObj.createDate = new Date(changedObj.createDate + ' UTC').toISOString().split('T')[0];
            return changedObj;
         })
      }

      console.log(JSON.stringify(data));

      fetch('action/inv_operation_compare.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(result => {
         console.log(result);
         return result.json();
      }).then(json => {
         console.log(json);
         console.log(json.compareList);
         if (json.type == "compare") {
            globalState.compareTable = json.compareList;
            showCompareForm(json.compareList);
         }
         //showMessage(json);
         /*if (json.type == 'success') {
            clearForm();
         }*/
      });
   }
})

function showCompareForm(compareList) {
   console.log('compare form init');
   document.getElementById('add-form').style.display = 'none';
   document.getElementById('compare-form').style.display = 'block';

   let table = document.getElementById('compare-table');
   table.tBodies[0].innerHTML = '';

   compareList.forEach((val, i) => {
      let tr = document.createElement('tr');
      let diff = +val.real_count - +val.count;
      tr.innerHTML = `
      <td>${i+1}</td>
      <td>${val.title}</td>
      <td>${val.create_date}</td>
      <td>${val.unit}</td>
      <td>${val.count}</td>
      <td>${val.real_count}</td>
      <td style="color: ${diff < 0 ?'red':'green'}; font-weight: bold;">${diff}</td>
      `;
      table.tBodies[0].append(tr);
   });

}

function showAddForm() {
   document.getElementById('compare-form').style.display = 'none';
   document.getElementById('add-form').style.display = 'block';
}

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

   return isValid;
}

function formValidation() {
   let isValid = true;

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
   modalWrapper.querySelectorAll('input').forEach(val => {
      val.value = '';
      val.className = '';
   });
   M.updateTextFields();
}

//полностью очищает форму и состояние
function clearForm() {
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