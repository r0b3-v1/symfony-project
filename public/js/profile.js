const profileTabs = document.getElementsByClassName('profile_tabs');
const contents = document.getElementsByClassName('profile_content');
const notifs = document.getElementsByClassName('notif');
const acceptDemandBtn = document.getElementsByClassName('accept-demand');
const main = document.querySelector('.main');

const notifSelect = document.getElementById('sender-choice');

// affiche la partie du profil sélectionné par l'utilisateur
for (let tab of profileTabs) {
    tab.addEventListener('click', function(){
        removeActiveClass(profileTabs);
        removeActiveClass(contents);

        this.classList.add('active');
        let target = this.getAttribute('data-target');
        let contentToBeDisplayed = document.querySelector(`[data-title="${target}"]`);
        contentToBeDisplayed.classList.add('active');

    });
}

//retire la classe .active pour tous les éléments de la collection
function removeActiveClass(collection) {
    for (const item of collection) {
        item.classList.remove('active');
    }
}

//cliquer sur un bouton de cette classe crée un modal permettant d'accepter la demande de commission
for (const button of acceptDemandBtn) {
    
    button.addEventListener('click',function(){
        document.querySelector('.modal')?.remove()
        const path = this.getAttribute('data');
        const title = this.getAttribute('title');
        const modal = HTMLFromString(`
            <div class="modal">
                <div class="modal-content">
                    <h3>Accepter cette demande de commission?</h3>
                    <p>(${title})
                    <p>Vous devez spécifier un prix pour cette commission</p>

                    <form action="${path}" method="POST">
                        <label for="price">Prix :</label>
                        <input name="price" type="number" value="0.00" step="0.01">
                        <div class="buttons">
                            <button class="button" type="submit">Valider</button>
                            <button class="button" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        `);
        main.append(modal);
    })
}

// modal pour afficher les détails d'une commission
const detailsButtons = document.getElementsByClassName('details-btn');
for (const button of detailsButtons) {
    
    button.addEventListener('click', function(){
        document.querySelector('.modal')?.remove()
        const desc = this.getAttribute('data-content');
        const modal = HTMLFromString(`
        <div class="modal">
            <div class="modal-content">
                <h3 style="margin-bottom:1em;">Détails de la commande</h3>
                <p>
                ${desc}
                </p>
                <button class="button" onclick="this.parentElement.parentElement.remove()" style="margin-top:1em;">Fermer</button>
            </div>
        </div>
    `);
    main.append(modal);
    })
}

