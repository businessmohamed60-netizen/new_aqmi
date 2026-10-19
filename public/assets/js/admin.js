(function(){'use strict';
document.querySelectorAll('[data-confirm]').forEach(function(e){
    e.addEventListener('click',function(ev){if(!confirm(this.dataset.confirm||'Êtes-vous sûr?')){ev.preventDefault()}});
});
document.querySelectorAll('.toggle-status').forEach(function(e){
    e.addEventListener('change',function(){
        var id=this.dataset.id,url=this.dataset.url;
        if(id&&url){fetch(url,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=\"csrf-token\"]')?.content}}).then(function(r){return r.json()}).then(function(d){if(d.success)location.reload()})}
    });
});
var searchInput=document.getElementById('tableSearch');
if(searchInput){
    searchInput.addEventListener('keyup',function(){
        var f=this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(function(r){r.style.display=r.textContent.toLowerCase().includes(f)?'':'none'});
    });
}
console.log('AQMI Admin initialized');
})();