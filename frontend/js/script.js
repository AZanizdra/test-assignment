$(function() {

   /// $( "#search-date" )

    $( "#search-date" ).datepicker({
        dateFormat: "yy-mm-dd",
        minDate: new Date()
    }).datepicker("setDate", new Date());

    var url = '/backend/us_2.php'
    $( "#search-btn" ).click(function(event) {
        var searchDate = $( "#search-date").val();
        $('#content-comedy').html('');
        $('#content-drama').html('');
        $('#content-musical').html('');
        $.ajax({
            type: "POST",
            url: url,
            data: {search: searchDate},
            success: function(data, status, jqXHR ){

                console.log('result',data.data);
                //arr = data.data;
                Object.keys(data.data).map(function(objectKey, index) {
                    var value = data.data[objectKey];
                    var content ='';
                        Object.keys(value).map(function(valueKey, valueIndex) {
                            var item = value[valueKey];
                            content = content+'<tr>'+ '<td>'+item['title']+'</td>'
                            +'<td>'+item['numberTicketsLeft']+'</td>'
                            +'<td>'+item['numberTicketsAvailable']+'</td>'
                            +'<td>'+item['state']+'</td>'
                            +'<td>'+item['price']+'</td>'
                            +'</tr>';


                        });
                    console.log('content'+objectKey.toLowerCase());
                    $('#content-'+objectKey.toLowerCase()).html(content);
                });
            },
            dataType: 'json'
        });
        console.log(event);
    });



});