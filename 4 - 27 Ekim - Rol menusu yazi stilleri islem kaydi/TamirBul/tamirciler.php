<?php
/*
SAYFA: Tamirciler
DOSYA: tamirciler.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Kategori filtre linkleri
- Onaylı tamircilerin kartları: işletme, yetkili, il/ilçe, puan, yetkinlikler
- Kartın arkasında tamircinin ilk yetkinlik kategorisinin resmi
*/

// Sekme başlığı.
$sayfa_basligi = "Tamirciler";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// HARİTA: Tamirci listesi ve filtre
// Adresteki ?kategori= değeri. Yoksa 0, yani hepsi.
$secilen_kategori = gelen_sayi("kategori");
// Filtre koşulu. Boş ise filtre yok.
$kosul = "";
// Kategori seçildiyse:
if ($secilen_kategori > 0) {
    // Alt sorgu: bu kategoride yetkinliği olan tamircilerin numaraları.
    $kosul = "AND id IN (SELECT tamirci_id FROM tamirci_yetkinlikleri WHERE kategori_id = $secilen_kategori)";
}

// Filtre linkleri için kategoriler.
$kategoriler = satirlar("SELECT * FROM kategoriler ORDER BY id");
// Onaylı tamirciler. Alt sorgu: ilk yetkinlik kategorisinin resmi (kart resmi).
$tamirciler = satirlar("SELECT kullanicilar.*, (SELECT kategoriler.resim FROM tamirci_yetkinlikleri INNER JOIN kategoriler ON kategoriler.id = tamirci_yetkinlikleri.kategori_id WHERE tamirci_yetkinlikleri.tamirci_id = kullanicilar.id ORDER BY kategoriler.id LIMIT 1) AS kart_resmi FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 1 $kosul ORDER BY isletme_adi");
// Sayfa açıklaması.
$aciklama = ayar_getir("tamirciler_sayfa_aciklama", "");

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
            <h2>Onaylı Tamirciler</h2>
            <!-- Açıklama. yazi_stili: yönetimde seçilen stil. -->
            <p<?php echo yazi_stili("tamirciler_sayfa_aciklama"); ?>><?php echo temiz_yaz($aciklama); ?></p>
        </div>
        <!-- Filtre linkleri. -->
        <div class="filtre">
            <!-- Hepsi. -->
            <a href="tamirciler.php" <?php if ($secilen_kategori == 0) { echo 'class="secili"'; } ?>>Hepsi</a>
            <!-- Her kategori bir link. -->
            <?php foreach ($kategoriler as $kategori) { ?>
                <!-- Kategori linki. -->
                <a href="tamirciler.php?kategori=<?php echo (int) $kategori["id"]; ?>" <?php if ($secilen_kategori == $kategori["id"]) { echo 'class="secili"'; } ?>><?php echo temiz_yaz($kategori["ad"]); ?></a>
            <?php } ?>
        </div>
        <!-- Kart ızgarası. -->
        <div class="kart-alani">
            <!-- Her tamirci bir kart. -->
            <?php foreach ($tamirciler as $tamirci) { ?>
                <!-- Arkası resimli kart. (string): resim NULL gelirse boş yazı olsun. -->
                <article class="kart resimli-kart" <?php echo arka_plan((string) $tamirci["kart_resmi"]); ?>>
                    <!-- İl / ilçe etiketi. -->
                    <p class="etiket">📍 <?php echo temiz_yaz($tamirci["sehir"] . " / " . $tamirci["ilce"]); ?></p>
                    <!-- İşletme adı. -->
                    <h2><?php echo temiz_yaz($tamirci["isletme_adi"]); ?></h2>
                    <!-- Yetkili. -->
                    <p><strong><?php echo temiz_yaz($tamirci["ad"] . " " . $tamirci["soyad"]); ?></strong></p>
                    <!-- Puan ortalaması. -->
                    <p class="rating"><span class="ikon ikon-nabiz">⭐</span> <?php echo tamirci_puan_metni($tamirci["id"]); ?></p>
                    <!-- Yetkinlikler. -->
                    <p><?php echo temiz_yaz(yetkinlik_metni($tamirci["id"])); ?></p>
                    <!-- Profil linki. -->
                    <p><a class="btn btn-sm btn-primary" href="tamirci-detay.php?id=<?php echo (int) $tamirci["id"]; ?>">Profil</a></p>
                </article>
            <?php } ?>
        </div>
        <!-- Hiç yoksa bilgi. -->
        <?php if (count($tamirciler) == 0) { ?>
            <!-- Bilgi. -->
            <p class="bos-liste">Bu kategoride onaylı tamirci yok.</p>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
