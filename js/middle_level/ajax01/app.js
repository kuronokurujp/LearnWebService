$(function() {
    $('.js-formArea').on("submit", function(e){
        e.preventDefault();
        
        $.ajax({
            type: "post",
            /*
            url: "ajax_json.php",
            dataType: "json",
            data: {
                name: $(".js-get-val-name").val(),
                age: $(".js-get-val-age").val()
            }

            */
            url:"ajax_txt.txt",
            dataType: "text",

        }).done(function(data, status) {
            console.log(data);
            console.log(status);

            /*
            // jsonで取得したデータを埋め込む
            // 書式の中に埋め込むので、デザインの管理を埋め込み先でできるのが利点
            var name = data.name;
            var age = data.age;
            $(".js-set-name").text(name);
            $(".js-set-age").text(age);
            //$(".js-set-form").html(data);
            */
           $(".js-set-form").html(data);
        });
    }); 
});
