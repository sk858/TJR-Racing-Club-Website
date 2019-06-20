
<script>
window.onload = function() {

    var photo = "";
    $('#loader').show(); 
    var request = $.ajax({
            type: 'POST',
            url:"includes/photo_load.php",
            data: ({choice: 0}),
            error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
        });

    var login= "<?php echo isset($_SESSION['logged_user_by_sql']);?>";
    console.log(login);

    request.done(function(data) {
        console.log(data);

        rawdata = data.split('"],["');

        rawdata.forEach(function(d){
            x = d.split('","');
            album = x[0].split('[["');
            if(album.length>1) album = album[1];
            id = x[1];
            name = x[2];
            url = x[3].split('"]]');
            url = url[0].replace("\\","");

            console.log(url);

            photo += "<a href='"+url+"' data-lightbox='lb1' class='jlr_item'><img src='" + url +"' title = '"+name+"' class='jlr_img'></a>";
        })

        document.getElementById("jLightroom").innerHTML += photo;
    });

    $("#type_select").change(function() {
        var choice = $("#type_select option:selected").val();

        $("#jLightroom").empty();

        var photo = "";
        var request = $.ajax({
                type: 'POST',
                url:"includes/photo_load.php",
                data: ({choice: choice}),
                error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
              }
            });

        request.done(function(data) {
            if(data!="[]"){
                rawdata = data.replace(new RegExp('"', 'g'),"");
                rawdata = rawdata.split('],[');
                rawdata.forEach(function(d){
                    x = d.split(',');
                    album = x[0].split('[[');
                    if(album.length>1) album = album[1];
                    id = x[1];
                    name = x[2];
                    url = x[3].split(']]');
                    url = url[0].replace("\\","");

                    if(choice == 0 || album == choice){
                        photo += "<a href='"+url+"' data-lightbox='lb1' class='jlr_item'><img src='" + url +"' title = '"+name+"' class='jlr_img'></a>";
                    }
                })
            }
            document.getElementById("jLightroom").innerHTML += photo;
        })
    });

    $( document ).ajaxComplete(function () {
    	var c = $(document).ready(function (){
    	    $("#jLightroom").lightroom({
     	           image_container_selector: ".jlr_item",
        	        img_selector: "img.jlr_img",
            	    img_class_loaded: "jlr_loaded",
                	img_space: 5,
               		img_mode: 'min',
                	init_callback: function(elem){$(elem).removeClass("gray_out"); $('#loader').hide();}
            	}).init();

        	var $gallery = $('#jLightroom a').simpleLightbox();
        	
});

    });

}
</script>


