<?php
/*
SAYFA: Hizmetler
DOSYA: hizmetler.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Üstte fotoğraflı kapak: başlık ve sayfa açıklaması (site yazılarından)
- Kategori filtre linkleri (hepsi + her kategori)
- Hizmet kartları: arka plan resmi, kategori, ad, açıklama, fiyat
- aktif = 0 olan hizmetler görünmez
- Fiyatlar nasıl belirlenir: üç kısa bilgi
- En altta "aradığınızı bulamadınız mı" çağrısı
*/

// Sekme başlığı.
$sayfa_basligi = "Hizmetler";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// HARİTA: Hizmet listesi ve kategori filtresi
// Adresteki ?kategori= değeri. Yoksa 0, yani hepsi.
$secilen_kategori = gelen_sayi("kategori");
// Filtre koşulu. Kategori seçilmediyse boş.
$kosul = "";
// Kategori seçildiyse sadece o kategori.
if ($secilen_kategori > 0) {
    // Ek koşul.
    $kosul = "AND hizmetler.kategori_id = $secilen_kategori";
}

// Filtre linkleri için kategoriler.
$kategoriler = satirlar("SELECT * FROM kategoriler ORDER BY id");
// Hizmetler. Kategori resmini de alırız: hizmetin resmi yoksa o kullanılır.
$hizmetler = satirlar("SELECT hizmetler.*, kategoriler.ad AS kategori_adi, kategoriler.resim AS kategori_resmi FROM hizmetler INNER JOIN kategoriler ON kategoriler.id = hizmetler.kategori_id WHERE hizmetler.aktif = 1 $kosul ORDER BY hizmetler.id DESC");
// Sayfa açıklaması (site yazılarından).
$aciklama = ayar_getir("hizmetler_sayfa_aciklama", "");
// Kapak fotoğrafı: elektronik kart üzerinde lehim işi. İnternetten (Unsplash) gelir.
$kapak_resmi = "https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?auto=format&fit=crop&w=1600&q=75";

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa kapağı: arkada fotoğraf, önünde başlık ve açıklama. -->
<section class="sayfa-kapagi" <?php echo arka_plan($kapak_resmi); ?>>
    <!-- Ortadaki yazılar. -->
    <div class="kapsayici sayfa-kapagi-ici">
        <!-- Küçük hap yazı. -->
        <div class="hero-rozet"><?php echo ikon("anahtar"); ?> <span<?php echo yazi_stili("hizmetler_rozet"); ?>><?php echo temiz_yaz(yazi_getir("hizmetler_rozet")); ?></span></div>
        <!-- Başlık. -->
        <h1>Hizmetlerimiz</h1>
        <!-- Açıklama. yazi_stili: yönetimde seçilen stil. -->
        <p<?php echo yazi_stili("hizmetler_sayfa_aciklama"); ?>><?php echo temiz_yaz($aciklama); ?></p>
    </div>
