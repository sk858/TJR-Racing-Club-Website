<script>
window.onload = function() {
    $("#year_select").change(function() {
        // START CODE Q5
        var choice = $("#year_select option:selected").val();

        var title = "<option value='0' selected>---</option>";
        var request = $.ajax({
                type: 'POST',
                url:"includes/history_load.php",
                data: ({choice: choice}),
                error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
              }
            });

        request.done(function(data) {
            //console.log(data);
            rawdata = data.split('"],["');

            rawdata.forEach(function(d){
                x = d.split('","');
                name = x[0].split('[["');
                name = name.replace(",","");

                title += "<option value='"+ name + "'>"+name+"</option>";
            })
            $("#title_select").html(title);

        })

    });

    $("#title_select").change(function() {
        var choice = $("#year_select option:selected").val();
        var title = $("#title_select option:selected").val();

        var request = $.ajax({
                type: 'POST',
                url:"includes/history_load.php",
                data: ({choice: choice, title:title}),
                error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
              }
            });

        request.done(function(data) {
            //console.log(data);
            rawdata = data.split('"],["');

            rawdata.forEach(function(d){
                x = d.split('","');
                name = x[1].split('"]]');
                name = name.replace(",","");
                name = name.replace(new RegExp('<br>', 'g'),"&#013;");
            })
            $("#intro_select").html(name);

        })

    });

    $("#photo_select").change(function() {
        // START CODE Q5
        var choice = $("#photo_select option:selected").val();
        $("#view").empty();
        var photo="";

        var request = $.ajax({
                type: 'POST',
                url:"includes/view_load.php",
                data: ({choice: choice}),
                error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
              }
            });

        request.done(function(data) {
            if(data != "[]"){
                rawdata = data.replace('[["',"");
                rawdata = rawdata.replace('"]]',"");
                rawdata = rawdata.replace('\\',"");

                photo += "<div><img src='" +rawdata+"'></div>";
                document.getElementById("view").innerHTML += photo;
            }
        })

    });


}
</script>