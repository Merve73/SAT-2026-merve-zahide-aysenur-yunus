<?php
/*
SAYFA: Tamirciler
DOSYA: tamirciler.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Üstte fotoğraflı kapak: başlık ve sayfa açıklaması (site yazılarından)
- Kategori filtre linkleri
- Onaylı tamircilerin kartları: işletme, yetkili, il/ilçe, puan, yetkinlikler
- Kartın arkasında tamircinin ilk yetkinlik kategorisinin resmi
- Tamirciler nasıl seçiliyor: üç adım
- En altta "tamirci misiniz, aramıza katılın" çağrısı
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
// Kapak fotoğrafı: tezgâhında çalışan bir usta. İnternetten (Unsplash) gelir.
$kapak_resmi = "https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?auto=format&fit=crop&w=1600&q=75";

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa kapağı: arkada fotoğraf, önünde başlık ve açıklama. -->
<section class="sayfa-kapagi" <?php echo arka_plan($kapak_resmi); ?>>
    <!-- Ortadaki yazılar. -->
    <div class="kapsayici sayfa-kapagi-ici">
        <!-- Küçük hap yazı. -->
        <div class="hero-rozet"><?php echo ikon("kalkan"); ?> <span<?php echo yazi_stili("tamirciler_rozet"); ?>><?php echo temiz_yaz(yazi_getir("tamirciler_rozet")); ?></span></div>
        <!-- Başlık. -->
        <h1>Onaylı Tamirciler</h1>
        <!-- Açıklama. yazi_stili: yönetimde seçilen stil. -->
        <p<?php echo yazi_stili("tamirciler_sayfa_aciklama"); ?>><?php echo temiz_yaz($aciklama); ?></p>
    </div>
</section>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
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
        <!-- Kaç tamirci listelendiği. -->
        <p class="liste-sayisi"><?php echo count($tamirciler); ?> onaylı tamirci listeleniyor</p>
        <!-- Kart ızgarası. -->
        <div class="kart-alani">
            <!-- Her tamirci bir kart. -->
            <?php foreach ($tamirciler as $tamirci) { ?>
                <!-- Arkası resimli kart. (string): resim NULL gelirse boş yazı olsun. -->
                <article class="kart resimli-kart" <?php echo arka_plan((string) $tamirci["kart_resmi"]); ?>>
                    <!-- İl / ilçe etiketi. -->
                    <p class="etiket"><?php echo ikon("konum"); ?> <?php echo temiz_yaz($tamirci["sehir"] . " / " . $tamirci["ilce"]); ?></p>
                    <!-- İşletme adı. -->
                    <h2><?php echo temiz_yaz($tamirci["isletme_adi"]); ?></h2>
                    <!-- Yetkili. -->
                    <p><strong><?php echo temiz_yaz($tamirci["ad"] . " " . $tamirci["soyad"]); ?></strong></p>
                    <!-- Puan ortalaması. -->
                    <p class="rating"><?php echo ikon("yildiz"); ?> <?php echo tamirci_puan_metni($tamirci["id"]); ?></p>
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
            <p class="bos-liste">Bu kategoride henüz onaylı tamirci yok. Talebinizi yine de açabilirsiniz, yeni katılan tamirciler de görür.</p>
        <?php } ?>
    </div>
</section>

<!-- HARİTA: Tamirciler nasıl seçiliyor -->
<section class="hakkimizda-bolum zemin-farkli">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title">Tamircilerimiz nasıl seçiliyor?</h2>
        <!-- Üç adım yan yana. -->
        <ol class="adim-cizgisi">
            <!-- 1. adım. -->
            <li>
                <!-- Numara. -->
                <span class="adim-no">1</span>
                <!-- Başlık. -->
                <h3>Başvuru</h3>
                <!-- Açıklama. -->
                <p>Tamirci işletme adını, adresini ve uzman olduğu cihaz kategorilerini yazarak başvurur.</p>
            </li>
            <!-- 2. adım. -->
            <li>
                <!-- Numara. -->
                <span class="adim-no">2</span>
                <!-- Başlık. -->
                <h3>Yönetici onayı</h3>
                <!-- Açıklama. -->
                <p>Yönetici bilgileri kontrol eder. Onaylanmayan tamirci bu listede görünmez ve teklif veremez.</p>
            </li>
            <!-- 3. adım. -->
            <li>
                <!-- Numara. -->
                <span class="adim-no">3</span>
                <!-- Başlık. -->
                <h3>Müşteri puanı</h3>
                <!-- Açıklama. -->
                <p>Her iş bitince müşteri 1-5 arası puan verir. Ortalama puan tamircinin kartında görünür.</p>
            </li>
        </ol>
    </div>
</section>

<!-- Alt çağrı bölümü. -->
<section class="cagri-bolumu sade">
    <!-- Kapsayıcı. -->
    <div class="kapsayici cagri-ici">
        <!-- Başlık. Yönetim > Site yazıları > Tamirciler sayfası'ndan değişir. -->
        <h2<?php echo yazi_stili("tamirciler_cagri_baslik"); ?>><?php echo temiz_yaz(yazi_getir("tamirciler_cagri_baslik")); ?></h2>
        <!-- Açıklama. -->
        <p<?php echo yazi_stili("tamirciler_cagri_aciklama"); ?>><?php echo temiz_yaz(yazi_getir("tamirciler_cagri_aciklama")); ?></p>
        <!-- İki düğme. -->
        <div class="hero-actions">
            <!-- Tamirci kaydı. -->
            <a class="btn btn-hero" href="tamirci-kayit.php">Tamirci Olarak Katıl</a>
            <!-- Talep oluştur. -->
            <a class="btn btn-outline btn-hero" href="talep-olustur.php">Tamir Talebi Oluştur</a>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
