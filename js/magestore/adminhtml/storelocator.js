function hideCloseDate(element){
    $$('#'+element.up('.fieldset').id+' tr').each(function(el,index){
        if(index>0 && element.value == 2 ){
            el.hide();
        }else el.show();
    });
}
function verifiTime(element){
    var hour = 0;
    var time = 0;
    $$('#'+element.up('.fieldset').id+' tr').each(function(el,index){
        if(index>0){
            updateTimeOption(el,hour, time);
            hour = el.select('select')[0].value;
            time = el.select('select')[1].value;
        }
    });
}
function updateTimeOption(element,hourValue, minuteValue){
    createTimeOption(element.select('select')[0],hourValue,23);
    if(element.select('select')[0].value == hourValue)
        createTimeOption(element.select('select')[1],minuteValue,59);
    else createTimeOption(element.select('select')[1],0,59);
}
function createTimeOption(element,start,end){
    if(start=='')
        start=0;
    var optionStr = '';
    var selected = (Number(element.value) < Number(start))?start:element.value;
    for(var i = start; i<= end; i++){
        optionStr += '<option value="'+i+'">'+getHour(i)+'</option>';
    }
    element.innerHTML = optionStr;
    element.value = Number(selected);
}
function getHour(i){
    if(i<10)
       return "0"+i;
    return i;
}
document.observe('dom:loaded', function() {
    $$('.hour_open,.minute_open,.hour_open_break,.minute_open_break,.hour_close_break,.minute_close_break,.hour_close,.minute_close').each(function(el){
        verifiTime(el);
        el.observe('change',function(){
            verifiTime(this);
        });
    });
    $$('.status_day').each(function(el){
        hideCloseDate(el);
        el.observe('change',function(){
            hideCloseDate(this);
        });
    });
});
