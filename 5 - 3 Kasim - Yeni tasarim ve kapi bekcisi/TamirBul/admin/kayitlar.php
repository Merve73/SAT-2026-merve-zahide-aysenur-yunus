<?php
/*
SAYFA: İşlem kayıtları (log)
DOSYA: admin/kayitlar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Sitede yapılan işlerin listesi: tarih, kim, rol, işlem, ayrıntı, IP. En yeni en üstte.
- Arama kutusu (kişi, işlem veya ayrıntıda arar) ve rol seçimi
- Güvenlikle ilgili kayıtlar (hatalı giriş, kilitlenme, reddedilen form) kırmızı satır
- 90 günden eski kayıtları silme düğmesi
Kayıtları ortak/islem-kaydi.php içindeki log_yaz fonksiyonu yazar.
*/

// Sekme başlığı.
$sayfa_basligi = "İşlem kayıtları";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");
// Tablo eski veritabanında yoksa oluştururuz.
log_tablosunu_hazirla();

// HARİTA: Eski kayıtları silme
// Sil düğmesine basıldıysa:
if (formdan_al("islem") == "eski_sil") {
    // DATE_SUB(NOW(), INTERVAL 90 DAY): bugünden 90 gün öncesi. Ondan eski kayıtlar silinir.
    sil("islem_kayitlari", "tarih < DATE_SUB(NOW(), INTERVAL 90 DAY)");
    // Silme işinin kendisini de kayda yazarız.
    log_yaz("Eski kayıtlar silindi", "90 günden eski");
    // Başarı yazısı.
    $tamam = "90 günden eski kayıtlar silindi.";
}

// HARİTA: İşlem kayıtlarını listeleme (arama ve süzme)
// Aranan kelime. Adres çubuğunda ?ara= varsa.
$ara = "";
// Varsa:
if (isset($_GET["ara"])) {
    // Baş ve sondaki boşlukları sileriz.
    $ara = trim($_GET["ara"]);
}
// Seçilen rol. Boşsa hepsi.
$secili_rol = "";
// Adres çubuğunda ?rol= varsa:
if (isset($_GET["rol"])) {
    // Rolü alırız.
    $secili_rol = $_GET["rol"];
}
// Seçilebilecek roller. "ziyaretci" giriş yapmamış kişidir (tabloda boş rol).
$roller = array("" => "Hepsi", "admin" => "Yönetici", "musteri" => "Müşteri", "tamirci" => "Tamirci", "ziyaretci" => "Ziyaretçi");
// Listede olmayan rol gelirse yok sayarız.
if (!isset($roller[$secili_rol])) {
    // Hepsi.
    $secili_rol = "";
}

