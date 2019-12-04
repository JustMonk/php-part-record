var instance = M.Tabs.init(document.querySelector('.tabs'), {});
var instances = M.FormSelect.init(document.querySelectorAll('select'), {});
var instancesDate = M.Datepicker.init(document.querySelectorAll('.datepicker'), {});

document.addEventListener('click', (e) => {
   if (e.target.tagName != 'A') return;

   if (e.target.hasAttribute('data-select')) {
      let selectNodes = document.querySelectorAll('.content-block');
      selectNodes.forEach(val => {
         val.style.display = 'none';
      });
      let currentNode = document.querySelector(`#${e.target.getAttribute('data-select')}`);
      currentNode.style.display = 'block';
   }

});