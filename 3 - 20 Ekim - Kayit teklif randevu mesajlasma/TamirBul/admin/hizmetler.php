<?php
/*
SAYFA: Hizmetler (ekle, düzenle, sil tek sayfada)
DOSYA: admin/hizmetler.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Bütün hizmetlerin listesi (gizliler dahil): resim, ad, kategori, fiyat, aktif
- Altta form. Adreste ?id= varsa o hizmet düzenlenir, yoksa yeni hizmet eklenir.
- Her satırda Düzenle ve Sil düğmesi
- Resim boş bırakılırsa sitede kategorinin resmi kullanılır
AYNI KALIP: kategoriler.php, adimlar.php, slaytlar.php de böyle yazıldı.
Form gizli "işlem" kutusu gönderir: kaydet veya sil.
*/

// Sekme başlığı.
$sayfa_basligi = "Hizmetler";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Kayıt yapıldıysa:
if (isset($_GET["tamam"])) {
    // Başarı yazısı.
    $tamam = "Kaydedildi.";
}
// Düzenlenen hizmetin numarası. 0 ise yeni hizmet.
$id = gelen_sayi("id");
// Yapılacak iş: kaydet veya sil.
$islem = formdan_al("islem");

// HARİTA: Hizmet silme
// Sil düğmesine basıldıysa:
if ($islem == "sil") {
    // Hizmeti sileriz.
    sil("hizmetler", "id = $id");
    // Başarı yazısı.
    $tamam = "Hizmet silindi.";
    // Form yeni kayıt için boşalsın.
    $id = 0;
}

// Formdaki kutular. Düzenleme ise aşağıda veritabanından dolar.
$ad = formdan_al("ad");
// Kategori.
$kategori_id = gelen_sayi("kategori_id");
// Açıklama.
$aciklama = formdan_al("aciklama");
// Fiyat.
$fiyat = (float) formdan_al("baslangic_fiyat");
// Aktif kutusu işaretli mi? Yeni formda işaretli başlar.
$aktif = 1;
// Resim.
$resim_mevcut = formdan_al("resim_adresi");

// HARİTA: Hizmet ekleme ve düzenleme (INSERT / UPDATE)
// Kaydet düğmesine basıldıysa:
if ($islem == "kaydet") {
    // İşaretsiz kutu formda hiç gelmez. Önce 0 kabul ederiz.
    $aktif = 0;
    // Kutu işaretliyse:
    if (isset($_POST["aktif"])) {
        // Sitede görünsün.
        $aktif = 1;
    }
    // Resim: dosya veya adres. Hatalıysa false.
    $resim = resim_formdan_al("hizmet");
    // Ad veya açıklama boşsa:
    if ($ad == "" || $aciklama == "") {
        // Hata.
        $hata = "Ad ve açıklama boş bırakılamaz.";
    // Kategori yoksa:
    } else if (!kategori_var_mi($kategori_id)) {
        // Hata.
        $hata = "Kategori seçin.";
    // Resim hatalıysa:
    } else if ($resim === false) {
        // Hata.
        $hata = "Resim kaydedilemedi. jpg, png, webp veya gif olmalı (en fazla 3 MB).";
    } else {
        // Yeni hizmetse ekler, değilse günceller.
        kaydet_veya_guncelle("hizmetler", array("ad" => $ad, "kategori_id" => $kategori_id, "aciklama" => $aciklama, "baslangic_fiyat" => $fiyat, "resim" => $resim, "aktif" => $aktif), $id);
        // Sayfayı yeniden açıyoruz. F5'e basınca kayıt tekrar olmasın.
        yonlendir("hizmetler.php?tamam=1");
    }
}

// Düzenle linkine basıldıysa (form gönderilmeden) kutuları veritabanından doldururuz.
if ($id > 0 && $islem == "") {
    // Hizmeti okuruz.
    $hizmet = tek_satir("SELECT * FROM hizmetler WHERE id = $id");
    // Bulunduysa:
    if ($hizmet) {
        // Ad.
        $ad = $hizmet["ad"];
        // Kategori.
        $kategori_id = (int) $hizmet["kategori_id"];
        // Açıklama.
        $aciklama = $hizmet["aciklama"];
        // Fiyat.
        $fiyat = $hizmet["baslangic_fiyat"];
        // Aktif mi.
        $aktif = (int) $hizmet["aktif"];
        // Resim.
        $resim_mevcut = $hizmet["resim"];
    }
}

