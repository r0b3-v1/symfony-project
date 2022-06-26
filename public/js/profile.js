const profileTabs = document.getElementsByClassName('profile_tabs');
const contents = document.getElementsByClassName('profile_content');

for (let tab of profileTabs) {
    console.log('lu')
    tab.addEventListener('click', function(){
        removeActiveClass(profileTabs);
        removeActiveClass(contents);

        this.classList.add('active');
        let target = this.getAttribute('target');
        let contentToBeDisplayed = document.querySelector(`[data="${target}"]`);
        contentToBeDisplayed.classList.add('active');


    });
    
}

function removeActiveClass(collection) {
    for (const item of collection) {
        item.classList.remove('active');
    }
}

