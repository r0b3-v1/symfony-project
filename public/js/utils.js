const triggers = document.querySelectorAll('[target][collapse-trigger]');

for (const trigger of triggers) {
    trigger.addEventListener('click', function(){
        let targetId = this.getAttribute('target');
        const target = document.querySelector(`#${targetId}[collapsable]`);
        target.classList.toggle('collapsed');
    })
    
}