// Liste: hizmetler ve kategori adları.
$hizmetler = satirlar("SELECT hizmetler.*, kategoriler.ad AS kategori_adi FROM hizmetler INNER JOIN kategoriler ON kategoriler.id = hizmetler.kategori_id ORDER BY hizmetler.id DESC");
// Form için kategoriler.
$kategoriler = satirlar("SELECT * FROM kategoriler ORDER BY id");

// Üst menü.
include "../ortak/baslik.php";
// Yönetim menüsü.
include "menu.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Hizmetler</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Buradaki hizmetler sitedeki hizmetler sayfasında görünür. "Sitede görünsün" kapalıysa gizlenir.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Resim</th><th>Ad</th><th>Kategori</th><th>Fiyat</th><th>Aktif</th><th></th></tr>
                <!-- Her hizmet bir satır. -->
                <?php foreach ($hizmetler as $satir) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Küçük resim. Yoksa kategori resmi kullanılır. -->
                        <td><?php if ($satir["resim"] != "") { ?><img class="tablo-resmi" src="<?php echo temiz_yaz(resim_adresi($satir["resim"])); ?>" alt=""><?php } else { ?><small>Kategori resmi</small><?php } ?></td>
                        <!-- Ad. -->
                        <td><?php echo temiz_yaz($satir["ad"]); ?></td>
                        <!-- Kategori. -->
                        <td><?php echo temiz_yaz($satir["kategori_adi"]); ?></td>
                        <!-- Fiyat. -->
                        <td><?php echo fiyat_yaz($satir["baslangic_fiyat"]); ?></td>
                        <!-- Aktif mi. -->
                        <td><?php if ($satir["aktif"] == 1) { echo "Evet"; } else { echo "Hayır"; } ?></td>
                        <!-- Düğmeler. -->
                        <td>
                            <!-- Düzenle: aynı sayfa, ?id= ile. -->
                            <a class="btn btn-sm btn-outline" href="hizmetler.php?id=<?php echo (int) $satir["id"]; ?>">Düzenle</a>
                            <!-- Sil formu. -->
                            <form method="post" action="hizmetler.php" onsubmit="return confirm('Bu hizmet silinsin mi?');">
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

        <!-- Ekleme ve düzenleme formu. enctype: resim dosyası gidebilsin. -->
        <form class="form-kutu" method="post" action="hizmetler.php" enctype="multipart/form-data">
            <!-- Başlık: id varsa düzenle, yoksa yeni. -->
            <h2><?php if ($id > 0) { echo "Hizmeti düzenle"; } else { echo "Yeni hizmet"; } ?></h2>
            <!-- İş. -->
            <input type="hidden" name="islem" value="kaydet">
            <!-- Numara (yeni ise 0). -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <!-- Ad. -->
            <label for="ad">Ad</label>
            <!-- Ad kutusu. -->
            <input id="ad" name="ad" type="text" value="<?php echo temiz_yaz($ad); ?>">
            <!-- Kategori. -->
            <label for="kategori_id">Kategori</label>
            <!-- Kategori kutusu. -->
            <select id="kategori_id" name="kategori_id">
                <!-- Her kategori bir seçenek. -->
                <?php foreach ($kategoriler as $kategori) { ?>
                    <!-- Seçenek. -->
                    <option value="<?php echo (int) $kategori["id"]; ?>" <?php if ($kategori_id == $kategori["id"]) { echo "selected"; } ?>><?php echo temiz_yaz($kategori["ad"]); ?></option>
                <?php } ?>
            </select>
            <!-- Açıklama. -->
            <label for="aciklama">Açıklama</label>
            <!-- Açıklama kutusu. -->
            <textarea id="aciklama" name="aciklama"><?php echo temiz_yaz($aciklama); ?></textarea>
            <!-- Fiyat. -->
            <label for="baslangic_fiyat">Başlangıç fiyatı (TL)</label>
            <!-- Fiyat kutusu. -->
            <input id="baslangic_fiyat" name="baslangic_fiyat" type="number" min="0" value="<?php echo (float) $fiyat; ?>">
            <!-- Resim alanı (ortak parça). Boş bırakılırsa kategori resmi kullanılır. -->
            <?php include "../ortak/resim-alani.php"; ?>
            <!-- Aktif kutusu. -->
            <label><input type="checkbox" name="aktif" <?php if ($aktif == 1) { echo "checked"; } ?>> Sitede görünsün</label>
            <!-- Kaydet. -->
            <p><button class="btn btn-primary" type="submit">Kaydet</button> <?php if ($id > 0) { ?><a href="hizmetler.php">Vazgeç, yeni ekle</a><?php } ?></p>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