// Sorgunun koşulu. 1 = 1 her zaman doğrudur, arkasına AND ile koşul eklemeyi kolaylaştırır.
$kosul = "1 = 1";
// Arama yazıldıysa:
if ($ara != "") {
    // LIKE '%kelime%': içinde kelime geçen. temizle: tırnak sorguyu bozamasın.
    $kelime = temizle($ara);
    // Kişi, işlem veya ayrıntıda geçenler.
    $kosul = $kosul . " AND (kisi LIKE '%$kelime%' OR islem LIKE '%$kelime%' OR ayrinti LIKE '%$kelime%')";
}
// Ziyaretçi seçildiyse rolü boş olanlar:
if ($secili_rol == "ziyaretci") {
    // Boş rol.
    $kosul = $kosul . " AND rol = ''";
} else if ($secili_rol != "") {
    // Seçilen rol. Listeden geldiği için güvenli.
    $kosul = $kosul . " AND rol = '$secili_rol'";
}
// Son 300 kayıt, en yeni üstte.
$kayitlar = satirlar("SELECT * FROM islem_kayitlari WHERE $kosul ORDER BY id DESC LIMIT 300");
// Bütün kayıt sayısı.
$toplam = sayi_getir("SELECT COUNT(*) FROM islem_kayitlari");
// Son 24 saatteki hatalı giriş sayısı. Çoksa biri şifre deniyor olabilir.
$hatali_giris = sayi_getir("SELECT COUNT(*) FROM islem_kayitlari WHERE islem = 'Hatalı giriş' AND tarih > DATE_SUB(NOW(), INTERVAL 1 DAY)");
// Güvenlikle ilgili işlemler. Bu satırlar kırmızı görünür.
$guvenlik_islemleri = array("Hatalı giriş", "Giriş kilitlendi", "Form reddedildi");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1><?php echo ikon("belge"); ?> İşlem kayıtları</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Sitede kim, ne zaman, ne yaptı. Toplam <?php echo $toplam; ?> kayıt var, en yeni 300 tanesi görünür. Şifreler asla kayda yazılmaz.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Son 24 saatte hatalı giriş varsa uyarı. -->
        <?php if ($hatali_giris > 0) { ?>
            <!-- Uyarı. -->
            <p class="uyari kayit-uyarisi"><?php echo ikon("kilit"); ?> Son 24 saatte <?php echo $hatali_giris; ?> hatalı giriş denemesi var.</p>
        <?php } ?>
        <!-- Arama formu. GET: arama adres çubuğuna yazılır, sayfa yenilense de kalır. -->
        <form class="kayit-arama" method="get" action="kayitlar.php">
            <!-- Arama kutusu. -->
            <input type="text" name="ara" maxlength="100" placeholder="Kişi, işlem veya ayrıntı ara (örnek: giriş)" value="<?php echo temiz_yaz($ara); ?>">
            <!-- Rol seçimi. -->
            <select name="rol">
                <!-- Her rol bir seçenek. -->
                <?php foreach ($roller as $kod => $ad) { ?>
                    <!-- Seçenek. Seçili rol selected gelir. -->
                    <option value="<?php echo $kod; ?>" <?php if ($secili_rol == $kod) { echo "selected"; } ?>><?php echo $ad; ?></option>
                <?php } ?>
            </select>
            <!-- Ara düğmesi. -->
            <button class="btn btn-sm btn-primary" type="submit">Ara</button>
            <!-- Aramayı temizle. -->
            <a class="btn btn-sm btn-outline" href="kayitlar.php">Temizle</a>
        </form>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Tarih</th><th>Kim</th><th>İşlem</th><th>Ayrıntı</th><th>IP</th></tr>
                <!-- Her kayıt bir satır. -->
                <?php foreach ($kayitlar as $kayit) { ?>
                    <!-- Güvenlik kaydıysa satır kırmızı. in_array: işlem listede var mı? -->
                    <tr <?php if (in_array($kayit["islem"], $guvenlik_islemleri)) { echo 'class="guvenlik-satiri"'; } ?>>
                        <!-- Tarih ve saat. -->
                        <td><?php echo tarih_yaz($kayit["tarih"]) . " " . date("H:i:s", strtotime($kayit["tarih"])); ?></td>
                        <!-- Kişi ve rolü. -->
                        <td><?php echo temiz_yaz($kayit["kisi"]); ?><br><small><?php echo temiz_yaz($kayit["rol"]); ?></small></td>
                        <!-- İşlem. -->
                        <td><strong><?php echo temiz_yaz($kayit["islem"]); ?></strong></td>
                        <!-- Ayrıntı. -->
                        <td><?php echo temiz_yaz($kayit["ayrinti"]); ?></td>
                        <!-- IP adresi. -->
                        <td><small><?php echo temiz_yaz($kayit["ip"]); ?></small></td>
                    </tr>
                <?php } ?>
                <!-- Kayıt yoksa bilgi satırı. -->
                <?php if (count($kayitlar) == 0) { ?>
                    <!-- colspan="5": hücre 5 sütun genişliğinde. -->
                    <tr><td colspan="5">Kayıt bulunamadı.</td></tr>
                <?php } ?>
            </table>
        </div>
        <!-- Eski kayıtları silme formu. data-onay: önce "Emin misiniz?" penceresi açılır. -->
        <form method="post" action="kayitlar.php" data-onay="90 günden eski bütün işlem kayıtları silinsin mi? Bu geri alınamaz." data-onay-dugme="Evet, sil">
            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
            <?php echo guvenlik_alani(); ?>
            <!-- Yapılacak iş. -->
            <input type="hidden" name="islem" value="eski_sil">
            <!-- Düğme. -->
            <button class="btn btn-sm btn-danger" type="submit">90 günden eski kayıtları sil</button>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