</section>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Filtre linkleri. Seçili olan renkli. -->
        <div class="filtre">
            <!-- Hepsi linki. -->
            <a href="hizmetler.php" <?php if ($secilen_kategori == 0) { echo 'class="secili"'; } ?>>Hepsi</a>
            <!-- Her kategori bir link. -->
            <?php foreach ($kategoriler as $kategori) { ?>
                <!-- Kategori linki. -->
                <a href="hizmetler.php?kategori=<?php echo (int) $kategori["id"]; ?>" <?php if ($secilen_kategori == $kategori["id"]) { echo 'class="secili"'; } ?>><?php echo temiz_yaz($kategori["ad"]); ?></a>
            <?php } ?>
        </div>
        <!-- Kaç hizmet listelendiği. -->
        <p class="liste-sayisi"><?php echo count($hizmetler); ?> hizmet listeleniyor</p>
        <!-- Kart ızgarası. -->
        <div class="kart-alani">
            <!-- Her hizmet bir kart. -->
            <?php foreach ($hizmetler as $hizmet) { ?>
                <?php
                // Kartın resmi: hizmetin resmi, yoksa kategorinin resmi.
                $kart_resmi = $hizmet["resim"];
                // Hizmetin resmi boşsa:
                if ($kart_resmi == "") {
                    // Kategori resmi.
                    $kart_resmi = $hizmet["kategori_resmi"];
                }
                ?>
                <!-- Arkası resimli kart. -->
                <article class="kart resimli-kart" <?php echo arka_plan($kart_resmi); ?>>
                    <!-- Kategori etiketi. -->
                    <p class="etiket"><?php echo kategori_ikonu($hizmet["kategori_adi"]); ?> <?php echo temiz_yaz($hizmet["kategori_adi"]); ?></p>
                    <!-- Hizmet adı. -->
                    <h2><?php echo temiz_yaz($hizmet["ad"]); ?></h2>
                    <!-- Açıklama. -->
                    <p><?php echo temiz_yaz($hizmet["aciklama"]); ?></p>
                    <!-- Fiyat. -->
                    <p class="fiyat"><?php echo fiyat_yaz($hizmet["baslangic_fiyat"]); ?> başlangıç</p>
                    <!-- Detay linki. -->
                    <p><a class="btn btn-sm btn-primary" href="hizmet-detay.php?id=<?php echo (int) $hizmet["id"]; ?>">İncele</a></p>
                </article>
            <?php } ?>
        </div>
        <!-- Hiç hizmet yoksa bilgi. -->
        <?php if (count($hizmetler) == 0) { ?>
            <!-- Bilgi ve talep linki. -->
            <p class="bos-liste">Bu kategoride hizmet yok. Yine de <a href="talep-olustur.php?kategori=<?php echo $secilen_kategori; ?>">talep oluşturabilirsiniz</a>.</p>
        <?php } ?>
    </div>
</section>

<!-- HARİTA: Fiyatlar nasıl belirlenir (hizmetler sayfası) -->
<section class="hakkimizda-bolum zemin-farkli">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title">Fiyatlar nasıl belirlenir?</h2>
        <!-- Üç bilgi yan yana. -->
        <div class="ozellik-izgara uclu">
            <!-- 1. bilgi. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("bilgi"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Başlangıç fiyatı</h3>
                    <!-- Açıklama. -->
                    <p>Kartlardaki fiyat, o işin en düşük ücretidir. Size fikir vermesi için yazılır.</p>
                </div>
            </div>
            <!-- 2. bilgi. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("mesaj"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Tamirci teklifi</h3>
                    <!-- Açıklama. -->
                    <p>Kesin fiyatı; cihazınızın markası, modeli ve arızasına bakan tamirci teklif olarak verir.</p>
                </div>
            </div>
            <!-- 3. bilgi. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("secim"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Seçim sizin</h3>
                    <!-- Açıklama. -->
                    <p>Gelen teklifleri fiyat, süre ve puana göre karşılaştırır, size uygun olanı seçersiniz.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Alt çağrı bölümü. -->
<section class="cagri-bolumu sade">
    <!-- Kapsayıcı. -->
    <div class="kapsayici cagri-ici">
        <!-- Başlık. Yönetim > Site yazıları > Hizmetler sayfası'ndan değişir. -->
        <h2<?php echo yazi_stili("hizmetler_cagri_baslik"); ?>><?php echo temiz_yaz(yazi_getir("hizmetler_cagri_baslik")); ?></h2>
        <!-- Açıklama. -->
        <p<?php echo yazi_stili("hizmetler_cagri_aciklama"); ?>><?php echo temiz_yaz(yazi_getir("hizmetler_cagri_aciklama")); ?></p>
        <!-- İki düğme. -->
        <div class="hero-actions">
            <!-- Talep oluştur. -->
            <a class="btn btn-hero" href="talep-olustur.php">Talep Oluştur</a>
            <!-- Tamirciler. -->
            <a class="btn btn-outline btn-hero" href="tamirciler.php">Tamircileri İncele</a>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
