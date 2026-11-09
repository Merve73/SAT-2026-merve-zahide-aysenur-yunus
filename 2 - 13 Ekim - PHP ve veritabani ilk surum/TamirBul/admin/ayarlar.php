<?php
/*
SAYFA: Site ayarlari
DOSYA: admin/ayarlar.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- site_ayarlari tablosundaki her satir icin bir kutu
- Kaydet deyince her anahtarin degeri UPDATE olur
- Ana sayfa basligi, telefon, adres gibi metinler boyle degisir
- Yeni anahtar eklemek icin SQL dosyasina satir eklenir, bu form onu da kutuda gosterir
*/

$kok = "../";
$sayfa_basligi = "Site ayarları";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

// Anahtarin ekranda okunur adini verir. Tabloda kisa kod durur.
function ayar_etiketi($anahtar) {
    if ($anahtar == "site_adi") {
        return "Site adı";
    }
    if ($anahtar == "telefon") {
        return "Telefon";
    }
    if ($anahtar == "eposta") {
        return "E-posta";
    }
    if ($anahtar == "adres") {
        return "Adres";
    }
    if ($anahtar == "calisma_saatleri") {
        return "Çalışma saatleri";
    }
    if ($anahtar == "anasayfa_baslik") {
        return "Ana sayfa başlığı";
    }
    if ($anahtar == "anasayfa_aciklama") {
        return "Ana sayfa açıklaması";
    }
    if ($anahtar == "adim1_baslik") {
        return "1. adım başlığı";
    }
    if ($anahtar == "adim1_aciklama") {
        return "1. adım açıklaması";
    }
    if ($anahtar == "adim2_baslik") {
        return "2. adım başlığı";
    }
    if ($anahtar == "adim2_aciklama") {
        return "2. adım açıklaması";
    }
    if ($anahtar == "adim3_baslik") {
        return "3. adım başlığı";
    }
    if ($anahtar == "adim3_aciklama") {
        return "3. adım açıklaması";
    }
    return $anahtar;
}

$hata = "";
$tamam = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $veritabani_hazir && isset($_POST["ayar"])) {
    // Her kutu ayar[anahtar] diye gelir. foreach ile tek tek gezeriz.
    foreach ($_POST["ayar"] as $anahtar => $deger) {
        $anahtar_temiz = mysqli_real_escape_string($baglanti, $anahtar);
        $deger_temiz = mysqli_real_escape_string($baglanti, $deger);
        $sorgu = "UPDATE site_ayarlari SET deger = '$deger_temiz' WHERE anahtar = '$anahtar_temiz'";
        $calisti = mysqli_query($baglanti, $sorgu);
        if (!$calisti) {
            $hata = "Bir ayar kaydedilemedi: " . mysqli_error($baglanti);
        }
    }
    if ($hata == "") {
        $tamam = "Ayarlar kaydedildi. Ana sayfayı yenileyince yeni metin görünür.";
    }
}

$ayarlar = false;
if ($veritabani_hazir) {
    $ayarlar = mysqli_query($baglanti, "SELECT * FROM site_ayarlari ORDER BY id");
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Site ayarları</h1>
        <p class="giris-metni">Bu kutulardaki yazılar ana sayfada, menüde ve altlıkta kullanılır.</p>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>
        <?php if ($tamam != "") { ?>
            <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
        <?php } ?>

        <form class="form-kutu" method="post" action="ayarlar.php">
            <?php if ($ayarlar) { ?>
                <?php while ($satir = mysqli_fetch_assoc($ayarlar)) { ?>
                    <label for="ayar_<?php echo temiz_yaz($satir["anahtar"]); ?>"><?php echo temiz_yaz(ayar_etiketi($satir["anahtar"])); ?></label>
                    <input id="ayar_<?php echo temiz_yaz($satir["anahtar"]); ?>" name="ayar[<?php echo temiz_yaz($satir["anahtar"]); ?>]" type="text" value="<?php echo temiz_yaz($satir["deger"]); ?>">
                <?php } ?>
            <?php } ?>
            <p><button class="buton" type="submit">Kaydet</button></p>
        </form>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
