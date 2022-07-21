const searchDiv = document.getElementById('search-modal');
const typeSelect = document.getElementById('search-type-select');
const quickSearchSubmits = document.getElementsByClassName('quicksearch-submit');
const searchCollapser = document.getElementById('search-collapser');
const type = document.getElementById('search-form-type');



type?.addEventListener('click', function(e){
    e.preventDefault();
    const postSearch = document.querySelector('fieldset.post-field');
    const artistSearch = document.querySelector('fieldset.author-field');
    const searchHidden = document.getElementById('search-hidden');
    postSearch.classList.toggle('deactivated');
    artistSearch.classList.toggle('deactivated');
    if(searchHidden.value == 'post'){
        searchHidden.value = 'artist';
        type.textContent = 'Chercher des images';
    }
    else{
        searchHidden.value = 'post';
        type.textContent = 'Chercher des artistes';
    }

})

searchCollapser?.addEventListener('click', function(){
    this.classList.toggle('fa-plus');
    this.classList.toggle('fa-minus');
})

for (const quickSearchSubmit of quickSearchSubmits) {
        quickSearchSubmit.addEventListener('click', function(){
        this.parentElement.submit();
    })
}

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

