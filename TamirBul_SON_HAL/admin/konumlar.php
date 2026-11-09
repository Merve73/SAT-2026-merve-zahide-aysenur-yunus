<?php
/*
SAYFA: İl ve ilçeler
DOSYA: admin/konumlar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Solda iller. İle tıklayınca sağda ilçeleri açılır (?il=66).
- Yeni il ekleme (plaka ve ad), seçilen ile yeni ilçe ekleme
- İl ve ilçe silme. Kullanıcısı veya talebi olan il / ilçe silinmez.
NOT: Kullanıcı ve talep tabloları il ve ilçenin adını saklar. Silmeden önce o adla kayıt var mı bakarız.
*/

// Sekme başlığı.
$sayfa_basligi = "İl / İlçe";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Yapılacak iş.
$islem = formdan_al("islem");
// Seçili ilin plaka numarası. 0 ise il seçilmedi.
$secili_il = gelen_sayi("il");
// Formdaki ad (il veya ilçe).
$ad = formdan_al("ad");

// HARİTA: İl ve ilçe yönetimi
// İl ekle:
if ($islem == "il_ekle") {
    // Plaka numarası.
    $plaka = gelen_sayi("plaka");
    // Plaka 1-81 dışındaysa veya ad boşsa:
    if ($plaka < 1 || $plaka > 81 || $ad == "") {
        // Hata.
        $hata = "Plaka (1-81) ve il adı yazın.";
    // Bu plaka zaten varsa:
    } else if (sayi_getir("SELECT COUNT(*) FROM iller WHERE id = $plaka") > 0) {
        // Hata.
        $hata = "Bu plaka numarası ile il zaten var.";
    } else {
        // İli ekleriz.
        kaydet("iller", array("id" => $plaka, "ad" => $ad));
        // Eklemeyi kayda yazarız.
        log_yaz("İl eklendi", $ad);
        // Başarı yazısı.
        $tamam = "İl eklendi. Şimdi ilçelerini ekleyin.";
        // Yeni ilin ilçeleri sağda açılsın.
        $secili_il = $plaka;
    }
}

// İl sil:
if ($islem == "il_sil") {
    // İlin adı.
    $il = tek_satir("SELECT ad FROM iller WHERE id = $secili_il");
    // Bu ili kullanan kullanıcı ve talep sayısı. Başta 0.
    $kullanan = 0;
    // İl bulunduysa sayarız.
    if ($il) {
        // Ad temizlenir.
        $il_adi = temizle($il["ad"]);
        // Kullanıcı ve talep sayısı toplanır.
        $kullanan = sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE sehir = '$il_adi'") + sayi_getir("SELECT COUNT(*) FROM talepler WHERE sehir = '$il_adi'");
    }
    // İl yoksa:
    if (!$il) {
        // Hata.
        $hata = "İl bulunamadı.";
    // Kullanılıyorsa:
    } else if ($kullanan > 0) {
        // Hata.
        $hata = "Bu ili kullanan " . $kullanan . " kullanıcı veya talep var. Silinemez.";
    } else {
        // Önce ilçeleri sileriz.
        sil("ilceler", "il_id = $secili_il");
        // Sonra ili.
        sil("iller", "id = $secili_il");
        // Silmeyi kayda yazarız.
        log_yaz("İl silindi", "Plaka " . $secili_il);
        // Başarı yazısı.
        $tamam = "İl ve ilçeleri silindi.";
        // Sağdaki liste kapansın.
        $secili_il = 0;
    }
}

// İlçe ekle:
if ($islem == "ilce_ekle") {
    // Ad boşsa:
    if ($ad == "") {
        // Hata.
        $hata = "İlçe adı boş bırakılamaz.";
    // İl seçili değilse:
    } else if (sayi_getir("SELECT COUNT(*) FROM iller WHERE id = $secili_il") == 0) {
        // Hata.
        $hata = "Önce soldan bir il seçin.";
    // Bu ilde aynı ilçe varsa: (F5'e basınca da ikinci kez eklenmez.)
    } else if (sayi_getir("SELECT COUNT(*) FROM ilceler WHERE il_id = $secili_il AND ad = '" . temizle($ad) . "'") > 0) {
        // Hata.
        $hata = "Bu ilçe zaten var.";
    } else {
        // İlçeyi ekleriz.
        kaydet("ilceler", array("il_id" => $secili_il, "ad" => $ad));
        // Eklemeyi kayda yazarız.
        log_yaz("İlçe eklendi", $ad);
        // Başarı yazısı.
        $tamam = "İlçe eklendi: " . $ad;
    }
}

