<?php
/*
SAYFA: Kod haritası
DOSYA: admin/kod-haritasi.php
KİM: Kişi 4

BU SAYFA NE İSE YARAR:
Hoca "şu özelliğin kodu nerede?" diye sorunca buradan bakılır.
Projedeki bütün dosyaları okur. İçinde HARİTA kelimesi ve hemen arkasında iki nokta olan yorum satırlarını bulur.
Her birini konu, dosya ve satır numarasıyla tabloda gösterir. Arama kutusuyla süzülür.
Kod değişse de satır numaraları her zaman güncel olur, çünkü dosyalar her açılışta yeniden okunur.

YENİ ETİKET NASIL EKLENİR:
Kodda ilgili satırın üstüne bir yorum yazılır: önce HARİTA, sonra iki nokta, sonra konu adı.
PHP ve JS'de iki eğik çizgi ile, CSS'te eğik çizgi-yıldız yorumu ile, HTML'de ünlem yorumu ile, SQL'de iki tire ile yazılır.
Örnekleri görmek için ortak/guvenlik.php dosyasına bakın.
*/

// Sekme başlığı.
$sayfa_basligi = "Kod haritası";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// ŞİMDİLİK KAPALI: Kod haritası ileride açılacak. Sayfa açılınca panele gönderir.
// Açmak için bu satırı ve altındaki satırı silin, admin/menu.php dosyasına linki geri ekleyin.
yonlendir("panel.php");

// Aranan kelime. Boş ise hepsi listelenir.
$ara = "";
// Adres çubuğunda ?ara= varsa:
if (isset($_GET["ara"])) {
    // Boşlukları silip alırız.
    $ara = trim($_GET["ara"]);
}
// "sifre" yazan da "Şifre" etiketini bulsun diye Türkçe harfleri sadeleştiririz.
$turkce = array("ş", "Ş", "ı", "İ", "ğ", "Ğ", "ü", "Ü", "ö", "Ö", "ç", "Ç");
// Karşılıkları.
$sade = array("s", "s", "i", "i", "g", "g", "u", "u", "o", "o", "c", "c");
// str_replace: birinci dizideki harfleri ikinci dizideki harflerle değiştirir.
$ara_sade = str_replace($turkce, $sade, $ara);

// HARİTA: Kod haritası (dosyaları tarayan sistem)
// Aranacak işaret. İki parçadan birleştiriyoruz ki bu satır listede ikinci kez çıkmasın.
$isaret = "HARİTA" . ":";

// Taranacak klasörler. "" proje ana klasörü.
$klasorler = array("", "ortak/", "musteri/", "tamirci/", "admin/", "css/", "js/", "veritabani/");
// Taranacak dosya uzantıları.
$uzantilar = array("php", "css", "js", "sql");

// Bulunan etiketler bu diziye eklenir.
$bulunanlar = array();

// Her klasörü geziyoruz.
foreach ($klasorler as $klasor) {
    // Her uzantıyı geziyoruz.
    foreach ($uzantilar as $uzanti) {
        // glob: klasördeki bu uzantılı dosyaların listesini verir. Örnek: ../ortak/*.php
        $dosyalar = glob(__DIR__ . "/../" . $klasor . "*." . $uzanti);
        // Her dosyayı geziyoruz.
        foreach ($dosyalar as $dosya_yolu) {
            // file: dosyayı satır satır diziye okur. İlk satır 0. eleman.
            $satirlar = file($dosya_yolu);
            // Her satırı geziyoruz.
            foreach ($satirlar as $sira => $satir) {
                // Satırda işaret var mı?
                $yer = strpos($satir, $isaret);
                // Varsa:
                if ($yer !== false) {
                    // İşaretten sonraki yazı konu adıdır.
                    $konu = substr($satir, $yer + strlen($isaret));
                    // Yorum kapatma işaretlerini sileriz (*/ ve -->).
                    $konu = str_replace(array("*/", "-->"), "", $konu);
                    // Baştaki ve sondaki boşlukları sileriz.
                    $konu = trim($konu);
                    // Dosyanın proje içindeki adı. Örnek: musteri/panel.php
                    $dosya_adi = $klasor . basename($dosya_yolu);
                    // Arama boşsa veya konu/dosya adı aranan kelimeyi içeriyorsa listeye ekleriz.
                    // Konu da sadeleştirilir ki iki taraf aynı harflerle karşılaştırılsın.
                    $konu_sade = str_replace($turkce, $sade, $konu . " " . $dosya_adi);
                    // stripos: büyük-küçük harfe bakmadan arar.
                    if ($ara_sade == "" || stripos($konu_sade, $ara_sade) !== false) {
                        // Satır numarası 1'den başlasın diye +1.
                        $bulunanlar[] = array("konu" => $konu, "dosya" => $dosya_adi, "satir" => $sira + 1);
                    }
                }
            }
        }
    }
}

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
        <h1>Kod haritası</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Bir özelliğin kodu hangi dosyanın hangi satırında? Aşağıya bir kelime yazın (örnek: teklif, şifre, resim, sohbet). Bulduğunuz dosyayı VS Code'da <strong>Ctrl+P</strong> ile açın, <strong>Ctrl+G</strong> ile satıra gidin. Ya da VS Code'da <strong>Ctrl+Shift+F</strong> ile "HARİTA" yazıp arayın. Soru listesi için proje klasöründeki KOD-HARITASI.txt dosyasına bakın.</p>
        <!-- Arama formu. GET: aranan kelime adres çubuğunda görünür. -->
        <form class="form-kutu" method="get" action="kod-haritasi.php">
            <!-- Kutunun adı. -->
            <label for="ara">Ne arıyorsunuz?</label>
            <!-- Arama kutusu. -->
            <input id="ara" name="ara" type="text" value="<?php echo temiz_yaz($ara); ?>" placeholder="Örn: teklif">
            <!-- Ara düğmesi ve temizle linki. -->
            <p><button class="btn btn-primary" type="submit">Ara</button> <a href="kod-haritasi.php">Hepsini göster</a></p>
        </form>
        <!-- Bulunan sayı. -->
        <p class="giris-metni"><?php echo count($bulunanlar); ?> sonuç bulundu.</p>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Konu</th><th>Dosya</th><th>Satır</th></tr>
                <!-- Her etiket bir satır. -->
                <?php foreach ($bulunanlar as $bulunan) { ?>
                    <!-- Konu, dosya, satır numarası. -->
                    <tr><td><?php echo temiz_yaz($bulunan["konu"]); ?></td><td><?php echo temiz_yaz($bulunan["dosya"]); ?></td><td><?php echo $bulunan["satir"]; ?></td></tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
