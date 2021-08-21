function getfilter(){
    var optOrNo = $('#u1_input').val();
    var minPrice = $('#u3_input').val();
    var maxPrice = $('#u5_input').val();
    var moreOrLess = $('#u7_input').val();
    var units = $('#u9_input').val();
    
    $.ajax({
        method: "POST",
        url: "filter.php",
        data: {
            opt:optOrNo,
            min:minPrice,
            max:maxPrice,
            more:moreOrLess,
            count:units 
        }
    }).success(function(result){
            $("#u11").html( result );
        })
};