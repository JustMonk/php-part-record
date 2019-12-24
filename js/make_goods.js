console.log(globalState);


function doubleTableRender() {
   //таблица сырья
   let num = 1;
   let tableBody = document.getElementById('material-table').tBodies[0];
   tableBody.innerHTML = '';
   for (let entry of globalState.materialTable) {
      let tr = document.createElement('tr');
      tr.setAttribute('data-id', entry[0]);
      tr.setAttribute('data-action', 'product-edit');
      tr.setAttribute('data-type', 'material');
      tr.innerHTML = `<tr>
         <td>${num}</td>
         <td>${entry[1].name}</td>
         <td>${entry[1].count}</td>
         <td>${entry[1].unit}</td>
         <td>${entry[1].createDate}</td>
         <td>${entry[1].expireDate}</td>
         <td><a class="delete-row-button"><i class="material-icons">delete_forever</i></a></td>
      </tr>`;
      tableBody.append(tr);
      num++;
   }

   //таблица продукции
   num = 1;
   tableBody = document.getElementById('make-table').tBodies[0];
   tableBody.innerHTML = '';
   for (let entry of globalState.makeTable) {
      let tr = document.createElement('tr');
      tr.setAttribute('data-id', entry[0]);
      tr.setAttribute('data-action', 'product-edit');
      tr.setAttribute('data-type', 'product');
      tr.innerHTML = `<tr>
         <td>${num}</td>
         <td>${entry[1].name}</td>
         <td>${entry[1].count}</td>
         <td>${entry[1].unit}</td>
         <td>${entry[1].createDate}</td>
         <td>${entry[1].expireDate}</td>
         <td><a class="delete-row-button"><i class="material-icons">delete_forever</i></a></td>
      </tr>`;
      tableBody.append(tr);
      num++;
   }

   //сообщение о пустой таблице
   //в сырье
   if (!globalState.materialTable.size) document.getElementById('empty-material-message').style.display = 'block';
   else document.getElementById('empty-material-message').style.display = 'none';
   //в производимой
   if (!globalState.makeTable.size) document.getElementById('empty-make-message').style.display = 'block';
   else document.getElementById('empty-make-message').style.display = 'none';
}

