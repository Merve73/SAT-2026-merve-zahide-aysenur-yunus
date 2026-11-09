<?php
/*
SAYFA: Ana sayfa resimleri (slaytlar)
DOSYA: admin/slaytlar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Ana sayfanın üstünde sırayla değişen resimlerin listesi
- Altta form. ?id= varsa düzenleme, yoksa yeni resim.
- Değişme süresi "Site yazıları" sayfasındaki slayt_suresi ayarındadır
AYNI KALIP: hizmetler.php ile aynı.
*/

// Sekme başlığı.
$sayfa_basligi = "Ana sayfa resimleri";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Kayıttan sonra:
if (isset($_GET["tamam"])) {
    // Başarı yazısı.
    $tamam = "Kaydedildi.";
}
// Düzenlenen slaytın numarası. 0 ise yeni.
$id = gelen_sayi("id");
// Yapılacak iş.
$islem = formdan_al("islem");

// Sil düğmesine basıldıysa:
if ($islem == "sil") {
    // Slaytı sileriz.
    sil("slaytlar", "id = $id");
    // Silmeyi kayda yazarız.
    log_yaz("Slayt silindi", "Numara " . $id);
    // Başarı yazısı.
    $tamam = "Resim silindi.";
    // Form boşalsın.
    $id = 0;
}

// Formdaki sıra. Yeni slaytta en büyük sıranın bir fazlası.
$sira = gelen_sayi("sira");
// Sıra yazılmadıysa:
if ($sira == 0) {
    // Sona ekleriz.
    $sira = sayi_getir("SELECT MAX(sira) FROM slaytlar") + 1;
}
// Kısa açıklama.
$aciklama = formdan_al("aciklama");
// Resim.
$resim_mevcut = formdan_al("resim_adresi");

// HARİTA: Ana sayfa slaytlarını yönetme
// Kaydet düğmesine basıldıysa:
if ($islem == "kaydet") {
    // Resim: dosya veya adres.
    $resim = resim_formdan_al("slayt");
    // Resim hatalıysa:
    if ($resim === false) {
        // Hata.
        $hata = "Resim kaydedilemedi. jpg, png, webp veya gif olmalı (en fazla 3 MB).";
    // Resim boşsa:
    } else if ($resim == "") {
        // Slaytın tek işi resim göstermek. Resimsiz olmaz.
        $hata = "Bir resim adresi yazın veya dosya yükleyin.";
    } else {
        // Yeni slaytsa ekler, değilse günceller.
        kaydet_veya_guncelle("slaytlar", array("sira" => $sira, "resim" => $resim, "aciklama" => $aciklama), $id);
        // Kaydı kayda yazarız.
        log_yaz("Slayt kaydedildi", $aciklama);
        // Sayfayı yeniden açıyoruz.
        yonlendir("slaytlar.php?tamam=1");
    }
}

// Düzenle linkine basıldıysa kutuları doldururuz.
if ($id > 0 && $islem == "") {
    // Slaytı okuruz.
    $slayt = tek_satir("SELECT * FROM slaytlar WHERE id = $id");
    // Bulunduysa:
    if ($slayt) {
        // Sıra.
        $sira = (int) $slayt["sira"];
        // Açıklama.
        $aciklama = $slayt["aciklama"];
        // Resim.
        $resim_mevcut = $slayt["resim"];
    }
}

// Liste, sıraya göre.
$slaytlar = satirlar("SELECT * FROM slaytlar ORDER BY sira, id");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Ana sayfa resimleri</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Ana sayfanın en üstünde sırayla değişen büyük resimler. Yatay resimler daha iyi görünür.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Sıra</th><th>Resim</th><th>Açıklama</th><th></th></tr>
                <!-- Her slayt bir satır. -->
                <?php foreach ($slaytlar as $satir) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Sıra. -->
                        <td><?php echo (int) $satir["sira"]; ?></td>
                        <!-- Küçük resim. -->
                        <td><img class="tablo-resmi" src="<?php echo temiz_yaz(resim_adresi($satir["resim"])); ?>" alt=""></td>
                        <!-- Açıklama. -->
                        <td><?php echo temiz_yaz($satir["aciklama"]); ?></td>
                        <!-- Düğmeler. -->
                        <td>
                            <!-- Düzenle. -->
                            <a class="btn btn-sm btn-outline" href="slaytlar.php?id=<?php echo (int) $satir["id"]; ?>">Düzenle</a>
                            <!-- Sil formu. data-onay: silmeden önce "Emin misiniz?" penceresi açılır (js/site.js). -->
                            <form method="post" action="slaytlar.php" data-onay="Bu resim silinsin mi?">
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
        <form class="form-kutu" method="post" action="slaytlar.php" enctype="multipart/form-data">
            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
            <?php echo guvenlik_alani(); ?>
            <!-- Başlık. -->
            <h2><?php if ($id > 0) { echo "Resmi düzenle"; } else { echo "Yeni resim"; } ?></h2>
            <!-- İş. -->
            <input type="hidden" name="islem" value="kaydet">
            <!-- Numara. -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <!-- Sıra. -->
            <label for="sira">Sıra (küçük sayı önce görünür)</label>
            <!-- Sıra kutusu. -->
            <input id="sira" name="sira" type="number" value="<?php echo $sira; ?>">
            <!-- Açıklama. -->
            <label for="aciklama">Kısa açıklama (resim açılmazsa görünür)</label>
            <!-- Açıklama kutusu. -->
            <input id="aciklama" name="aciklama" type="text" value="<?php echo temiz_yaz($aciklama); ?>">
            <!-- Resim alanı (ortak parça). -->
            <?php include "../ortak/resim-alani.php"; ?>
            <!-- Kaydet. -->
            <p><button class="btn btn-primary" type="submit">Kaydet</button> <?php if ($id > 0) { ?><a href="slaytlar.php">Vazgeç, yeni ekle</a><?php } ?></p>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
