const filter = document.getElementById('user-filter');
const usernames = document.getElementsByClassName('username');

filter.addEventListener('keyup', function(){
    for (const username of usernames) {
        if(username.textContent.includes(this.value) || this.value == ''){
            username.parentElement.classList.remove('hidden');
        }

        else{
            username.parentElement.classList.add('hidden');
        }
    }

})