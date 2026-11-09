<?php
/*
SAYFA: Ana sayfa
DOSYA: index.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK (hepsi yönetim panelinden değişir):
- Üstte sırayla değişen büyük resimler (admin/slaytlar.php)
- Büyük başlık, açıklama, iki düğme (admin/ayarlar.php)
- Nasıl çalışır kartları (admin/adimlar.php)
- Kategori kartları (admin/kategoriler.php)
- Son hizmetler (admin/hizmetler.php)
*/

// Tarayıcı sekmesindeki başlık.
$sayfa_basligi = "Ana sayfa";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Büyük başlık.
$anasayfa_baslik = ayar_getir("anasayfa_baslik", "Cihazınız mı bozuldu?");
// Başlığın altındaki açıklama.
$anasayfa_aciklama = ayar_getir("anasayfa_aciklama", "");
// Birinci düğmenin yazısı.
$anasayfa_buton = ayar_getir("anasayfa_buton", "Tamir Talebi Oluştur");
// İkinci düğmenin yazısı.
$anasayfa_buton2 = ayar_getir("anasayfa_buton2", "Talebimi Sorgula");
// Slaytın kaç saniyede değişeceği.
$slayt_suresi = (int) ayar_getir("slayt_suresi", "5");
// Adımlar bölümünün başlığı.
$adimlar_baslik = ayar_getir("adimlar_baslik", "Nasıl Çalışır?");
// Kategoriler bölümünün başlığı.
$kategoriler_baslik = ayar_getir("kategoriler_baslik", "Kategoriler");
// Hizmetler bölümünün başlığı.
$hizmetler_baslik = ayar_getir("hizmetler_baslik", "Hizmetler");
// Kaç hizmet gösterilecek.
$hizmet_sayisi = (int) ayar_getir("anasayfa_hizmet_sayisi", "3");
// Yönetici yazı veya 0 yazdıysa 3 kullanırız. Eksi sayı sorguyu bozardı.
if ($hizmet_sayisi < 1) {
    // Varsayılan.
    $hizmet_sayisi = 3;
}

// HARİTA: Ana sayfa slaytları (PHP tarafı)
// Slayt resimleri, sıra numarasına göre.
$slaytlar = satirlar("SELECT * FROM slaytlar ORDER BY sira, id");
// Nasıl çalışır adımları, sıra numarasına göre.
$adimlar = satirlar("SELECT * FROM adimlar ORDER BY sira, id");
// HARİTA: Ana sayfa kategori ve hizmet kartları
// Kategoriler.
$kategoriler = satirlar("SELECT * FROM kategoriler ORDER BY id");
// Son eklenen hizmetler. Kategori resmini de alırız: hizmetin resmi yoksa o kullanılır.
$hizmetler = satirlar("SELECT hizmetler.*, kategoriler.ad AS kategori_adi, kategoriler.resim AS kategori_resmi FROM hizmetler INNER JOIN kategoriler ON kategoriler.id = hizmetler.kategori_id WHERE hizmetler.aktif = 1 ORDER BY hizmetler.id DESC LIMIT $hizmet_sayisi");

// Üst menüyü yazarız.
include "ortak/baslik.php";
?>

<!-- Karşılama bölümü: arkada değişen resimler, önde başlık. -->
<section class="hero-section">
    <!-- Slayt resimleri. data-süre: js kaç saniyede değiştireceğini buradan okur. -->
    <div id="slayt-alani" class="hero-bg-slider" data-sure="<?php echo $slayt_suresi; ?>">
        <!-- Her slayt bir resim. İlki (sıra 0) "aktif" başlar, hemen görünür. -->
        <?php foreach ($slaytlar as $sira => $slayt) { ?>
            <!-- Resim. -->
            <img class="slayt <?php if ($sira == 0) { echo "aktif"; } ?>" src="<?php echo temiz_yaz(resim_adresi($slayt["resim"])); ?>" alt="<?php echo temiz_yaz($slayt["aciklama"]); ?>">
        <?php } ?>
    </div>
    <!-- Resmin üstündeki koyu perde, yazı okunsun diye. -->
    <div class="hero-overlay"></div>
    <!-- Ortadaki yazılar. -->
    <div class="container hero-container-center">
        <!-- Yazı kutusu. -->
        <div class="hero-content">
            <!-- Büyük başlık. -->
            <h2><?php echo temiz_yaz($anasayfa_baslik); ?></h2>
            <!-- Açıklama. -->
            <p><?php echo temiz_yaz($anasayfa_aciklama); ?></p>
            <!-- İki düğme. -->
            <div class="hero-actions">
                <!-- Talep oluştur düğmesi. -->
                <a class="btn btn-hero" href="talep-olustur.php"><?php echo temiz_yaz($anasayfa_buton); ?></a>
                <!-- Talep sorgula düğmesi. -->
                <a class="btn btn-outline btn-hero" href="talep-takip.php"><?php echo temiz_yaz($anasayfa_buton2); ?></a>
            </div>
        </div>
    </div>
