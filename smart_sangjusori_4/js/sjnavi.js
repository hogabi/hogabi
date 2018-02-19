function activeGNB(id,li_id) {

    for(num=1; num<=2; num++) {
        document.getElementById('sjnavi'+num).style.display='none';
        
    }

    for(num=1; num<=3; num++) {

        document.getElementById('sjnavili'+num).style.color='#000';  

	
	}
	
	sessionStorage.setItem('sjidsave', id);
    sessionStorage.setItem('sjliidsave', li_id);
	
    document.getElementById(li_id).style.color='#389bbc'; //해당 ID만 보임
    document.getElementById(id).style.display='block'; //해당 ID만 보임
    
    
}


window.addEventListener('DOMContentLoaded', function(){
    if (sessionStorage.getItem('sjidsave')) {
            saveId = sessionStorage.getItem('sjidsave');
            saveLiId = sessionStorage.getItem('sjliidsave');

          activeGNB (saveId, saveLiId);

    } else {

    }

})
