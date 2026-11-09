<?php
/*
SAYFA: Ana sayfa
DOSYA: index.php
KIM: Kisi 1 (gorunum). Metinler veritabanindan geldigi icin Kisi 4 ayarlar ekranini bilir.

BU SAYFADA BUNLAR OLACAK:
- Buyuk karsilama basligi ve aciklamasi (site_ayarlari)
- Talep olustur dugmesi
- Uc adim kutusu (site_ayarlari)
- Uc cihaz turu linki: bilgisayar, telefon, tablet
- Son eklenen hizmet kartlari (hizmetler tablosu)
*/

// Ana klasorde oldugumuz icin css yolu ek bir klasor istemez.
$kok = "";
$sayfa_basligi = "Ana sayfa";

// Once veritabani, sonra oturum. Menu bu ikisine ihtiyac duyar.
include "ortak/baglanti.php";
include "ortak/guvenlik.php";

// Karsilama metinleri. Veritabani kapaliysa ikinci parametredeki yedek yazi gorunur.
$anasayfa_baslik = ayar_getir($baglanti, "anasayfa_baslik", "Telefon, tablet ve bilgisayar tamiri için tamirci bul");
$anasayfa_aciklama = ayar_getir($baglanti, "anasayfa_aciklama", "Arızayı yazın, tamirci randevu önersin.");
$adim1_baslik = ayar_getir($baglanti, "adim1_baslik", "Arızayı anlatın");
$adim1_aciklama = ayar_getir($baglanti, "adim1_aciklama", "Marka, model ve arızayı yazın.");
$adim2_baslik = ayar_getir($baglanti, "adim2_baslik", "Tamirci üstlensin");
$adim2_aciklama = ayar_getir($baglanti, "adim2_aciklama", "Onaylı tamirci gün önerir.");
$adim3_baslik = ayar_getir($baglanti, "adim3_baslik", "Randevuya gidin");
$adim3_aciklama = ayar_getir($baglanti, "adim3_aciklama", "Takip kodu ile durumu sorun.");

// Son 3 aktif hizmet. Yeni eklenen ustte kalsin diye id'ye gore tersten siralariz.
$hizmetler = false;
if ($veritabani_hazir) {
    $sorgu = "SELECT * FROM hizmetler WHERE aktif = 1 ORDER BY id DESC LIMIT 3";
    $hizmetler = mysqli_query($baglanti, $sorgu);
}

include "ortak/baslik.php";
?>

<!-- Bu bolum: karsilama. Baslik ve aciklama site_ayarlari tablosundan gelir. -->
<section class="karsilama">
    <div class="kapsayici">
        <h1><?php echo temiz_yaz($anasayfa_baslik); ?></h1>
        <p><?php echo temiz_yaz($anasayfa_aciklama); ?></p>
        <a class="buton" href="talep-olustur.php">Tamir talebi oluştur</a>
        <a class="buton-ikincil" href="hizmetler.php">Hizmetlere bak</a>
    </div>
</section>

<!-- Bu bolum: isin 3 adimi. Yazilar yine site_ayarlari tablosundan. -->
<section>
    <div class="kapsayici">
        <h2>Nasıl çalışır?</h2>
        <div class="kart-alani">
            <article class="kart">
                <p class="etiket">1</p>
                <h2><?php echo temiz_yaz($adim1_baslik); ?></h2>
                <p><?php echo temiz_yaz($adim1_aciklama); ?></p>
            </article>
            <article class="kart">
                <p class="etiket">2</p>
                <h2><?php echo temiz_yaz($adim2_baslik); ?></h2>
                <p><?php echo temiz_yaz($adim2_aciklama); ?></p>
            </article>
            <article class="kart">
                <p class="etiket">3</p>
                <h2><?php echo temiz_yaz($adim3_baslik); ?></h2>
                <p><?php echo temiz_yaz($adim3_aciklama); ?></p>
            </article>
        </div>
    </div>
</section>

<!-- Bu bolum: uc cihaz turu. Link hizmetler sayfasina tur filtresi tasir. -->
<section>
    <div class="kapsayici">
        <h2>Hangi cihaz?</h2>
        <div class="kart-alani">
            <a class="tur-kutu" href="hizmetler.php?tur=bilgisayar">
                <h2>Bilgisayar</h2>
                <p>Format, ısınma, yavaşlama ve donanım bakımı.</p>
            </a>
            <a class="tur-kutu" href="hizmetler.php?tur=telefon">
                <h2>Telefon</h2>
                <p>Ekran, batarya ve şarj sorunları.</p>
            </a>
            <a class="tur-kutu" href="hizmetler.php?tur=tablet">
                <h2>Tablet</h2>
                <p>Ekran ve şarj soketi tamiri.</p>
            </a>
        </div>
    </div>
</section>

<!-- Bu bolum: veritabanindan gelen son hizmetler. -->
<section>
    <div class="kapsayici">
        <h2>Hizmetler</h2>
        <div class="kart-alani">
            <?php if ($hizmetler) { ?>
                <?php while ($satir = mysqli_fetch_assoc($hizmetler)) { ?>
                    <article class="kart">
                        <p class="etiket"><?php echo temiz_yaz(cihaz_adi($satir["cihaz_turu"])); ?></p>
                        <h2><?php echo temiz_yaz($satir["ad"]); ?></h2>
                        <p><?php echo temiz_yaz($satir["aciklama"]); ?></p>
                        <p class="fiyat"><?php echo fiyat_yaz($satir["baslangic_fiyat"]); ?> başlangıç</p>
                        <a class="buton" href="hizmet-detay.php?id=<?php echo (int) $satir["id"]; ?>">İncele</a>
                    </article>
                <?php } ?>
            <?php } else { ?>
                <p>Hizmet listesi veritabanı açılınca burada görünecek.</p>
            <?php } ?>
        </div>
        <p><a href="hizmetler.php">Tüm hizmetler</a></p>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
