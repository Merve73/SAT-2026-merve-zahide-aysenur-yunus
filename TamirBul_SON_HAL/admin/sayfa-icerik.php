<?php
/*
SAYFA: Sayfa yazıları
DOSYA: admin/sayfa-icerik.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- sayfa_icerikleri tablosundaki her sayfa için başlık ve uzun metin formu (şu an Hakkımızda)
- Her yazının sitede nerede göründüğü ve "Sayfada gör" düğmesi
- Metnin altında harf ve kelime sayacı, boş satırın yeni paragraf olduğu bilgisi
- Başlığın ve metnin altında açılan yazı stili kutusu (Word benzeri araç çubuğu ve önizleme)
  Stil anahtarı: sayfa_ + sayfa kodu + _baslik veya _icerik. Örnek: sayfa_hakkimizda_baslik
- Aynı sayfanın kısa yazıları (kapak, sorular) için Site yazıları ekranına link
*/

// Sekme başlığı.
$sayfa_basligi = "Sayfa yazıları";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");
// Stil tablosu eski veritabanında yoksa oluştururuz.
yazi_stili_tablosunu_hazirla();

// HARİTA: Sayfa yazılarının yeri
// Her sayfa kodunun sitede nerede göründüğü. Tabloya yeni sayfa eklenirse buraya da bir satır yazılır.
// kod => array(görünen ad, sitede nerede, sayfanın adresi, Site yazıları'ndaki grubu)
$sayfa_yerleri = array(
    // Hakkımızda hikayesi.
    "hakkimizda" => array("Hakkımızda: Hikayemiz", "Hakkımızda sayfası > fotoğraflı kapağın altındaki 'Hikayemiz' bölümü, fotoğrafın solundaki yazı", "hakkimizda.php", "hakkimizda")
);

// Kaydettikten sonra adres ?kaydedildi=1 olur.
if (isset($_GET["kaydedildi"])) {
    // Başarı yazısı.
    $tamam = "Sayfa yazısı kaydedildi. Siteyi yenileyince yeni yazı görünür.";
}

// HARİTA: Uzun sayfa yazılarını değiştirme (Hakkımızda)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Hangi sayfa. Örnek: hakkimizda
    $sayfa = formdan_al("sayfa");
    // Başlık.
    $baslik = formdan_al("baslik");
    // Metin.
    $icerik = formdan_al("icerik");
    // Bu sayfa tabloda var mı? Uydurma sayfa kodu kabul edilmez.
    $sayfa_var = sayi_getir("SELECT COUNT(*) FROM sayfa_icerikleri WHERE sayfa = '" . temizle($sayfa) . "'");
    // Sayfa yoksa:
    if ($sayfa_var == 0) {
        // Hata.
        $hata = "Böyle bir sayfa yazısı yok.";
    // Boş kutu varsa:
    } else if ($baslik == "" || $icerik == "") {
        // Hata.
        $hata = "Başlık ve metin boş bırakılamaz.";
    // Başlık 150 harften uzunsa (tablodaki sınır):
    } else if (mb_strlen($baslik, "UTF-8") > 150) {
        // Hata.
        $hata = "Başlık en fazla 150 harf olabilir.";
    } else {
        // Kaydederiz.
        guncelle("sayfa_icerikleri", array("baslik" => $baslik, "icerik" => $icerik), "sayfa = '" . temizle($sayfa) . "'");
        // Stil kutuları geldiyse ve listeyse:
        if (isset($_POST["stil"]) && is_array($_POST["stil"])) {
            // Her yazının (başlık, metin) seçimleri için:
            foreach ($_POST["stil"] as $anahtar => $secimler) {
                // Seçimler liste olarak geldiyse:
                if (is_array($secimler)) {
                    // Seçimleri kaydederiz.
                    yazi_stili_kaydet($anahtar, $secimler);
                }
            }
        }
        // Değişikliği kayda yazarız.
        log_yaz("Sayfa yazısı kaydedildi", $sayfa);
        // Sayfayı yeniden açarız. Sayfa yenilenince form ikinci kez gönderilmez.
        yonlendir("sayfa-icerik.php?kaydedildi=1");
    }
}