document.addEventListener('click', (e) => {
   //обработчик для кнопки, которая закрывает уведомление
   if (e.target.classList.contains('close-message') || e.target.parentElement.classList.contains('close-message')) {
      e.target.closest('.message-card').remove();
   }

   let modal = M.Modal.getInstance(document.getElementById('add-modal'));

   //обработчик добавления нового сырья
   if (e.target.id == 'add-material-button') {
      if (!modalValidation()) return;

      //проверяет есть ли ID, чтобы сохранить изменения по этому ключу
      /*let id = Math.floor(Math.random() * Math.floor(9999));
      while (globalState.materialTable.has(id)) {
         id = Math.floor(Math.random() * Math.floor(9999));
      }
      if (e.target.hasAttribute('data-id')) id = +e.target.getAttribute('data-id');*/
      let id = document.getElementById('goods-select').value;
      if (e.target.hasAttribute('data-id')) {
         console.log('delete prev id');
         let prevId = e.target.getAttribute('data-id');
         //предотвращение потери данных и дублирования при редактировании
         if (globalState.materialTable.has(prevId)) globalState.materialTable.delete(prevId);
      }

      //в зависимости от того, что выбрано в поле номенклатуры (в момент нажатия на кнопку) - устанавливаем "справочный" регистр
      let targetRegistry = globalState.materials.hasOwnProperty(document.getElementById('goods-select').value) ? globalState.materials : globalState.halfway;

      globalState.materialTable.set(id, {
         registry_id: targetRegistry[document.getElementById('goods-select').value].registry_id,
         string_key: document.getElementById('goods-select').value,
         name: targetRegistry[document.getElementById('goods-select').value].name,
         count: document.getElementById('goods-count').value,
         unit: targetRegistry[document.getElementById('goods-select').value].unit,
         createDate: targetRegistry[document.getElementById('goods-select').value].create_date,
         expireDate: targetRegistry[document.getElementById('goods-select').value].expire_date
      });

      console.log('добавлена строка в material-map');
      doubleTableRender();
      modal.close();

      //let id = globalState.goods[document.getElementById('goods-select').value].registry_id;
   }

   //обработчик добавления нового продукта производства
   if (e.target.id == 'add-product-button') {
      if (!modalValidation()) return;

      //проверяет есть ли ID, чтобы сохранить изменения по этому ключу
      let id = Math.floor(Math.random() * Math.floor(9999));
      while (globalState.makeTable.has(id)) {
         id = Math.floor(Math.random() * Math.floor(9999));
      }
      if (e.target.hasAttribute('data-id')) id = +e.target.getAttribute('data-id');

      let targetRegistry = globalState.halfwayList.hasOwnProperty(document.getElementById('goods-select').value) ? globalState.halfwayList : globalState.finishedList;

      globalState.makeTable.set(id, {
         product_id: targetRegistry[document.getElementById('goods-select').value].id,
         name: document.getElementById('goods-select').value,
         count: document.getElementById('goods-count').value,
         unit: targetRegistry[document.getElementById('goods-select').value].unit,
         createDate: document.getElementById('goods-create-date').value,
         expireDate: document.getElementById('goods-expire-date').value
      });

      console.log('добавлена строка в product-map');
      doubleTableRender();
      modal.close();

      //let id = globalState.goods[document.getElementById('goods-select').value].registry_id;
   }

   //удаление ячейки из состояния и таблицы
   if (e.target.closest('a') && e.target.closest('a').classList.contains('delete-row-button')) {
      console.log('вошли в deleterow');
      let row = e.target.closest('tr');

      if (globalState.materialTable.has(row.getAttribute('data-id'))) {
         globalState.materialTable.delete(row.getAttribute('data-id'));
      } else {
         globalState.makeTable.delete(+row.getAttribute('data-id'));
      }

      doubleTableRender();
      return;
   }

   //модальное окно для добавления нового сырья
   if (e.target.id == 'add-new-material') {
      let modal = M.Modal.getInstance(document.getElementById('add-modal'));
      let autocompleteData;
      clearAddModal();

      //зависит от того, что производим
      let typeSelect = document.querySelector('#production_type');
      let goodsSelect = document.querySelector('#goods-select');
      if (typeSelect.value == 'halfway') {
         autocompleteData = globalState.materials;
         goodsSelect.setAttribute('data-autocomplete-object', 'materials');
      } else {
         autocompleteData = globalState.halfway;
         goodsSelect.setAttribute('data-autocomplete-object', 'halfway');
      }

      let instances = M.Autocomplete.init(goodsSelect, {
         data: autocompleteData,
         minLength: 0,
         onAutocomplete: function (elem) {
            document.querySelector('#goods-unit').value = autocompleteData[elem].unit;
            document.querySelector('#goods-avaliable-count').value = autocompleteData[elem].count;
            document.querySelector('#goods-create-date').value = autocompleteData[elem].create_date;
            document.querySelector('#goods-expire-date').value = autocompleteData[elem].expire_date;
         }
      });

      modal.el.querySelector('#modal-title').innerHTML = '<i class="fas fa-cubes"></i> Добавление сырья';
      modal.el.querySelector('#modal-desc').innerText = 'Выберите номенлатуру из списка, которая будет использована в качестве сырья.';
      let footer = modal.el.querySelector('.modal-footer');
      footer.innerHTML = `
      <a href="#!" id="add-material-button" class="waves-effect waves-green btn blue">Добавить</a>
      <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      `;

      modal.open();
   }

   //модальное окно для добавления нового продукта производства
   if (e.target.id == 'add-new-product') {
      let modal = M.Modal.getInstance(document.getElementById('add-modal'));
      let autocompleteData;
      clearAddModal();

      //зависит от того, что производим
      let typeSelect = document.querySelector('#production_type');
      let goodsSelect = document.querySelector('#goods-select');
      if (typeSelect.value == 'halfway') {
         autocompleteData = globalState.halfwayList;
         goodsSelect.setAttribute('data-autocomplete-object', 'halfwayList');
      } else {
         autocompleteData = globalState.finishedList;
         goodsSelect.setAttribute('data-autocomplete-object', 'finishedList');
      }

      let instances = M.Autocomplete.init(goodsSelect, {
         data: autocompleteData,
         minLength: 0,
         onAutocomplete: function (elem) {
            document.querySelector('#goods-unit').value = autocompleteData[elem].unit;
            document.querySelector('#goods-avaliable-count').value = '-';

            let nowDate = new Date();
            document.querySelector('#goods-create-date').value = nowDate.toISOString().split('T')[0];

            let expireDate = new Date();
            expireDate.setDate(nowDate.getDate() + +autocompleteData[elem].valid_days);
            document.querySelector('#goods-expire-date').value = expireDate.toISOString().split('T')[0];
         }
      });

      modal.el.querySelector('#modal-title').innerHTML = '<i class="fas fa-cheese"></i> Добавление товара';
      modal.el.querySelector('#modal-desc').innerText = 'Выберите номеклатуру, которую производите.';
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
      let footer = modal.el.querySelector('.modal-footer');
      //объект из которого тянем данные для автозаполнения, присваивается в условии
      let goodsObj;// = globalState.goods[globalState.incomeTable.get(rowId).name];
      clearAddModal();

      let typeSelect = document.querySelector('#production_type');
      let goodsSelect = document.querySelector('#goods-select');
      if (e.target.closest('tr').getAttribute('data-type') == 'material') {
         rowId = e.target.closest('tr').getAttribute('data-id');
         //2 way for material select re-init
         if (typeSelect.value == 'halfway') {
            goodsObj = globalState.materials[rowId];
            let instances = M.Autocomplete.init(goodsSelect, {
               data: globalState.materials,
               minLength: 0,
               onAutocomplete: function (elem) {
                  document.querySelector('#goods-unit').value = globalState.materials[elem].unit;
                  document.querySelector('#goods-avaliable-count').value = globalState.materials[elem].count;
                  document.querySelector('#goods-create-date').value = globalState.materials[elem].create_date;
                  document.querySelector('#goods-expire-date').value = globalState.materials[elem].expire_date;
               }
            });
         } else {
            goodsObj = globalState.halfway[rowId];
            let instances = M.Autocomplete.init(goodsSelect, {
               data: globalState.halfway,
               minLength: 0,
               onAutocomplete: function (elem) {
                  document.querySelector('#goods-unit').value = globalState.halfway[elem].unit;
                  document.querySelector('#goods-avaliable-count').value = globalState.halfway[elem].count;
                  document.querySelector('#goods-create-date').value = globalState.halfway[elem].create_date;
                  document.querySelector('#goods-expire-date').value = globalState.halfway[elem].expire_date;
               }
            });
         }
         //общие поля
         modal.el.querySelector('#goods-select').value = rowId;
         console.log(rowId);
         modal.el.querySelector('#goods-count').value = globalState.materialTable.get(rowId).count;
         modal.el.querySelector('#goods-unit').value = goodsObj.unit;
         modal.el.querySelector('#goods-avaliable-count').value = goodsObj.count;
         modal.el.querySelector('#goods-create-date').value = goodsObj.create_date;
         modal.el.querySelector('#goods-expire-date').value = goodsObj.expire_date;

         footer.innerHTML = `
         <a href="#!" id="add-material-button" data-id="${rowId}" class="waves-effect waves-green btn blue">Сохранить</a>
         <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
         `;
      } else {
         //2 way for product
         if (typeSelect.value == 'halfway') {
            goodsObj = globalState.makeTable.get(rowId);
            let instances = M.Autocomplete.init(goodsSelect, {
               data: globalState.halfwayList,
               minLength: 0,
               onAutocomplete: function (elem) {
                  document.querySelector('#goods-unit').value = globalState.halfwayList[elem].unit;
                  document.querySelector('#goods-avaliable-count').value = '-';

                  let nowDate = new Date();
                  document.querySelector('#goods-create-date').value = nowDate.toISOString().split('T')[0];

                  let expireDate = new Date();
                  expireDate.setDate(nowDate.getDate() + +globalState.halfwayList[elem].valid_days);
                  document.querySelector('#goods-expire-date').value = expireDate.toISOString().split('T')[0];
               }
            });
         } else {
            goodsObj = globalState.makeTable.get(rowId);
            let instances = M.Autocomplete.init(goodsSelect, {
               data: globalState.finishedList,
               minLength: 0,
               onAutocomplete: function (elem) {
                  document.querySelector('#goods-unit').value = globalState.finishedList[elem].unit;
                  document.querySelector('#goods-avaliable-count').value = '-';

                  let nowDate = new Date();
                  document.querySelector('#goods-create-date').value = nowDate.toISOString().split('T')[0];

                  let expireDate = new Date();
                  expireDate.setDate(nowDate.getDate() + +globalState.finishedList[elem].valid_days);
                  document.querySelector('#goods-expire-date').value = expireDate.toISOString().split('T')[0];
               }
            });
         }
         //общие поля
         modal.el.querySelector('#goods-select').value = goodsObj.name;
         modal.el.querySelector('#goods-count').value = goodsObj.count;
         modal.el.querySelector('#goods-unit').value = goodsObj.unit;
         modal.el.querySelector('#goods-avaliable-count').value = '-';
         modal.el.querySelector('#goods-create-date').value = goodsObj.createDate;
         modal.el.querySelector('#goods-expire-date').value = goodsObj.expireDate;

         footer.innerHTML = `
         <a href="#!" id="add-product-button" data-id="${rowId}" class="waves-effect waves-green btn blue">Сохранить</a>
         <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
         `;
      }



      modal.el.querySelector('#modal-title').innerText = 'Редактирование товара';
      modal.el.querySelector('#modal-desc').innerText = 'Внесите необходимые изменения и нажмите сохранить';
      //let footer = modal.el.querySelector('.modal-footer');
      /*footer.innerHTML = `
      <a href="#!" id="add-product-button" data-id="${rowId}" class="waves-effect waves-green btn blue">Сохранить</a>
      <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      `;*/

      modal.open();
      M.updateTextFields();
   }

   //отправка формы
   if (e.target.id == 'create-record') {
      if (!formValidation()) return;

      let data = {
         docNum: document.getElementById('doc-number').value,
         operationDate: new Date(document.getElementById('operation-date').value).toISOString().split('T')[0],
         materialList: [...globalState.materialTable.values()],
         productList: [...globalState.makeTable.values()]
      }

      console.log(JSON.stringify(data));

      fetch('action/make_operation_confirm.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(result => {
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

   //попытка добавить количество, превышаюшее размер партии (если подбирается партия)
   console.log(modal.el.querySelector('#goods-count').value > modal.el.querySelector('#goods-avaliable-count').value);
   console.log('val = ' + modal.el.querySelector('#goods-count').value);
   console.log('val = ' + modal.el.querySelector('#goods-avaliable-count').value);

   if (isFinite(modal.el.querySelector('#goods-avaliable-count').value)) {
      if (+modal.el.querySelector('#goods-count').value > +modal.el.querySelector('#goods-avaliable-count').value) {
         isValid = false;
         modal.el.querySelector('#goods-count').className = 'validate invalid';
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

   //не пустая таблица сырья (состояние)
   if (!globalState.materialTable.size) {
      isValid = false;
      M.toast({ html: 'Необходимо добавить сырье' });
   } else if (!isValid) {
      M.toast({ html: 'Заполните обязательные поля' });
   }

   //не пустая таблица продукции (состояние)
   if (!globalState.makeTable.size) {
      isValid = false;
      M.toast({ html: 'Необходимо добавить хотя бы один производимый товар' });
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

//очищаем состояние и таблицы при смене типа продукции
document.addEventListener('change', (e) => {
   if (e.target.id != 'production_type') return;
   globalState.materialTable.clear();
   globalState.makeTable.clear();
   doubleTableRender();
});