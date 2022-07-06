<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script>
    let makale_yukleniyor = false;

    $.fn.isInViewport = function() {
        var elementTop = $(this).offset().top;
        var elementBottom = elementTop + $(this).innerHeight();

        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + window.innerHeight;

        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

    function loadMore(ilk_id, sira, kategori_id){
        $.ajax({
            url: `makale_getir.php?ilk_id=${ilk_id}&sira=${sira}&kategori_id=${kategori_id}`,
            type: "get",
            beforeSend: function(){
                makale_yukleniyor = true;
                $('.yukleniyor').show();
            }
        }).done(function(data){
            makale_yukleniyor = false;
            $('.yukleniyor').hide()
            $(".makaleler").append(data);
        }).fail(function(){
            makale_yukleniyor = false;
            $('.hata').show()
            $('.yukleniyor').hide()
        })
    }

    $(window).scroll(function() {
        $(".makale").each(function (index, element) {
            if ($(element).isInViewport()) {
                window.history.pushState("", "", $(element).attr("data-makale-url"));
            }
        });

        if(makale_yukleniyor == false && (
            $(window).scrollTop() + window.innerHeight >= document.documentElement.clientHeight
        )) {
            let ilk_makale = $(".makale:first");
            let son_makale = $(".makale:last");

            let ilk_makale_id = ilk_makale.attr("data-makale-id");
            let son_makale_sira = son_makale.attr("data-makale-sira");
            let son_makale_kategori_id = son_makale.attr("data-makale-kategori-id");

            loadMore(ilk_makale_id, son_makale_sira, son_makale_kategori_id);
        }
    });
</script>
<?php

require_once "db.php";

/*
 * Url yoksa sayfayı 404 sayfasına yonlendir.
 */
if (!isset($_GET['url']) || empty($_GET['url'])){
    redirectTo404();
}

/*
 * Urlden makaleyi bul.
 */
$makaleSorgusu = $db->prepare(
<<<SQL
SELECT
  posts.id,
  posts.title,
  posts.content,
  posts.url,
  posts.category_id
FROM posts
WHERE posts.url = :url
SQL
);

$makaleSorgusu->execute(["url" => $_GET["url"]]);

$makale = $makaleSorgusu->fetch();

/*
 * Eğer makale yoksa 404 sayfasında yönlendir.
 */
if ($makale) {
    echo
<<<HTML
<div class="makaleler">
    <div
        class="makale"
        data-makale-id="{$makale["id"]}"
        data-makale-sira="0"
        data-makale-kategori-id="{$makale["category_id"]}"
        data-makale-url="{$makale["url"]}"
    >
        <h1>{$makale["title"]}</h1>
        <div>{$makale["content"]}</div>
    </div>
</div>
<div class="hata" style="display: none">
    <p>Beklenmeyen bir hata olustu.</p>
</div>
<div class="yukleniyor" style="display: none">
    <p>Yükleniyor........</p>
</div>
HTML;
} else {
    redirectTo404();
}