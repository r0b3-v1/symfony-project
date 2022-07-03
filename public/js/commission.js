const deadlineCheck = document.getElementById('commission_nodeadline');
const deadlineInput = document.getElementById('commission_deadline').parentElement;

disableToggle();

deadlineCheck.addEventListener('click', function(){
    disableToggle();
})

function disableToggle(){
    if(deadlineCheck.checked){
        deadlineInput.classList.add('deactivated');
    }
    else{
        deadlineInput.classList.remove('deactivated');
    }
}