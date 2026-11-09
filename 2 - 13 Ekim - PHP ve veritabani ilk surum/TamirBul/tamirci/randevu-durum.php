<?php
/*
SAYFA: Randevu durum kaydi
DOSYA: tamirci/randevu-durum.php
KIM: Kisi 2 bu dosyayi tamirci paneliyle birlikte anlatir.

BU SAYFA EKRAN BASMAZ.
- POST ile gelen randevu id ve yeni durumu alir
- Sadece bu tamircinin kendi randevusunu gunceller
- Tamamlandi ise bagli talebi de tamamlandi yapar
*/

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_tamirci();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: randevularim.php");
    exit;
}

$id = 0;
$durum = "";

if (isset($_POST["id"])) {
    $id = (int) $_POST["id"];
}
if (isset($_POST["durum"])) {
    $durum = $_POST["durum"];
}

// Formdan baska bir yazi gelirse kabul etmeyiz.
if ($durum != "tamamlandi" && $durum != "iptal") {
    header("Location: randevularim.php");
    exit;
}

$benim_id = (int) $_SESSION["kullanici_id"];

if ($veritabani_hazir && $id > 0) {
    // tamirci_id kosulu: baskasinin randevusunu degistiremesin.
    $guncelle = mysqli_query($baglanti, "UPDATE randevular SET durum = '$durum' WHERE id = $id AND tamirci_id = $benim_id");

    if ($guncelle && $durum == "tamamlandi") {
        $bul = mysqli_query($baglanti, "SELECT talep_id FROM randevular WHERE id = $id AND tamirci_id = $benim_id LIMIT 1");
        if ($bul && mysqli_num_rows($bul) == 1) {
            $satir = mysqli_fetch_assoc($bul);
            $talep_id = (int) $satir["talep_id"];
            mysqli_query($baglanti, "UPDATE talepler SET durum = 'tamamlandi' WHERE id = $talep_id AND tamirci_id = $benim_id");
        }
    }
}

header("Location: randevularim.php");
exit;
