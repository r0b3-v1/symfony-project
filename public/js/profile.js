const profileTabs = document.getElementsByClassName('profile_tabs');
const contents = document.getElementsByClassName('profile_content');
const notifs = document.getElementsByClassName('notif');

const notifSelect = document.getElementById('sender-choice');

// affiche la partie du profil sélectionné par l'utilisateur
for (let tab of profileTabs) {
    tab.addEventListener('click', function(){
        removeActiveClass(profileTabs);
        removeActiveClass(contents);

        this.classList.add('active');
        let target = this.getAttribute('target');
        let contentToBeDisplayed = document.querySelector(`[data="${target}"]`);
        contentToBeDisplayed.classList.add('active');


    });
}

//permet d'afficher uniquement les notifications de l'auteur sélectionné 
notifSelect.addEventListener('change', function(){
    let sender = this.options[this.selectedIndex].value;
    for (const notif of notifs) {
        notif.classList.remove('hidden');
        if( sender!= 'all' && notif.getAttribute('sender') != sender){
            notif.classList.add('hidden');
        }
    }
})

//retire la classe .active pour tous les éléments de la collection
function removeActiveClass(collection) {
    for (const item of collection) {
        item.classList.remove('active');
    }
}

