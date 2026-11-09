<?php
/*
SAYFA: Talep takip
DOSYA: talep-takip.php
KİM: Kişi 3

BU SAYFADA BUNLAR OLACAK:
- Takip kodu kutusu. Giriş gerekmez.
- Kod bulunursa: durum, cihaz, kategori, arıza, konum, tarih, teklif sayısı, seçilen tamirci
*/

// Sekme başlığı.
$sayfa_basligi = "Talep takip";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Bulunan talep. Bulunmazsa null.
$talep = null;
// Yazılan kod. strtoupper: tb1001 yazılsa da TB1001 olur.
$kod = strtoupper(formdan_al("takip_kodu"));

// HARİTA: Talep takip (koda göre arama)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Kodu temizleriz.
    $kod_temiz = temizle($kod);
    // HARİTA: LEFT JOIN ve alt sorgu örneği
    // Talep, kategori adı, tamirci işletmesi ve teklif sayısı tek sorguda.
    // LEFT JOIN: tamirci seçilmediyse de talep gelir, işletme adı boş olur.
    // Parantez içindeki SELECT (alt sorgu) teklifleri sayar.
    $talep = tek_satir("SELECT talepler.*, kategoriler.ad AS kategori_adi, tamirciler.isletme_adi, (SELECT COUNT(*) FROM teklifler WHERE teklifler.talep_id = talepler.id) AS teklif_sayisi FROM talepler INNER JOIN kategoriler ON kategoriler.id = talepler.kategori_id LEFT JOIN kullanicilar AS tamirciler ON tamirciler.id = talepler.tamirci_id WHERE talepler.takip_kodu = '$kod_temiz'");
    // Bulunmadıysa:
    if (!$talep) {
        // Hata.
        $hata = "Bu kod ile talep yok.";
    }
}

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Ortalanmış bölüm. -->
<section class="auth-section">
    <!-- Kapsayıcı. -->
    <div class="container auth-container">
        <!-- Beyaz kart. -->
        <div class="auth-card">
            <!-- Başlık. -->
            <h2>Talep Takip</h2>
            <!-- Açıklama. -->
            <p class="auth-subtitle">Talep açınca verilen kodu yazın. Örnek: TB1001 <button type="button" class="bilgi-dugmesi" data-pencere="takip-bilgisi" aria-label="Takip kodu nedir?">i</button></p>
            <!-- Bilgi penceresinin içeriği. <template> sayfada görünmez, js/site.js pop-up'ta gösterir. -->
            <template id="takip-bilgisi" data-ikon="i" data-baslik="Takip kodu nedir?">
                <!-- Açıklama. -->
                <p>Talep oluşturunca size <strong>TB1004-K7Q2M9</strong> gibi bir kod verilir. Kod, talep detay sayfasında ve panelinizde yazar.</p>
                <!-- Neden. -->
                <p>Kodu bilen herkes talebin durumunu giriş yapmadan görebilir. Bu yüzden kodun sonundaki harfler rastgeledir, tahmin edilemez. Kodunuzu sadece güvendiğiniz kişilerle paylaşın.</p>
            </template>
            <!-- Hata kutusu. -->
            <?php include "ortak/mesajlar.php"; ?>
            <!-- Sorgu formu. -->
            <form method="post" action="talep-takip.php">
                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                <?php echo guvenlik_alani(); ?>
                <!-- Kod alanı. -->
                <div class="form-group">
                    <!-- Kutunun adı. -->
                    <label for="takip_kodu">Takip Kodu</label>
                    <!-- Kod kutusu. -->
                    <input id="takip_kodu" name="takip_kodu" maxlength="20" type="text" placeholder="TB1001" value="<?php echo temiz_yaz($kod); ?>">
                </div>
                <!-- Sorgula düğmesi. -->
                <button class="btn btn-primary btn-block" type="submit">Sorgula</button>
            </form>
        </div>

        <!-- Talep bulunduysa sonuç kartı. -->
        <?php if ($talep) { ?>
            <!-- Sonuç kartı. -->
            <div class="request-item-card dar-kart">
                <!-- Durum etiketi. -->
                <span class="<?php echo durum_rozeti($talep["durum"]); ?>"><?php echo durum_adi($talep["durum"]); ?></span>
                <!-- Cihaz. -->
                <h4><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"] . " - " . $talep["cihaz_turu"]); ?></h4>
                <!-- Kod, tarih, konum. -->
                <p class="request-date">Kod: <?php echo temiz_yaz($talep["takip_kodu"]); ?> | <?php echo tarih_yaz($talep["olusturma_tarihi"]); ?> | <?php echo temiz_yaz($talep["sehir"] . " / " . $talep["ilce"]); ?></p>
                <!-- Ayrıntılar. -->
                <div class="request-item-body">
                    <!-- Kategori. -->
                    <p><strong>Kategori:</strong> <?php echo temiz_yaz($talep["kategori_adi"]); ?></p>
                    <!-- Arıza. -->
                    <p><strong>Arıza:</strong> <?php echo temiz_yaz($talep["ariza"]); ?></p>
                    <!-- Teklif sayısı. -->
                    <p><strong>Gelen teklif:</strong> <?php echo (int) $talep["teklif_sayisi"]; ?></p>
                    <!-- Tamirci seçildiyse adı. -->
                    <?php if ($talep["isletme_adi"] != "") { ?>
                        <!-- Seçilen tamirci. -->
                        <p><strong>Seçilen tamirci:</strong> <?php echo temiz_yaz($talep["isletme_adi"]); ?></p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
