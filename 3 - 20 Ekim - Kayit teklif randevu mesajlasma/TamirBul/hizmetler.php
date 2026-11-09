<?php
/*
SAYFA: Hizmetler
DOSYA: hizmetler.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Kategori filtre linkleri (hepsi + her kategori)
- Hizmet kartları: arka plan resmi, kategori, ad, açıklama, fiyat
- aktif = 0 olan hizmetler görünmez
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

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık alanı. -->
        <div class="dashboard-header">
            <!-- Başlık. -->
            <h2>Hizmetlerimiz</h2>
            <!-- Açıklama. -->
            <p><?php echo temiz_yaz($aciklama); ?></p>
        </div>
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
                    <p class="etiket"><?php echo temiz_yaz($hizmet["kategori_adi"]); ?></p>
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

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
