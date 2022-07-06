<?php

require_once "db.php";

if (!isset($_GET['ilk_id']) || empty($_GET['ilk_id'])) {
    redirectTo404();
} else if (!isset($_GET['sira'])){
    redirectTo404();
} else if (!isset($_GET['kategori_id']) || empty($_GET['kategori_id'])){
    redirectTo404();
}

/*
 * Sira ve Kategori id den makaleyi bul.
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
WHERE posts.id != :ilk_id AND posts.category_id = :kategori_id
LIMIT :sira, 1;
SQL
);

$makaleSorgusu->bindValue(":ilk_id", (int)$_GET["ilk_id"], PDO::PARAM_INT);
$makaleSorgusu->bindValue(":kategori_id", (int)$_GET["kategori_id"], PDO::PARAM_INT);
$makaleSorgusu->bindValue(":sira", (int)$_GET["sira"], PDO::PARAM_INT);

$makaleSorgusu->execute();

$makale = $makaleSorgusu->fetch();

if ($makale) {
    $siraNo = ((int)$_GET["sira"]) + 1;

    echo <<<HTML
<div
    class="makale"
    data-makale-sira="$siraNo"
    data-makale-kategori-id="{$makale["category_id"]}"
    data-makale-url="{$makale["url"]}"
>
    <h1>{$makale["title"]}</h1>
    <div>{$makale["content"]}</div>
</div>
HTML;
} else {
    http_response_code(404);

    echo <<<HTML
        <h1>Bitti</h1>
HTML;
}