// Bütün sayfa yazıları.
$sayfalar = satirlar("SELECT * FROM sayfa_icerikleri ORDER BY id");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Dar kapsayıcı: uzun yazı rahat okunsun. -->
    <div class="kapsayici dar-form">
        <!-- Başlık. -->
        <h1>Sayfa yazıları</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Sitedeki uzun metinler burada. Kapak yazıları, düğmeler, sık sorulan sorular gibi kısa yazılar <a href="ayarlar.php">Site yazıları</a> ekranında.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Her sayfa için bir form. -->
        <?php foreach ($sayfalar as $sayfa) { ?>
            <?php
            // Bu sayfanın yer bilgisi. Listede yoksa genel bir bilgi yazarız.
            $yer = array($sayfa["sayfa"], "Bu yazının yeri listede yazılı değil", "index.php", "");
            // Listede varsa:
            if (isset($sayfa_yerleri[$sayfa["sayfa"]])) {
                // Listedeki bilgi.
                $yer = $sayfa_yerleri[$sayfa["sayfa"]];
            }
            ?>
            <!-- Form. yazi-grubu: Site yazıları ekranındaki kutularla aynı görünüş. -->
            <form class="yazi-grubu" method="post" action="sayfa-icerik.php">
                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                <?php echo guvenlik_alani(); ?>
                <!-- Hangi sayfa olduğu gizli gider. -->
                <input type="hidden" name="sayfa" value="<?php echo temiz_yaz($sayfa["sayfa"]); ?>">
                <!-- Başlık satırı. -->
                <div class="yazi-grubu-baslik">
                    <!-- İkon kutusu. -->
                    <span class="kart-ikonu"><?php echo ikon("belge"); ?></span>
                    <!-- Ad ve yer. -->
                    <div>
                        <!-- Görünen ad. -->
                        <h2><?php echo temiz_yaz($yer[0]); ?></h2>
                        <!-- Sitede nerede. -->
                        <p><?php echo ikon("konum"); ?> <?php echo temiz_yaz($yer[1]); ?></p>
                    </div>
                    <!-- Sayfayı yeni sekmede açar. -->
                    <a class="btn btn-sm btn-outline" href="<?php echo $kok . temiz_yaz($yer[2]); ?>" target="_blank" rel="noopener">Sayfada gör <?php echo ikon("ok"); ?></a>
                </div>
                <?php
                // Başlığın stil anahtarı. Örnek: sayfa_hakkimizda_baslik
                $stil_anahtar = "sayfa_" . $sayfa["sayfa"] . "_baslik";
                // Önizlemede başlığın kendisi görünür.
                $stil_ornek_yazi = $sayfa["baslik"];
                ?>
                <!-- Başlığın alanı: kutu, sonra açılan stil kutusu. -->
                <div class="yazi-alani stil-alani">
                    <!-- Kutunun adı. -->
                    <label for="baslik-<?php echo temiz_yaz($sayfa["sayfa"]); ?>">Başlık</label>
                    <!-- Nerede. -->
                    <p class="yazi-yeri"><?php echo ikon("konum"); ?> Yazının üstündeki büyük başlık</p>
                    <!-- Başlık kutusu. stil-hedef: önizleme bu kutunun yazısını gösterir. -->
                    <input class="stil-hedef yazi-kutusu" id="baslik-<?php echo temiz_yaz($sayfa["sayfa"]); ?>" name="baslik" type="text" maxlength="150" value="<?php echo temiz_yaz($sayfa["baslik"]); ?>">
                    <!-- Stil ayarı: tıklayınca açılır. -->
                    <details class="stil-ayari">
                        <!-- Görünen düğme. -->
                        <summary><?php echo ikon("kalem"); ?> Başlığın yazı stili</summary>
                        <!-- Word gibi araç çubuğu. -->
                        <?php include "../ortak/stil-araclari.php"; ?>
                        <!-- Önizleme. -->
                        <?php include "../ortak/stil-onizleme.php"; ?>
                    </details>
                </div>
                <?php
                // Metnin stil anahtarı. Örnek: sayfa_hakkimizda_icerik
                $stil_anahtar = "sayfa_" . $sayfa["sayfa"] . "_icerik";
                // Önizlemede metnin kendisi görünür.
                $stil_ornek_yazi = $sayfa["icerik"];
                ?>
                <!-- Metnin alanı. -->
                <div class="yazi-alani stil-alani">
                    <!-- Kutunun adı. -->
                    <label for="icerik-<?php echo temiz_yaz($sayfa["sayfa"]); ?>">Metin</label>
                    <!-- Bilgi. -->
                    <p class="yazi-yeri"><?php echo ikon("bilgi"); ?> Boş bir satır bırakırsanız sitede yeni paragraf başlar.</p>
                    <!-- Metin kutusu. data-sayac: js/site.js altına harf ve kelime sayısını yazar. -->
                    <textarea class="stil-hedef yazi-kutusu buyuk-metin" id="icerik-<?php echo temiz_yaz($sayfa["sayfa"]); ?>" name="icerik" rows="12" data-sayac="sayac-<?php echo temiz_yaz($sayfa["sayfa"]); ?>"><?php echo temiz_yaz($sayfa["icerik"]); ?></textarea>
                    <!-- Sayaç yazısı. js/site.js doldurur. -->
                    <p class="metin-sayaci" id="sayac-<?php echo temiz_yaz($sayfa["sayfa"]); ?>"></p>
                    <!-- Stil ayarı: tıklayınca açılır. -->
                    <details class="stil-ayari">
                        <!-- Görünen düğme. -->
                        <summary><?php echo ikon("kalem"); ?> Metnin yazı stili</summary>
                        <!-- Word gibi araç çubuğu. -->
                        <?php include "../ortak/stil-araclari.php"; ?>
                        <!-- Önizleme. -->
                        <?php include "../ortak/stil-onizleme.php"; ?>
                    </details>
                </div>
                <!-- Alt satır: kaydet ve aynı sayfanın kısa yazıları. -->
                <div class="yazi-alt">
                    <!-- Kaydet. -->
                    <button class="btn btn-primary" type="submit"><?php echo ikon("onay"); ?> Kaydet</button>
                    <!-- Bu sayfanın kısa yazıları varsa Site yazıları'ndaki grubuna link. -->
                    <?php if ($yer[3] != "") { ?>
                        <!-- Link. -->
                        <a class="btn btn-sm btn-outline" href="ayarlar.php#grup-<?php echo temiz_yaz($yer[3]); ?>">Bu sayfanın diğer yazıları</a>
                    <?php } ?>
                </div>
            </form>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
