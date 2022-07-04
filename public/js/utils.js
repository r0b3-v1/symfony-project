const triggers = document.querySelectorAll('[target][collapse-trigger]');

for (const trigger of triggers) {
    trigger.addEventListener('click', function (e) {
        e.preventDefault();
        let targetId = this.getAttribute('target');
        const target = document.querySelector(`#${targetId}[collapsable]`);
        target.classList.toggle('collapsed');
    })

}
// on récupère les select servant à filtrer les listes, ils ont un attribut filter
const filters = document.querySelectorAll('select[filter]');


for (const filter of filters) {
    filter.addEventListener('change', function () {
        let value = this.options[this.selectedIndex].value;
        let target = this.getAttribute('filter');
        //on récupère les éléments que le filtre peut filtrer
        const filtrables = document.querySelectorAll(`[filtrable=${target}]`);
        for (const element of filtrables) {
            let data = element.getAttribute('data');
            element.classList.add('hidden');
            if (data == value || value == 'all') {
                element.classList.remove('hidden');
            }
        }
    })
}

// fonction qui transforme une chaîne de caractères en élément HTML
function HTMLFromString(str){
    const div = document.createElement('div');
    div.innerHTML = str.trim();
    return div.firstChild;
}