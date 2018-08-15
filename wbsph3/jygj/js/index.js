
$(function() {
    var w=$('img').css('width');
    var sw=screen.availWidth;
    w=w.substr(0,w.length-2);
    if(w>sw){
        $('img').css('width',sw+'px');
        $('img').css('height','auto');
    }
})