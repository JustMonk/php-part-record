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
   for(let entry of globalState.incomeTable) {
      let tr = document.createElement('tr');
      tr.setAttribute('data-id', entry[0]);
      tr.innerHTML = `<tr>
         <td>${num}</td>
         <td>${entry[1].name}</td>
         <td>${entry[1].count}</td>
         <td>${entry[1].createDate}</td>
         <td>${globalState.goods[entry[1].name].valid_days}</td>
         <td>X</td>
      </tr>`;
      tableBody.append(tr);
      num++;
   }
}

document.addEventListener('click', (e) => {
   if (e.target.id == 'add-product-button') {

      let id = Math.floor(Math.random() * Math.floor(9999));
      while(globalState.incomeTable.has(id)) {
         id = Math.floor(Math.random() * Math.floor(9999));
      }

      globalState.incomeTable.set(id, {
         name: document.getElementById('goods-select').value,
         count: document.getElementById('goods-count').value,
         createDate: document.getElementById('goods-create-date').value
      });
      console.log('добавлена строка в map');
      incomeTableRender();
   }

   //заглушка под добавление
   if (e.target.id == 'create-record') {
      M.toast({html: 'Приход создан! (но записи в БД пока нет, тестовая версия)'})
   }
})

//ощищает модальную форму добавления и скрывает доп.поля
function clearAddModal() {
   let modalWrapper = document.getElementById('add-modal');
   document.getElementById('extended-fields').style.display = 'none';
   modalWrapper.querySelectorAll('input').forEach(val => { val.value = '' });
   M.updateTextFields();
}

//callback, чтобы убирать значение если введенного нет в стэйте, либо возвращать предыдущее
document.addEventListener('focusout', (e) => {
   if (e.target.tagName != 'INPUT' && !e.target.hasAttribute('data-autocomplete-object')) return;
   if (!globalState[e.target.getAttribute('data-autocomplete-object')].hasOwnProperty([e.target.value])) {
      e.target.value = '';
      M.updateTextFields();
   }
});