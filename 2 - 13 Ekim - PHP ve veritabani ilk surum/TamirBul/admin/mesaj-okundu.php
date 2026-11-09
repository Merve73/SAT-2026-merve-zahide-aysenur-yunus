<?php
/*
SAYFA: Mesaji okundu yap
DOSYA: admin/mesaj-okundu.php
KIM: Kisi 4

BU SAYFA EKRAN BASMAZ.
- Mesajin okundu sutununu 1 yapar.
*/

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

if ($_SERVER["REQUEST_METHOD"] == "POST" && $veritabani_hazir) {
    $id = 0;
    if (isset($_POST["id"])) {
        $id = (int) $_POST["id"];
    }
    if ($id > 0) {
        mysqli_query($baglanti, "UPDATE mesajlar SET okundu = 1 WHERE id = $id");
    }
}

header("Location: mesajlar.php");
exit;
