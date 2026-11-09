<?php
/*
SAYFA: Nasıl çalışır adımları (ekle, düzenle, sil tek sayfada)
DOSYA: admin/adimlar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Ana sayfadaki numaralı kartların listesi: sıra, resim, başlık, açıklama
- Altta form. ?id= varsa düzenleme, yoksa yeni adım.
- Kart numarası (1, 2, 3) ana sayfada sıraya göre kendiliğinden verilir.
AYNI KALIP: hizmetler.php ile aynı.
*/

// Sekme başlığı.
$sayfa_basligi = "Nasıl çalışır adımları";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Kayıttan sonra:
if (isset($_GET["tamam"])) {
    // Başarı yazısı.
    $tamam = "Kaydedildi.";
}
// Düzenlenen adımın numarası. 0 ise yeni.
$id = gelen_sayi("id");
// Yapılacak iş.
$islem = formdan_al("islem");

// Sil düğmesine basıldıysa:
if ($islem == "sil") {
    // Adımı sileriz.
    sil("adimlar", "id = $id");
    // Başarı yazısı.
    $tamam = "Adım silindi.";
    // Form boşalsın.
    $id = 0;
}

// Formdaki sıra. Yeni adımda en büyük sıranın bir fazlası gelir.
$sira = gelen_sayi("sira");
// Sıra yazılmadıysa:
if ($sira == 0) {
    // MAX: en büyük sıra. +1 ile sona eklenir.
    $sira = sayi_getir("SELECT MAX(sira) FROM adimlar") + 1;
}
// Başlık.
$baslik = formdan_al("baslik");
// Açıklama.
$aciklama = formdan_al("aciklama");
// Resim.
$resim_mevcut = formdan_al("resim_adresi");

// HARİTA: Nasıl çalışır adımlarını yönetme
// Kaydet düğmesine basıldıysa:
if ($islem == "kaydet") {
    // Resim: dosya veya adres.
    $resim = resim_formdan_al("adim");
    // Başlık boşsa:
    if ($baslik == "") {
        // Hata.
        $hata = "Başlık boş bırakılamaz.";
    // Resim hatalıysa:
    } else if ($resim === false) {
        // Hata.
        $hata = "Resim kaydedilemedi. jpg, png, webp veya gif olmalı (en fazla 3 MB).";
    } else {
        // Yeni adımsa ekler, değilse günceller.
        kaydet_veya_guncelle("adimlar", array("sira" => $sira, "baslik" => $baslik, "aciklama" => $aciklama, "resim" => $resim), $id);
        // Sayfayı yeniden açıyoruz.
        yonlendir("adimlar.php?tamam=1");
    }
}

// Düzenle linkine basıldıysa kutuları doldururuz.
if ($id > 0 && $islem == "") {
    // Adımı okuruz.
    $adim = tek_satir("SELECT * FROM adimlar WHERE id = $id");
    // Bulunduysa:
    if ($adim) {
        // Sıra.
        $sira = (int) $adim["sira"];
        // Başlık.
        $baslik = $adim["baslik"];
        // Açıklama.
        $aciklama = $adim["aciklama"];
        // Resim.
        $resim_mevcut = $adim["resim"];
    }
}

// Liste, sıraya göre.
$adimlar = satirlar("SELECT * FROM adimlar ORDER BY sira, id");

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
        <h1>Nasıl çalışır adımları</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Ana sayfadaki numaralı kartlar. Bölüm başlığı "Site yazıları" sayfasındadır.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Sıra</th><th>Resim</th><th>Başlık</th><th>Açıklama</th><th></th></tr>
                <!-- Her adım bir satır. -->
                <?php foreach ($adimlar as $satir) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Sıra. -->
                        <td><?php echo (int) $satir["sira"]; ?></td>
                        <!-- Resim varsa küçük resim. -->
                        <td><?php if ($satir["resim"] != "") { ?><img class="tablo-resmi" src="<?php echo temiz_yaz(resim_adresi($satir["resim"])); ?>" alt=""><?php } ?></td>
                        <!-- Başlık. -->
                        <td><?php echo temiz_yaz($satir["baslik"]); ?></td>
                        <!-- Açıklama. -->
                        <td><?php echo temiz_yaz($satir["aciklama"]); ?></td>
                        <!-- Düğmeler. -->
                        <td>
                            <!-- Düzenle. -->
                            <a class="btn btn-sm btn-outline" href="adimlar.php?id=<?php echo (int) $satir["id"]; ?>">Düzenle</a>
                            <!-- Sil formu. -->
                            <form method="post" action="adimlar.php" onsubmit="return confirm('Bu adım silinsin mi?');">
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
        <form class="form-kutu" method="post" action="adimlar.php" enctype="multipart/form-data">
            <!-- Başlık. -->
            <h2><?php if ($id > 0) { echo "Adımı düzenle"; } else { echo "Yeni adım"; } ?></h2>
            <!-- İş. -->
            <input type="hidden" name="islem" value="kaydet">
            <!-- Numara. -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <!-- Sıra. -->
            <label for="sira">Sıra (küçük sayı önce görünür)</label>
            <!-- Sıra kutusu. -->
            <input id="sira" name="sira" type="number" value="<?php echo $sira; ?>">
            <!-- Başlık. -->
            <label for="baslik">Başlık</label>
            <!-- Başlık kutusu. -->
            <input id="baslik" name="baslik" type="text" value="<?php echo temiz_yaz($baslik); ?>">
            <!-- Açıklama. -->
            <label for="aciklama">Açıklama</label>
            <!-- Açıklama kutusu. -->
            <textarea id="aciklama" name="aciklama" rows="2"><?php echo temiz_yaz($aciklama); ?></textarea>
            <!-- Resim alanı (ortak parça). -->
            <?php include "../ortak/resim-alani.php"; ?>
            <!-- Kaydet. -->
            <p><button class="btn btn-primary" type="submit">Kaydet</button> <?php if ($id > 0) { ?><a href="adimlar.php">Vazgeç, yeni ekle</a><?php } ?></p>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
