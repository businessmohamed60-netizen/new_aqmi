(function(){'use strict';
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
document.addEventListener('DOMContentLoaded',function(){
    setTimeout(function(){
        document.querySelectorAll('.alert-dismissible').forEach(function(a){
            var bs = new bootstrap.Alert(a); bs.close();
        });
    },5000);
});
document.querySelectorAll('a[href^="#"]').forEach(function(a){
    a.addEventListener('click',function(e){
        var t = document.querySelector(this.getAttribute('href'));
        if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'})}
    });
});
document.addEventListener('submit',function(e){
    var f=e.target,b=f.querySelector('button[type="submit"]');
    if(b){setTimeout(function(){b.disabled=true;b.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Chargement...'},10)}
});
document.querySelectorAll('.needs-validation').forEach(function(f){
    f.addEventListener('submit',function(e){if(!f.checkValidity()){e.preventDefault();e.stopPropagation()}f.classList.add('was-validated')});
});
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(e){new bootstrap.Tooltip(e)});
console.log('AQMI App initialized');
})();