// İlçe sil:
if ($islem == "ilce_sil") {
    // İlçe numarası.
    $ilce_id = gelen_sayi("ilce_id");
    // İlçenin ve ilinin adı (Merkez gibi adlar birden çok ilde var).
    $ilce = tek_satir("SELECT ilceler.ad, iller.ad AS il_adi FROM ilceler INNER JOIN iller ON iller.id = ilceler.il_id WHERE ilceler.id = $ilce_id");
    // Bu ilçeyi kullanan kullanıcı ve talep sayısı. Başta 0.
    $kullanan = 0;
    // İlçe bulunduysa sayarız.
    if ($ilce) {
        // İl adı temizlenir.
        $il_adi = temizle($ilce["il_adi"]);
        // İlçe adı temizlenir.
        $ilce_adi = temizle($ilce["ad"]);
        // Kullanıcı ve talep sayısı toplanır.
        $kullanan = sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE sehir = '$il_adi' AND ilce = '$ilce_adi'") + sayi_getir("SELECT COUNT(*) FROM talepler WHERE sehir = '$il_adi' AND ilce = '$ilce_adi'");
    }
    // İlçe yoksa:
    if (!$ilce) {
        // Hata.
        $hata = "İlçe bulunamadı.";
    // Kullanılıyorsa:
    } else if ($kullanan > 0) {
        // Hata.
        $hata = "Bu ilçeyi kullanan " . $kullanan . " kullanıcı veya talep var. Silinemez.";
    } else {
        // İlçeyi sileriz.
        sil("ilceler", "id = $ilce_id");
        // Silmeyi kayda yazarız.
        log_yaz("İlçe silindi", "");
        // Başarı yazısı.
        $tamam = "İlçe silindi.";
    }
}

