<script>
window.onload = function() {

    var history = "";
    var request = $.ajax({
            type: 'POST',
            url:"includes/history_load.php",
            data: ({choice: 0}),
            error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
        });

    request.done(function(data) {
        console.log(data);
        rawdata = data.split('"],["');
        rawdata.forEach(function(d){
            x = d.split('","');
            name = x[0].split('[["');
            name = name.replace(",","");
            intro = x[1].split('"]]');
            intro = intro[0];

            history += "<label>"+ name + "</label><p>"+intro+"</p>";
        })

        document.getElementById("content").innerHTML += history;
    });

    var photo = "";
    var request = $.ajax({
            type: 'POST',
            url:"includes/history_pic_load.php",
            data: ({choice: 0}),
            error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
        });


    request.done(function(data) {

        rawdata = data.split('"],["');
        rawdata.forEach(function(d){
            x = d.split('","');
            url = x[1].split('"]]');
            url = url[0].replace("\\","");
            console.log(url);

            photo += "<div><img src='" +url+"'></div>";
        })

        document.getElementById("pic").innerHTML += photo;
    });

    $('#year').on('click','input',function(){
        var year = this.value;
        $("#content").empty();
        $("#pic").empty();

        var history = "";
        var request = $.ajax({
                type: 'POST',
                url:"includes/history_load.php",
                data: ({choice: year}),
                error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
              }
            });

        request.done(function(data) {
            //console.log(data);
            if(data!="[]"){
                rawdata = data.split('"],["');
                rawdata.forEach(function(d){
                    x = d.split('","');
                    name = x[0].split('[["');
                    name = name.replace(",","");
                    intro = x[1].split('"]]');
                    intro = intro[0];

                    history += "<label>"+ name + "</label><p>"+intro+"</p>";
                })
            }

            document.getElementById("content").innerHTML += history;
        })

        var photo = "";
        var request = $.ajax({
                type: 'POST',
                url:"includes/history_pic_load.php",
                data: ({choice: year}),
                error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
              }
            });


        request.done(function(data) {
            if(data!="[]"){
                console.log(data);
                rawdata = data.split('"],[');
                rawdata.forEach(function(d){
                    x = d.split(',"');
                    url = x[1].split('"]]');
                    url = url[0].replace("\\","");
                    console.log(url);

                    photo += "<div><img src='" +url+"' max-width='100%' max-height='100%'></div>";
                })
            }

            document.getElementById("pic").innerHTML += photo;
        });
    });

    $( document ).ajaxComplete(function () {
        $("#pic > div:gt(0)").hide();

        setInterval(function() {
          $('#pic > div:first')
            .fadeOut(1000)
            .next()
            .fadeIn(1000)
            .end()
            .appendTo('#pic');
        }, 10000);
    });

}
</script>