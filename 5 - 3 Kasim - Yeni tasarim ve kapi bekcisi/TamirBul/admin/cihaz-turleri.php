<?php
/*
SAYFA: Cihaz türleri
DOSYA: admin/cihaz-turleri.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Üstte ekleme formu: kategori ve cihaz türü adı
- Altta liste: kategori, cihaz türü, Sil düğmesi
- Talep formundaki "Cihaz Türü" kutusu bu tablodan dolar
NOT: Talepler türün adını kendisi saklar. Tür silinse de eski talepler bozulmaz.
*/

// Sekme başlığı.
$sayfa_basligi = "Cihaz türleri";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Yapılacak iş: ekle veya sil.
$islem = formdan_al("islem");
// Formdaki kategori. Ekledikten sonra seçili kalır, arka arkaya ekleme kolay olur.
$secili_kategori = gelen_sayi("kategori_id");

// HARİTA: Cihaz türü ekleme ve silme
// Sil düğmesine basıldıysa:
if ($islem == "sil") {
    // Bu türe bağlı hizmet satırlarını sileriz (hizmet kalır, sadece bu cihazla bağı kopar).
    sil("hizmet_cihazlari", "cihaz_turu_id = " . gelen_sayi("id"));
    // Türü sileriz.
    sil("cihaz_turleri", "id = " . gelen_sayi("id"));
    // Silmeyi kayda yazarız.
    log_yaz("Cihaz türü silindi", "Numara " . gelen_sayi("id"));
    // Başarı yazısı.
    $tamam = "Cihaz türü silindi.";
}
// Ekle düğmesine basıldıysa:
if ($islem == "ekle") {
    // Yeni türün adı.
    $ad = formdan_al("ad");
    // Kategori yoksa:
    if (!kategori_var_mi($secili_kategori)) {
        // Hata.
        $hata = "Kategori seçin.";
    // Ad boşsa:
    } else if ($ad == "") {
        // Hata.
        $hata = "Cihaz türü adı boş bırakılamaz.";
    // Bu kategoride aynı ad varsa: (F5'e basınca da ikinci kez eklenmez.)
    } else if (sayi_getir("SELECT COUNT(*) FROM cihaz_turleri WHERE kategori_id = $secili_kategori AND ad = '" . temizle($ad) . "'") > 0) {
        // Hata.
        $hata = "Bu cihaz türü zaten var.";
    } else {
        // Ekleriz.
        kaydet("cihaz_turleri", array("kategori_id" => $secili_kategori, "ad" => $ad));
        // Eklemeyi kayda yazarız.
        log_yaz("Cihaz türü eklendi", $ad);
        // Başarı yazısı.
        $tamam = "Eklendi: " . $ad;
    }
}

// Form için kategoriler.
$kategoriler = satirlar("SELECT * FROM kategoriler ORDER BY id");
// Liste: türler ve kategori adları.
$turler = satirlar("SELECT cihaz_turleri.*, kategoriler.ad AS kategori_adi FROM cihaz_turleri INNER JOIN kategoriler ON kategoriler.id = cihaz_turleri.kategori_id ORDER BY kategoriler.id, cihaz_turleri.ad");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Cihaz türleri</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Müşteri talep açarken önce kategoriyi, sonra bu listedeki cihaz türünü seçer.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Ekleme formu. -->
        <form class="form-kutu" method="post" action="cihaz-turleri.php">
            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
            <?php echo guvenlik_alani(); ?>
            <!-- Başlık. -->
            <h2>Yeni cihaz türü</h2>
            <!-- İş. -->
            <input type="hidden" name="islem" value="ekle">
            <!-- Kategori. -->
            <label for="kategori_id">Kategori</label>
            <!-- Kategori kutusu. -->
            <select id="kategori_id" name="kategori_id">
                <!-- Boş seçenek. -->
                <option value="">Seçin</option>
                <!-- Her kategori bir seçenek. -->
                <?php foreach ($kategoriler as $kategori) { ?>
                    <!-- Seçenek. -->
                    <option value="<?php echo (int) $kategori["id"]; ?>" <?php if ($secili_kategori == $kategori["id"]) { echo "selected"; } ?>><?php echo temiz_yaz($kategori["ad"]); ?></option>
                <?php } ?>
            </select>
            <!-- Ad. -->
            <label for="ad">Cihaz türü adı</label>
            <!-- Ad kutusu. -->
            <input id="ad" name="ad" type="text" placeholder="Örn: Klima">
            <!-- Ekle. -->
            <p><button class="btn btn-primary" type="submit">Ekle</button></p>
        </form>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Kategori</th><th>Cihaz türü</th><th></th></tr>
                <!-- Her tür bir satır. -->
                <?php foreach ($turler as $tur) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Kategori. -->
                        <td><?php echo temiz_yaz($tur["kategori_adi"]); ?></td>
                        <!-- Tür. -->
                        <td><?php echo temiz_yaz($tur["ad"]); ?></td>
                        <!-- Sil. -->
                        <td>
                            <!-- Sil formu. data-onay: silmeden önce "Emin misiniz?" penceresi açılır (js/site.js). -->
                            <form method="post" action="cihaz-turleri.php" data-onay="Bu cihaz türü silinsin mi?">
                                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                <?php echo guvenlik_alani(); ?>
                                <!-- İş. -->
                                <input type="hidden" name="islem" value="sil">
                                <!-- Numara. -->
                                <input type="hidden" name="id" value="<?php echo (int) $tur["id"]; ?>">
                                <!-- Düğme. -->
                                <button class="btn btn-sm btn-danger" type="submit">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
