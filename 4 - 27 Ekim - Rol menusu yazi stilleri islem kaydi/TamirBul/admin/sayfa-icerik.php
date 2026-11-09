<?php
/*
SAYFA: Sayfa yazıları
DOSYA: admin/sayfa-icerik.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- sayfa_icerikleri tablosundaki her sayfa için başlık ve uzun metin formu
- Şu an tabloda hakkımızda var. Hakkımızda sayfası bunu okur.
- Başlığın ve metnin üstünde Word benzeri araç çubuğu, altında önizleme
  Stil anahtarı: sayfa_ + sayfa kodu + _baslik veya _icerik. Örnek: sayfa_hakkimizda_baslik
*/

// Sekme başlığı.
$sayfa_basligi = "Sayfa yazıları";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");
// Stil tablosu eski veritabanında yoksa oluştururuz.
yazi_stili_tablosunu_hazirla();

// HARİTA: Uzun sayfa yazılarını değiştirme (Hakkımızda)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Hangi sayfa. Örnek: hakkımızda
    $sayfa = formdan_al("sayfa");
    // Başlık.
    $baslik = formdan_al("baslik");
    // Metin.
    $icerik = formdan_al("icerik");
    // Boş kutu varsa:
    if ($baslik == "" || $icerik == "") {
        // Hata.
        $hata = "Başlık ve metin boş bırakılamaz.";
    } else {
        // Kaydederiz.
        guncelle("sayfa_icerikleri", array("baslik" => $baslik, "icerik" => $icerik), "sayfa = '" . temizle($sayfa) . "'");
        // Stil kutuları geldiyse:
        if (isset($_POST["stil"])) {
            // Her yazının (başlık, metin) seçimleri için:
            foreach ($_POST["stil"] as $anahtar => $secimler) {
                // Seçimleri kaydederiz.
                yazi_stili_kaydet($anahtar, $secimler);
            }
        }
        // Değişikliği kayda yazarız.
        log_yaz("Sayfa yazısı kaydedildi", $sayfa);
        // Başarı yazısı.
        $tamam = "Sayfa yazısı kaydedildi.";
    }
}

// Bütün sayfa yazıları.
$sayfalar = satirlar("SELECT * FROM sayfa_icerikleri ORDER BY id");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Sayfa yazıları</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Uzun metinler burada durur. Hakkımızda sayfası bu tablodan okur.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Her sayfa için bir form. -->
        <?php foreach ($sayfalar as $sayfa) { ?>
            <!-- Form. -->
            <form class="form-kutu" method="post" action="sayfa-icerik.php">
                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                <?php echo guvenlik_alani(); ?>
                <!-- Hangi sayfa olduğu gizli gider. -->
                <input type="hidden" name="sayfa" value="<?php echo temiz_yaz($sayfa["sayfa"]); ?>">
                <!-- Sayfanın kodu. -->
                <p class="etiket"><?php echo temiz_yaz($sayfa["sayfa"]); ?></p>
                <?php
                // Başlığın stil anahtarı. Örnek: sayfa_hakkimizda_baslik
                $stil_anahtar = "sayfa_" . $sayfa["sayfa"] . "_baslik";
                // Önizlemede başlığın kendisi görünür.
                $stil_ornek_yazi = $sayfa["baslik"];
                ?>
                <!-- Başlığın alanı: araç çubuğu, kutu, önizleme. -->
                <div class="stil-alani">
                    <!-- Başlık. -->
                    <label>Başlık</label>
                    <!-- Word gibi araç çubuğu. -->
                    <?php include "../ortak/stil-araclari.php"; ?>
                    <!-- Başlık kutusu. stil-hedef: önizleme bu kutunun yazısını gösterir. -->
                    <input class="stil-hedef" name="baslik" type="text" value="<?php echo temiz_yaz($sayfa["baslik"]); ?>">
                    <!-- Önizleme. -->
                    <?php include "../ortak/stil-onizleme.php"; ?>
                </div>
                <?php
                // Metnin stil anahtarı. Örnek: sayfa_hakkimizda_icerik
                $stil_anahtar = "sayfa_" . $sayfa["sayfa"] . "_icerik";
                // Önizlemede metnin kendisi görünür.
                $stil_ornek_yazi = $sayfa["icerik"];
                ?>
                <!-- Metnin alanı: araç çubuğu, kutu, önizleme. -->
                <div class="stil-alani">
                    <!-- Metin. -->
                    <label>Metin</label>
                    <!-- Word gibi araç çubuğu. -->
                    <?php include "../ortak/stil-araclari.php"; ?>
                    <!-- Metin kutusu. -->
                    <textarea class="stil-hedef" name="icerik" rows="8"><?php echo temiz_yaz($sayfa["icerik"]); ?></textarea>
                    <!-- Önizleme. -->
                    <?php include "../ortak/stil-onizleme.php"; ?>
                </div>
                <!-- Kaydet. -->
                <p><button class="btn btn-primary" type="submit">Kaydet</button></p>
            </form>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
