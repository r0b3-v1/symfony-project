const searchButton = document.getElementById('search-link');
const searchDiv = document.getElementById('search-modal');
const typeSelect = document.getElementById('search-type-select');
const quickSearchSubmit = document.getElementById('quicksearch-submit');
const searchCollapser = document.getElementById('search-collapser');

searchCollapser?.addEventListener('click', function(){
    this.classList.toggle('fa-plus');
    this.classList.toggle('fa-minus');
})

quickSearchSubmit.addEventListener('click', function(e){
    this.parentElement.submit();
})

// searchButton.addEventListener('click', function (e) {
//     e.preventDefault();
//     toggleCollapse(searchDiv);
// });

typeSelect?.addEventListener('change', function(){
    let selectedValue = this.options[this.selectedIndex].value;
    for(const option of document.querySelectorAll('[data]')){
        
        if (option.getAttribute('data') != selectedValue){
            toggleCollapse(option);
        }
    }
    const divToCollapse = document.querySelector(`[data="${selectedValue}"]`);

    toggleCollapse(divToCollapse);
});

function toggleCollapse(element){
    element.classList.toggle('collapsable');
    element.classList.toggle('collapsed');
}