// İller ve her ilin ilçe sayısı (alt sorgu).
$iller = satirlar("SELECT iller.*, (SELECT COUNT(*) FROM ilceler WHERE ilceler.il_id = iller.id) AS ilce_sayisi FROM iller ORDER BY ad");
// Seçili il.
$il = tek_satir("SELECT * FROM iller WHERE id = $secili_il");
// Seçili ilin ilçeleri.
$ilceler = satirlar("SELECT * FROM ilceler WHERE il_id = $secili_il ORDER BY ad");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>İl / İlçe</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Formlardaki il ve ilçe listeleri buradan gelir. Tamirciler sadece kendi ilindeki talepleri görür.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- İki sütun yan yana. -->
        <div class="yan-yana">
            <!-- Sol: iller. -->
            <div>
                <!-- Başlık. -->
                <h2>İller</h2>
                <!-- Tablo kutusu. -->
                <div class="tablo-kutu">
                    <!-- Tablo. -->
                    <table>
                        <!-- Başlık satırı. -->
                        <tr><th>Plaka</th><th>İl</th><th>İlçe</th><th></th></tr>
                        <!-- Her il bir satır. -->
                        <?php foreach ($iller as $satir) { ?>
                            <!-- Satır. -->
                            <tr>
                                <!-- Plaka. -->
                                <td><?php echo (int) $satir["id"]; ?></td>
                                <!-- İl adı, tıklayınca ilçeleri açar. -->
                                <td><a href="konumlar.php?il=<?php echo (int) $satir["id"]; ?>"><?php echo temiz_yaz($satir["ad"]); ?></a></td>
                                <!-- İlçe sayısı. -->
                                <td><?php echo (int) $satir["ilce_sayisi"]; ?></td>
                                <!-- Sil. -->
                                <td>
                                    <!-- Sil formu. data-onay: silmeden önce "Emin misiniz?" penceresi açılır (js/site.js). -->
                                    <form method="post" action="konumlar.php" data-onay="İl ve ilçeleri silinsin mi?">
                                        <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                        <?php echo guvenlik_alani(); ?>
                                        <!-- İş. -->
                                        <input type="hidden" name="islem" value="il_sil">
                                        <!-- Plaka. -->
                                        <input type="hidden" name="il" value="<?php echo (int) $satir["id"]; ?>">
                                        <!-- Düğme. -->
                                        <button class="btn btn-sm btn-danger" type="submit">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
                <!-- İl ekleme formu. -->
                <form class="form-kutu" method="post" action="konumlar.php">
                    <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                    <?php echo guvenlik_alani(); ?>
                    <!-- Başlık. -->
                    <h2>Yeni il</h2>
                    <!-- İş. -->
                    <input type="hidden" name="islem" value="il_ekle">
                    <!-- Plaka. -->
                    <label for="plaka">Plaka numarası</label>
                    <!-- Plaka kutusu. -->
                    <input id="plaka" name="plaka" type="number" min="1" max="81" placeholder="Örn: 42">
                    <!-- Ad. -->
                    <label for="il_adi">İl adı</label>
                    <!-- Ad kutusu. -->
                    <input id="il_adi" name="ad" type="text" placeholder="Örn: Konya">
                    <!-- Ekle. -->
                    <p><button class="btn btn-primary" type="submit">İl ekle</button></p>
                </form>
            </div>
            <!-- Sağ: seçili ilin ilçeleri. -->
            <div>
                <!-- İl seçildiyse: -->
                <?php if ($il) { ?>
                    <!-- Başlık. -->
                    <h2><?php echo temiz_yaz($il["ad"]); ?> ilçeleri</h2>
                    <!-- İlçe ekleme formu. -->
                    <form class="form-kutu" method="post" action="konumlar.php">
                        <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                        <?php echo guvenlik_alani(); ?>
                        <!-- İş. -->
                        <input type="hidden" name="islem" value="ilce_ekle">
                        <!-- Hangi il. -->
                        <input type="hidden" name="il" value="<?php echo $secili_il; ?>">
                        <!-- Ad. -->
                        <label for="ilce_adi">Yeni ilçe adı</label>
                        <!-- Ad kutusu. -->
                        <input id="ilce_adi" name="ad" type="text" placeholder="Örn: Merkez">
                        <!-- Ekle. -->
                        <p><button class="btn btn-primary" type="submit">İlçe ekle</button></p>
                    </form>
                    <!-- Tablo kutusu. -->
                    <div class="tablo-kutu">
                        <!-- Tablo. -->
                        <table>
                            <!-- Başlık satırı. -->
                            <tr><th>İlçe</th><th></th></tr>
                            <!-- Her ilçe bir satır. -->
                            <?php foreach ($ilceler as $satir) { ?>
                                <!-- Satır. -->
                                <tr>
                                    <!-- İlçe adı. -->
                                    <td><?php echo temiz_yaz($satir["ad"]); ?></td>
                                    <!-- Sil. -->
                                    <td>
                                        <!-- Sil formu. data-onay: silmeden önce "Emin misiniz?" penceresi açılır (js/site.js). -->
                                        <form method="post" action="konumlar.php" data-onay="İlçe silinsin mi?">
                                            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                            <?php echo guvenlik_alani(); ?>
                                            <!-- İş. -->
                                            <input type="hidden" name="islem" value="ilce_sil">
                                            <!-- Hangi il (sayfa aynı ilde kalsın). -->
                                            <input type="hidden" name="il" value="<?php echo $secili_il; ?>">
                                            <!-- İlçe numarası. -->
                                            <input type="hidden" name="ilce_id" value="<?php echo (int) $satir["id"]; ?>">
                                            <!-- Düğme. -->
                                            <button class="btn btn-sm btn-danger" type="submit">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                <!-- İl seçilmediyse bilgi. -->
                <?php } else { ?>
                    <!-- Bilgi. -->
                    <p class="bos-liste">İlçelerini görmek için soldan bir ile tıklayın.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
