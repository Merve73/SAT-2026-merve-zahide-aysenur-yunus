<?php
/*
SAYFA: Hizmet ekle
DOSYA: admin/hizmet-ekle.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- Ad, cihaz turu, aciklama, baslangic fiyati, aktif kutusu
- Kayit hizmetler tablosuna INSERT ile yazilir
*/

$kok = "../";
$sayfa_basligi = "Hizmet ekle";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = "";
    $cihaz_turu = "";
    $aciklama = "";
    $fiyat = 0;
    $aktif = 0;

    if (isset($_POST["ad"])) {
        $ad = trim($_POST["ad"]);
    }
    if (isset($_POST["cihaz_turu"])) {
        $cihaz_turu = $_POST["cihaz_turu"];
    }
    if (isset($_POST["aciklama"])) {
        $aciklama = trim($_POST["aciklama"]);
    }
    if (isset($_POST["baslangic_fiyat"])) {
        $fiyat = (float) $_POST["baslangic_fiyat"];
    }
    if (isset($_POST["aktif"])) {
        $aktif = 1;
    }

    if ($cihaz_turu != "bilgisayar" && $cihaz_turu != "telefon" && $cihaz_turu != "tablet") {
        $hata = "Cihaz türü seçin.";
    } else if ($ad == "" || $aciklama == "") {
        $hata = "Ad ve açıklama boş bırakılamaz.";
    } else if ($fiyat < 0) {
        $hata = "Fiyat eksi olamaz.";
    } else if (!$veritabani_hazir) {
        $hata = "Veritabanı kapalı.";
    } else {
        $ad_temiz = mysqli_real_escape_string($baglanti, $ad);
        $aciklama_temiz = mysqli_real_escape_string($baglanti, $aciklama);
        $sorgu = "INSERT INTO hizmetler (ad, cihaz_turu, aciklama, baslangic_fiyat, aktif) VALUES ('$ad_temiz', '$cihaz_turu', '$aciklama_temiz', $fiyat, $aktif)";
        $calisti = mysqli_query($baglanti, $sorgu);
        if ($calisti) {
            header("Location: hizmetler.php");
            exit;
        } else {
            $hata = "Kayıt olmadı: " . mysqli_error($baglanti);
        }
    }
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Yeni hizmet</h1>
        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>
        <form class="form-kutu" method="post" action="hizmet-ekle.php">
            <label for="ad">Ad</label>
            <input id="ad" name="ad" type="text">

            <label for="cihaz_turu">Cihaz türü</label>
            <select id="cihaz_turu" name="cihaz_turu">
                <option value="bilgisayar">Bilgisayar</option>
                <option value="telefon">Telefon</option>
                <option value="tablet">Tablet</option>
            </select>

            <label for="aciklama">Açıklama</label>
            <textarea id="aciklama" name="aciklama"></textarea>

            <label for="baslangic_fiyat">Başlangıç fiyatı (TL)</label>
            <input id="baslangic_fiyat" name="baslangic_fiyat" type="number" min="0" step="1">

            <label><input type="checkbox" name="aktif" checked> Sitede görünsün</label>

            <p><button class="buton" type="submit">Kaydet</button></p>
        </form>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
