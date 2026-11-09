<?php
/*
SAYFA: Kategoriler (ekle, düzenle, sil tek sayfada)
DOSYA: admin/kategoriler.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Kategori listesi: resim, ad, açıklama, cihaz türü sayısı
- Altta form. ?id= varsa düzenleme, yoksa yeni kategori.
- Talep veya hizmeti olan kategori silinmez. Silinince cihaz türleri ve yetkinlikleri de silinir.
AYNI KALIP: hizmetler.php ile aynı.
*/

// Sekme başlığı.
$sayfa_basligi = "Kategoriler";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Kayıttan sonra adres ?tamam=1 olur.
if (isset($_GET["tamam"])) {
    // Başarı yazısı.
    $tamam = "Kaydedildi.";
}
// Düzenlenen kategorinin numarası. 0 ise yeni.
$id = gelen_sayi("id");
// Yapılacak iş: kaydet veya sil.
$islem = formdan_al("islem");

// HARİTA: Kategori silme (kullanılıyorsa engelle)
// Sil düğmesine basıldıysa:
if ($islem == "sil") {
    // Bu kategoriyi kullanan talep ve hizmet sayısı.
    $kullanan = sayi_getir("SELECT COUNT(*) FROM talepler WHERE kategori_id = $id") + sayi_getir("SELECT COUNT(*) FROM hizmetler WHERE kategori_id = $id");
    // Kullanılıyorsa:
    if ($kullanan > 0) {
        // Hata.
        $hata = "Bu kategoride " . $kullanan . " talep veya hizmet var. Silinemez. İsterseniz adını değiştirin.";
    } else {
        // Kategorinin cihaz türlerini sileriz.
        sil("cihaz_turleri", "kategori_id = $id");
        // Tamircilerin bu yetkinliğini sileriz.
        sil("tamirci_yetkinlikleri", "kategori_id = $id");
        // Kategoriyi sileriz.
        sil("kategoriler", "id = $id");
        // Silmeyi kayda yazarız.
        log_yaz("Kategori silindi", "Numara " . $id);
        // Başarı yazısı.
        $tamam = "Kategori silindi.";
    }
    // Form yeni kayıt için boşalsın.
    $id = 0;
}

// Formdaki ad.
$ad = formdan_al("ad");
// Açıklama.
$aciklama = formdan_al("aciklama");
// Resim.
$resim_mevcut = formdan_al("resim_adresi");

// HARİTA: Kategori ekleme ve düzenleme
// Kaydet düğmesine basıldıysa:
if ($islem == "kaydet") {
    // Resim: dosya veya adres. Hatalıysa false.
    $resim = resim_formdan_al("kategori");
    // Ad boşsa:
    if ($ad == "") {
        // Hata.
        $hata = "Kategori adı boş bırakılamaz.";
    // Resim hatalıysa:
    } else if ($resim === false) {
        // Hata.
        $hata = "Resim kaydedilemedi. jpg, png, webp veya gif olmalı (en fazla 3 MB). Adreste boşluk, tırnak, parantez olmamalı.";
    } else {
        // Yeni kategoriyse ekler, değilse günceller.
        kaydet_veya_guncelle("kategoriler", array("ad" => $ad, "aciklama" => $aciklama, "resim" => $resim), $id);
        // Kaydı kayda yazarız.
        log_yaz("Kategori kaydedildi", $ad);
        // Sayfayı yeniden açıyoruz.
        yonlendir("kategoriler.php?tamam=1");
    }
}

// Düzenle linkine basıldıysa kutuları veritabanından doldururuz.
if ($id > 0 && $islem == "") {
    // Kategoriyi okuruz.
    $kategori = tek_satir("SELECT * FROM kategoriler WHERE id = $id");
    // Bulunduysa:
    if ($kategori) {
        // Ad.
        $ad = $kategori["ad"];
        // Açıklama.
        $aciklama = $kategori["aciklama"];
        // Resim.
        $resim_mevcut = $kategori["resim"];
    }
}

// Liste. Alt sorgu her kategorinin cihaz türü sayısını sayar.
$kategoriler = satirlar("SELECT kategoriler.*, (SELECT COUNT(*) FROM cihaz_turleri WHERE cihaz_turleri.kategori_id = kategoriler.id) AS tur_sayisi FROM kategoriler ORDER BY id");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Kategoriler</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Ana sayfadaki kategori kartları, talep formu ve tamirci yetkinlikleri bu tablodan gelir.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Resim</th><th>Ad</th><th>Açıklama</th><th>Cihaz türü</th><th></th></tr>
                <!-- Her kategori bir satır. -->
                <?php foreach ($kategoriler as $satir) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Resim varsa küçük resim. -->
                        <td><?php if ($satir["resim"] != "") { ?><img class="tablo-resmi" src="<?php echo temiz_yaz(resim_adresi($satir["resim"])); ?>" alt=""><?php } ?></td>
                        <!-- Ad. -->
                        <td><?php echo temiz_yaz($satir["ad"]); ?></td>
                        <!-- Açıklama. -->
                        <td><?php echo temiz_yaz($satir["aciklama"]); ?></td>
                        <!-- Cihaz türü sayısı, cihaz türleri sayfasına link. -->
                        <td><a href="cihaz-turleri.php"><?php echo (int) $satir["tur_sayisi"]; ?> tür</a></td>
                        <!-- Düğmeler. -->
                        <td>
                            <!-- Düzenle. -->
                            <a class="btn btn-sm btn-outline" href="kategoriler.php?id=<?php echo (int) $satir["id"]; ?>">Düzenle</a>
                            <!-- Sil formu. data-onay: silmeden önce "Emin misiniz?" penceresi açılır (js/site.js). -->
                            <form method="post" action="kategoriler.php" data-onay="Bu kategori silinsin mi?">
                                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                <?php echo guvenlik_alani(); ?>
                                <!-- İş. -->
                                <input type="hidden" name="islem" value="sil">
                                <!-- Numara. -->
                                <input type="hidden" name="id" value="<?php echo (int) $satir["id"]; ?>">
                                <!-- Düğme. -->
                                <button class="btn btn-sm btn-danger" type="submit">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <!-- Ekleme ve düzenleme formu. -->
        <form class="form-kutu" method="post" action="kategoriler.php" enctype="multipart/form-data">
            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
            <?php echo guvenlik_alani(); ?>
            <!-- Başlık. -->
            <h2><?php if ($id > 0) { echo "Kategoriyi düzenle"; } else { echo "Yeni kategori"; } ?></h2>
            <!-- İş. -->
            <input type="hidden" name="islem" value="kaydet">
            <!-- Numara. -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <!-- Ad. -->
            <label for="ad">Ad</label>
            <!-- Ad kutusu. -->
            <input id="ad" name="ad" type="text" value="<?php echo temiz_yaz($ad); ?>">
            <!-- Açıklama. -->
            <label for="aciklama">Kısa açıklama (kartta görünür)</label>
            <!-- Açıklama kutusu. -->
            <textarea id="aciklama" name="aciklama" rows="2"><?php echo temiz_yaz($aciklama); ?></textarea>
            <!-- Resim alanı (ortak parça). -->
            <?php include "../ortak/resim-alani.php"; ?>
            <!-- Kaydet. -->
            <p><button class="btn btn-primary" type="submit">Kaydet</button> <?php if ($id > 0) { ?><a href="kategoriler.php">Vazgeç, yeni ekle</a><?php } ?></p>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
