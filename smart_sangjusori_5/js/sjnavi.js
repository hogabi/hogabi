
jQuery(function ($) {

    $(document).ready(function () {

      // 2차 메뉴가 계속 노출 될 수 있도록 마지막에 선택한 1차메뉴를 보관한 값을 이용하여
      // activeGNB를 호출토록 함.
      $(function() {
        if (sessionStorage.getItem('sjidsave')) {
                saveId = sessionStorage.getItem('sjidsave');
                saveLiId = sessionStorage.getItem('sjliidsave');

              // console.log ('load saveId --> '+ saveId);
              // console.log ('load saveLiId --> '+ saveLiId);

              activeGNB (saveId, saveLiId);

        } else {
            activeGNB ('sjnavi1','sjnavili1');
            // console.log ('sessionStorage Not Save');
        }
    	});



    });

});

function activeGNB(id, li_id) {

    // console.log ('activeGNB id --> ' + id);
    // console.log ('activeGNB li_id --> ' + li_id);

    var procId = '#'+id;
    var procLiId = '#'+li_id;

    jQuery(".sjTopMenu").css("display","none");
    jQuery(".sjTopMenuA").css("color","#000");

    jQuery(procId).css("display","block");
    jQuery(procLiId).css("color","#389bbc");

    sessionStorage.setItem('sjidsave', id);
    sessionStorage.setItem('sjliidsave', li_id);

}