</section>

<!-- Nasıl çalışır bölümü. -->
<section id="nasil-calisir" class="how-it-works-section">
    <!-- Ortalanmış içerik. -->
    <div class="container">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title"><?php echo temiz_yaz($adimlar_baslik); ?></h2>
        <!-- Kart ızgarası. -->
        <div class="steps-grid">
            <!-- Her adım bir kart. $sira 0'dan başlar, ekranda +1 yazarız. -->
            <?php foreach ($adimlar as $sira => $adim) { ?>
                <!-- Arkası resimli kart. arka_plan() style="background-image..." yazar. -->
                <div class="step-card resimli-kart" <?php echo arka_plan($adim["resim"]); ?>>
                    <!-- Adım numarası. -->
                    <div class="step-number"><?php echo $sira + 1; ?></div>
                    <!-- Adım başlığı. -->
                    <h3><?php echo temiz_yaz($adim["baslik"]); ?></h3>
                    <!-- Adım açıklaması. -->
                    <p><?php echo temiz_yaz($adim["aciklama"]); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Kategoriler bölümü. -->
<section id="hizmetler" class="categories-section">
    <!-- Ortalanmış içerik. -->
    <div class="container">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title"><?php echo temiz_yaz($kategoriler_baslik); ?></h2>
        <!-- Kart ızgarası. -->
        <div class="categories-grid">
            <!-- Her kategori bir kart. -->
            <?php foreach ($kategoriler as $kategori) { ?>
                <!-- Kartın tamamı link. Talep formunu bu kategori seçili açar. -->
                <a class="category-card-link" href="talep-olustur.php?kategori=<?php echo (int) $kategori["id"]; ?>">
                    <!-- Arkası resimli kart. -->
                    <div class="category-card resimli-kart" <?php echo arka_plan($kategori["resim"]); ?>>
                        <!-- Kategori adı. -->
                        <h3><?php echo temiz_yaz($kategori["ad"]); ?></h3>
                        <!-- Kategori açıklaması. -->
                        <p><?php echo temiz_yaz($kategori["aciklama"]); ?></p>
                        <!-- Alt yazı. -->
                        <span class="card-action-text">Talep Oluştur →</span>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Son hizmetler bölümü. -->
<section class="how-it-works-section">
    <!-- Ortalanmış içerik. -->
    <div class="container">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title"><?php echo temiz_yaz($hizmetler_baslik); ?></h2>
        <!-- Kart ızgarası. -->
        <div class="kart-alani">
            <!-- Her hizmet bir kart. -->
            <?php foreach ($hizmetler as $hizmet) { ?>
                <?php
                // Kartın resmi: hizmetin kendi resmi.
                $kart_resmi = $hizmet["resim"];
                // Hizmetin resmi yoksa kategorinin resmi.
                if ($kart_resmi == "") {
                    // Kategori resmi.
                    $kart_resmi = $hizmet["kategori_resmi"];
                }
                ?>
                <!-- Arkası resimli kart. -->
                <article class="kart resimli-kart" <?php echo arka_plan($kart_resmi); ?>>
                    <!-- Kategori etiketi. -->
                    <p class="etiket"><?php echo temiz_yaz($hizmet["kategori_adi"]); ?></p>
                    <!-- Hizmet adı. -->
                    <h2><?php echo temiz_yaz($hizmet["ad"]); ?></h2>
                    <!-- Açıklama. -->
                    <p><?php echo temiz_yaz($hizmet["aciklama"]); ?></p>
                    <!-- Başlangıç fiyatı. -->
                    <p class="fiyat"><?php echo fiyat_yaz($hizmet["baslangic_fiyat"]); ?> başlangıç</p>
                    <!-- Detay linki. -->
                    <p><a class="btn btn-sm btn-primary" href="hizmet-detay.php?id=<?php echo (int) $hizmet["id"]; ?>">İncele</a></p>
                </article>
            <?php } ?>
        </div>
        <!-- Tüm hizmetler linki. -->
        <p class="section-link"><a class="btn btn-outline" href="hizmetler.php">Tüm hizmetler</a></p>
    </div>
</section>

<?php
// Sayfa altını yazarız.
include "ortak/altlik.php";
?>
