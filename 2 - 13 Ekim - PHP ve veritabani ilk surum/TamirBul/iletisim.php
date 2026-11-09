<?php
/*
SAYFA: Iletisim
DOSYA: iletisim.php
KIM: Kisi 1 gorunum. Kayit kodu bu dosyada, mantigi Kisi 3 ile ayni insert ornegidir.

BU SAYFADA BUNLAR OLACAK:
- Solda telefon, e-posta, adres, calisma saati (site_ayarlari)
- Sagda iletisim formu: ad, eposta, konu, mesaj
- Form mesajlar tablosuna yazar. Mesaji sadece yonetici gorur.
*/

$kok = "";
$sayfa_basligi = "İletişim";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

$hata = "";
$tamam = "";

// Form sadece POST ile gelince calisir. Sayfa ilk acilisinda bu blok atlanir.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = "";
    $eposta = "";
    $konu = "";
    $mesaj = "";

    if (isset($_POST["ad"])) {
        $ad = trim($_POST["ad"]);
    }
    if (isset($_POST["eposta"])) {
        $eposta = trim($_POST["eposta"]);
    }
    if (isset($_POST["konu"])) {
        $konu = trim($_POST["konu"]);
    }
    if (isset($_POST["mesaj"])) {
        $mesaj = trim($_POST["mesaj"]);
    }

    if (!$veritabani_hazir) {
        $hata = "Veritabanı kapalı, mesaj kaydedilemedi.";
    } else if ($ad == "" || $eposta == "" || $konu == "" || $mesaj == "") {
        $hata = "Bütün kutuları doldurun.";
    } else if (strpos($eposta, "@") === false) {
        $hata = "E-posta adresinde @ olmalı.";
    } else {
        // Tirnak isareti sorguyu bozmasin diye temizleriz.
        $ad_temiz = mysqli_real_escape_string($baglanti, $ad);
        $eposta_temiz = mysqli_real_escape_string($baglanti, $eposta);
        $konu_temiz = mysqli_real_escape_string($baglanti, $konu);
        $mesaj_temiz = mysqli_real_escape_string($baglanti, $mesaj);
        $simdi = date("Y-m-d H:i:s");

        $sorgu = "INSERT INTO mesajlar (ad, eposta, konu, mesaj, okundu, tarih) VALUES ('$ad_temiz', '$eposta_temiz', '$konu_temiz', '$mesaj_temiz', 0, '$simdi')";
        $calisti = mysqli_query($baglanti, $sorgu);

        if ($calisti) {
            $tamam = "Mesajınız alındı. Yönetici panelden okuyacak.";
        } else {
            $hata = "Kayıt olmadı: " . mysqli_error($baglanti);
        }
    }
}

$telefon = ayar_getir($baglanti, "telefon", "0500 000 00 00");
$eposta_ayar = ayar_getir($baglanti, "eposta", "info@tamirbul.test");
$adres = ayar_getir($baglanti, "adres", "Adres girilmedi");
$saat = ayar_getir($baglanti, "calisma_saatleri", "");

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>İletişim</h1>
        <p class="giris-metni">Soldaki bilgiler site ayarlarından gelir. Form mesajlar tablosuna yazılır.</p>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>
        <?php if ($tamam != "") { ?>
            <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
        <?php } ?>

        <div class="yan-yana">
            <!-- Bu bolum: iletisim bilgisi. -->
            <div class="bilgi-kutu">
                <h2>Bize ulaşın</h2>
                <p><strong>Telefon:</strong> <?php echo temiz_yaz($telefon); ?></p>
                <p><strong>E-posta:</strong> <?php echo temiz_yaz($eposta_ayar); ?></p>
                <p><strong>Adres:</strong> <?php echo temiz_yaz($adres); ?></p>
                <p><strong>Saat:</strong> <?php echo temiz_yaz($saat); ?></p>
            </div>

            <!-- Bu bolum: iletisim formu. -->
            <form class="form-kutu" method="post" action="iletisim.php">
                <label for="ad">Adınız</label>
                <input id="ad" name="ad" type="text">

                <label for="eposta">E-posta</label>
                <input id="eposta" name="eposta" type="text">

                <label for="konu">Konu</label>
                <input id="konu" name="konu" type="text">

                <label for="mesaj">Mesaj</label>
                <textarea id="mesaj" name="mesaj"></textarea>

                <p><button class="buton" type="submit">Gönder</button></p>
            </form>
        </div>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
