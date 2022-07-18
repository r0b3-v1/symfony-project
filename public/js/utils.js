const triggers = document.querySelectorAll('[target][collapse-trigger]');
const passInput = document.querySelectorAll('input[type="password"]');
console.log(passInput);

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

//fonction qui permet d'ajouter un oeil pour afficher/cacher son mot de passe
(function togglePassword(){
    for (const input of passInput) {
        const toggleVisibleButton = document.createElement('i');
        const divContainer = input.parentElement;
        const inputContainer = document.createElement('div');

        inputContainer.append(input,toggleVisibleButton);
        toggleVisibleButton.classList.add('fa-solid', 'fa-eye', 'password-toggle');
        toggleVisibleButton.setAttribute('title','Afficher le mot de passe')
        toggleVisibleButton.addEventListener('click', function(){
            this.classList.toggle('fa-eye')
            this.classList.toggle('fa-eye-slash')
            if(input.getAttribute('type') === 'password'){
                toggleVisibleButton.setAttribute('title','Masquer le mot de passe')
                input.setAttribute('type', 'text');
            }
            else{
                toggleVisibleButton.setAttribute('title','Afficher le mot de passe')
                input.setAttribute('type', 'password');
            }
        })

        divContainer.append(inputContainer);
    }
})()