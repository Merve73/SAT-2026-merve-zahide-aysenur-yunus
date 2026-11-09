<?php
/*
SAYFA: Yönetim paneli
DOSYA: admin/panel.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK (yöneticinin bilmesi gereken her şey tek ekranda):
- Sayaçlar: dikkat isteyen işler (onay bekleyen usta, okunmamış mesaj, teklif bekleyen talep)
  ve genel durum (devam eden iş, tamamlanan iş, randevu, müşteri, tamirci). Sayaca tıklayınca ilgili sayfa açılır.
- Dikkat isteyen sayaç 0'dan büyükse turuncu renkte hafifçe parlar (css: yanip-sonen). İş bitince normal renge döner.
- Onay bekleyen tamirci başvuruları: Onayla ve Reddet düğmeleri
- Okunmamış iletişim mesajları: Okundu düğmesi
- Son talepler ve yaklaşan randevular
- Talep durumlarının dağılımı (çubuk)
- İçerik yönetimi kısayolları: her içerik sayfasının linki ve kaç kayıt olduğu
*/

// Sekme başlığı.
$sayfa_basligi = "Yönetim";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// HARİTA: Yönetim paneli sayaçları
// Sayaç kutuları. Her sayaç: ikon, yazı, sayı, tıklayınca açılacak sayfa ve renk sınıfı.
// Renk: highlight turuncu (dikkat), success yeşil (bitti), boş mavi (bilgi).
$sayaclar = array(
    array("ikon" => "kisiler", "yazi" => "Onay Bekleyen Usta", "sayi" => sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 0"), "adres" => "kullanicilar.php", "renk" => "highlight"),
    array("ikon" => "zarf", "yazi" => "Okunmamış Mesaj", "sayi" => sayi_getir("SELECT COUNT(*) FROM mesajlar WHERE okundu = 0"), "adres" => "mesajlar.php", "renk" => "highlight"),
    array("ikon" => "saat", "yazi" => "Teklif Bekleyen Talep", "sayi" => sayi_getir("SELECT COUNT(*) FROM talepler WHERE durum = 'beklemede'"), "adres" => "talepler.php", "renk" => "highlight"),
    array("ikon" => "anahtar", "yazi" => "Devam Eden İş", "sayi" => sayi_getir("SELECT COUNT(*) FROM talepler WHERE durum IN ('tamirci_atandi', 'randevu_kuruldu', 'tamirde')"), "adres" => "talepler.php", "renk" => ""),
    array("ikon" => "onay", "yazi" => "Tamamlanan İş", "sayi" => sayi_getir("SELECT COUNT(*) FROM talepler WHERE durum = 'tamamlandi'"), "adres" => "talepler.php", "renk" => "success"),
    array("ikon" => "takvim", "yazi" => "Bekleyen Randevu", "sayi" => sayi_getir("SELECT COUNT(*) FROM randevular WHERE durum = 'bekliyor'"), "adres" => "randevular.php", "renk" => ""),
    array("ikon" => "kisi-ekle", "yazi" => "Toplam Müşteri", "sayi" => sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'musteri'"), "adres" => "kullanicilar.php", "renk" => ""),
    array("ikon" => "canta", "yazi" => "Aktif Tamirci", "sayi" => sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 1"), "adres" => "kullanicilar.php", "renk" => "success")
);

// Onay bekleyen tamirciler, ilk başvuran önce.
$basvurular = satirlar("SELECT * FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 0 ORDER BY id");
// Okunmamış mesajlar, yeniden eskiye. En fazla 5 tane.
$mesajlar = satirlar("SELECT * FROM mesajlar WHERE okundu = 0 ORDER BY id DESC LIMIT 5");
// Son 6 talep ve müşteri adı.
$talepler = satirlar("SELECT talepler.*, kullanicilar.ad, kullanicilar.soyad FROM talepler INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id ORDER BY talepler.id DESC LIMIT 6");
// Bekleyen randevular, en yakın tarih önce. Tamircinin işletme adını da alırız.
$randevular = satirlar("SELECT randevular.*, talepler.takip_kodu, kullanicilar.isletme_adi FROM randevular INNER JOIN talepler ON talepler.id = randevular.talep_id INNER JOIN kullanicilar ON kullanicilar.id = randevular.tamirci_id WHERE randevular.durum = 'bekliyor' ORDER BY randevular.randevu_tarihi, randevular.randevu_saati LIMIT 5");

// HARİTA: Talep durumlarının dağılımı
// Bütün talep sayısı. Çubukların yüzdesini hesaplamak için.
$toplam_talep = sayi_getir("SELECT COUNT(*) FROM talepler");
// Durumların sırası. Türkçe adlarını durum_adi() verir.
$durum_listesi = array("beklemede", "tamirci_atandi", "randevu_kuruldu", "tamirde", "tamamlandi", "iptal");
// Her durumun sayısını bu diziye yazarız. Örnek: "beklemede" => 3
$durum_sayilari = array();
// Her durum için:
foreach ($durum_listesi as $durum) {
    // O durumdaki talepleri sayarız.
    $durum_sayilari[$durum] = sayi_getir("SELECT COUNT(*) FROM talepler WHERE durum = '" . $durum . "'");
}

// HARİTA: İçerik yönetimi kısayolları
// Sitedeki içerik sayfaları. Her biri: ekranda yazan ad, sayfa adresi ve kaç kayıt olduğu.
$icerik_sayfalari = array(
    array("ad" => "Site yazıları", "adres" => "ayarlar.php", "sayi" => sayi_getir("SELECT COUNT(*) FROM site_ayarlari")),
    array("ad" => "Ana sayfa resimleri", "adres" => "slaytlar.php", "sayi" => sayi_getir("SELECT COUNT(*) FROM slaytlar")),
    array("ad" => "Nasıl çalışır", "adres" => "adimlar.php", "sayi" => sayi_getir("SELECT COUNT(*) FROM adimlar")),
    array("ad" => "Kategoriler", "adres" => "kategoriler.php", "sayi" => sayi_getir("SELECT COUNT(*) FROM kategoriler")),
    array("ad" => "Cihaz türleri", "adres" => "cihaz-turleri.php", "sayi" => sayi_getir("SELECT COUNT(*) FROM cihaz_turleri")),
    array("ad" => "Hizmetler", "adres" => "hizmetler.php", "sayi" => sayi_getir("SELECT COUNT(*) FROM hizmetler")),
    array("ad" => "İl / İlçe", "adres" => "konumlar.php", "sayi" => sayi_getir("SELECT COUNT(*) FROM iller")),
    array("ad" => "Sayfa yazıları", "adres" => "sayfa-icerik.php", "sayi" => sayi_getir("SELECT COUNT(*) FROM sayfa_icerikleri"))
);

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Panel bölümü. -->
<section class="dashboard-section">
    <!-- Kapsayıcı. -->
    <div class="container">
        <!-- Başlık alanı: solda başlık, sağda kısayol düğmeleri. -->
        <div class="dashboard-header panel-ust">
            <!-- Sol taraf. -->
            <div>
                <!-- Başlık. -->
                <h2>Sistem Yönetici Paneli</h2>
                <!-- Bugünün tarihi. date("Y-m-d") bugünü verir, tarih_yaz Türkçe yazar. -->
                <p><?php echo tarih_yaz(date("Y-m-d")); ?> · Sizi bekleyen işler turuncu renkle işaretlenir.</p>
            </div>
            <!-- Sağ taraf: kısayollar. -->
            <div class="kart-dugmeleri">
                <!-- Siteyi yeni sekmede açar. target="_blank": yeni sekme. -->
                <a class="btn btn-sm btn-outline" href="../index.php" target="_blank">Siteyi gör</a>
                <!-- Site yazılarını ve stillerini değiştirme sayfası. -->
                <a class="btn btn-sm btn-primary" href="ayarlar.php">Yazıları ve stilleri düzenle</a>
            </div>
        </div>

        <!-- Sayaç kutuları. -->
        <div class="stats-cards-grid panel-sayaclar">
            <!-- Her sayaç bir kutu ve link. -->
            <?php foreach ($sayaclar as $sayac) { ?>
                <?php
                // Kutunun css sınıfları. Örnek: "highlight"
                $sayac_sinifi = $sayac["renk"];
                // Dikkat isteyen sayaçta bekleyen iş varsa:
                if ($sayac["renk"] == "highlight" && $sayac["sayi"] > 0) {
                    // Turuncu parıltı sınıfını ekleriz.
                    $sayac_sinifi = $sayac_sinifi . " yanip-sonen";
                } else if ($sayac["renk"] == "highlight") {
                    // Bekleyen iş yoksa (sayı 0) turuncu olmaz, normal renkte durur.
                    $sayac_sinifi = "";
                }
                ?>
                <!-- Kutu: sayı ve yazı. Tıklayınca ilgili sayfa açılır. -->
                <a class="stat-card <?php echo $sayac_sinifi; ?>" href="<?php echo $sayac["adres"]; ?>"><span class="sayac-ikonu"><?php echo ikon($sayac["ikon"]); ?></span><span class="stat-number"><?php echo $sayac["sayi"]; ?></span><span class="stat-label"><?php echo $sayac["yazi"]; ?></span></a>
            <?php } ?>
        </div>

        <!-- İki sütunlu ızgara. Dar ekranda tek sütun olur. -->
        <div class="panel-izgara">

            <!-- 1. kutu: tamirci başvuruları. Başvuru varsa turuncu çerçeveyle parlar. -->
            <div class="panel-kutu <?php if (count($basvurular) > 0) { echo "yanip-sonen"; } ?>">
                <!-- Kutu başlığı ve "tümü" linki. -->
                <div class="panel-kutu-baslik"><h3>Onay Bekleyen Tamirciler</h3><a href="kullanicilar.php">Tümü →</a></div>
                <!-- HARİTA: Tamirci başvurusu onaylama / reddetme (düğmeler) -->
                <!-- Her başvuru bir satır. -->
                <?php foreach ($basvurular as $usta) { ?>
                    <!-- Satır. -->
                    <div class="panel-satir">
                        <!-- İşletme ve yetkili. -->
                        <strong><?php echo temiz_yaz($usta["isletme_adi"] . " (" . $usta["ad"] . " " . $usta["soyad"] . ")"); ?></strong>
                        <!-- Konum, telefon, uzmanlık. -->
                        <div class="request-meta"><?php echo temiz_yaz($usta["sehir"] . " / " . $usta["ilce"] . " | " . $usta["telefon"] . " | " . yetkinlik_metni($usta["id"])); ?></div>
                        <!-- Düğmeler. -->
                        <div class="offer-actions">
                            <!-- Onayla formu. kullanici-onay.php kaydeder. -->
                            <form method="post" action="kullanici-onay.php">
                                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                <?php echo guvenlik_alani(); ?>
                                <!-- Tamircinin numarası. -->
                                <input type="hidden" name="id" value="<?php echo (int) $usta["id"]; ?>">
                                <!-- Yapılacak iş. -->
                                <input type="hidden" name="islem" value="onayla">
                                <!-- İşlemden sonra bu sayfaya dön. -->
                                <input type="hidden" name="donus" value="panel">
                                <!-- Düğme. -->
                                <button class="btn btn-sm btn-secondary" type="submit">Onayla</button>
                            </form>
                            <!-- Reddet formu. data-onay: js/site.js gönderilmeden önce "Emin misiniz?" penceresi açar. -->
                            <form method="post" action="kullanici-onay.php" data-onay="Başvuru reddedilsin mi?">
                                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                <?php echo guvenlik_alani(); ?>
                                <!-- Tamircinin numarası. -->
                                <input type="hidden" name="id" value="<?php echo (int) $usta["id"]; ?>">
                                <!-- Yapılacak iş. -->
                                <input type="hidden" name="islem" value="reddet">
                                <!-- İşlemden sonra bu sayfaya dön. -->
                                <input type="hidden" name="donus" value="panel">
                                <!-- Düğme. -->
                                <button class="btn btn-sm btn-danger" type="submit">Reddet</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
                <!-- Başvuru yoksa bilgi. -->
                <?php if (count($basvurular) == 0) { ?>
                    <!-- Bilgi. -->
                    <p class="panel-bos">Onay bekleyen başvuru yok.</p>
                <?php } ?>
            </div>

            <!-- 2. kutu: okunmamış mesajlar. Mesaj varsa turuncu çerçeveyle parlar. -->
            <div class="panel-kutu <?php if (count($mesajlar) > 0) { echo "yanip-sonen"; } ?>">
                <!-- Kutu başlığı ve "tümü" linki. -->
                <div class="panel-kutu-baslik"><h3>Okunmamış Mesajlar</h3><a href="mesajlar.php">Tümü →</a></div>
                <!-- Her mesaj bir satır. -->
                <?php foreach ($mesajlar as $mesaj) { ?>
                    <!-- Satır. -->
                    <div class="panel-satir">
                        <!-- Yazan ve konu. -->
                        <strong><?php echo temiz_yaz($mesaj["ad"] . " - " . $mesaj["konu"]); ?></strong>
                        <!-- Mesajın ilk 90 harfi. mb_substr Türkçe harfi bozmadan keser. -->
                        <div class="request-meta"><?php echo temiz_yaz(mb_substr($mesaj["mesaj"], 0, 90, "UTF-8")); ?></div>
                        <!-- Mesajın tamamı. <template> sayfada görünmez, "Oku" düğmesi pop-up'ta açar. -->
                        <template id="mesaj-<?php echo (int) $mesaj["id"]; ?>" data-ikon="i" data-baslik="<?php echo temiz_yaz($mesaj["konu"]); ?>">
                            <!-- Kimden. -->
                            <p><strong>Kimden:</strong> <?php echo temiz_yaz($mesaj["ad"] . " (" . $mesaj["eposta"] . ")"); ?></p>
                            <!-- Ne zaman. -->
                            <p><strong>Tarih:</strong> <?php echo tarih_yaz($mesaj["tarih"]); ?></p>
                            <!-- Mesajın tamamı. nl2br: satır başlarını korur. -->
                            <p><?php echo nl2br(temiz_yaz($mesaj["mesaj"])); ?></p>
                        </template>
                        <!-- E-posta, tarih, oku ve okundu düğmesi. -->
                        <div class="offer-actions">
                            <!-- Oku düğmesi: mesajın tamamını pop-up'ta açar (js/site.js 9b). -->
                            <button type="button" class="btn btn-sm btn-outline" data-pencere="mesaj-<?php echo (int) $mesaj["id"]; ?>">Oku</button>
                            <!-- E-posta ve tarih. -->
                            <span class="kucuk-etiket"><?php echo temiz_yaz($mesaj["eposta"]); ?> · <?php echo tarih_yaz($mesaj["tarih"]); ?></span>
                            <!-- Okundu formu. mesaj-okundu.php kaydeder. -->
                            <form method="post" action="mesaj-okundu.php">
                                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                <?php echo guvenlik_alani(); ?>
                                <!-- Mesaj numarası. -->
                                <input type="hidden" name="id" value="<?php echo (int) $mesaj["id"]; ?>">
                                <!-- İşlemden sonra bu sayfaya dön. -->
                                <input type="hidden" name="donus" value="panel">
                                <!-- Düğme. -->
                                <button class="btn btn-sm btn-primary" type="submit">Okundu</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
                <!-- Mesaj yoksa bilgi. -->
                <?php if (count($mesajlar) == 0) { ?>
                    <!-- Bilgi. -->
                    <p class="panel-bos">Okunmamış mesaj yok.</p>
                <?php } ?>
            </div>

            <!-- 3. kutu: son talepler. -->
            <div class="panel-kutu">
                <!-- Kutu başlığı ve "tümü" linki. -->
                <div class="panel-kutu-baslik"><h3>Son Talepler</h3><a href="talepler.php">Tümü →</a></div>
                <!-- Tablo kutusu. -->
                <div class="tablo-kutu">
                    <!-- Tablo. -->
                    <table>
                        <!-- Başlık satırı. -->
                        <tr><th>Kod</th><th>Müşteri</th><th>Cihaz</th><th>Durum</th></tr>
                        <!-- Her talep bir satır. -->
                        <?php foreach ($talepler as $talep) { ?>
                            <!-- Kod, müşteri, cihaz, durum. -->
                            <tr><td><?php echo temiz_yaz($talep["takip_kodu"]); ?></td><td><?php echo temiz_yaz($talep["ad"] . " " . $talep["soyad"]); ?></td><td><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"]); ?></td><td><span class="<?php echo durum_rozeti($talep["durum"]); ?>"><?php echo durum_adi($talep["durum"]); ?></span></td></tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <!-- 4. kutu: yaklaşan randevular. -->
            <div class="panel-kutu">
                <!-- Kutu başlığı ve "tümü" linki. -->
                <div class="panel-kutu-baslik"><h3>Bekleyen Randevular</h3><a href="randevular.php">Tümü →</a></div>
                <!-- Randevu varsa tablo. -->
                <?php if (count($randevular) > 0) { ?>
                    <!-- Tablo kutusu. -->
                    <div class="tablo-kutu">
                        <!-- Tablo. -->
                        <table>
                            <!-- Başlık satırı. -->
                            <tr><th>Zaman</th><th>Kod</th><th>Tamirci</th></tr>
                            <!-- Her randevu bir satır. -->
                            <?php foreach ($randevular as $randevu) { ?>
                                <!-- Tarih ve saat, kod, tamirci. -->
                                <tr><td><?php echo tarih_yaz($randevu["randevu_tarihi"]) . " " . temiz_yaz($randevu["randevu_saati"]); ?></td><td><?php echo temiz_yaz($randevu["takip_kodu"]); ?></td><td><?php echo temiz_yaz($randevu["isletme_adi"]); ?></td></tr>
                            <?php } ?>
                        </table>
                    </div>
                <!-- Randevu yoksa bilgi. -->
                <?php } else { ?>
                    <!-- Bilgi. -->
                    <p class="panel-bos">Bekleyen randevu yok.</p>
                <?php } ?>
            </div>

            <!-- 5. kutu: talep durumları. -->
            <div class="panel-kutu">
                <!-- Kutu başlığı. -->
                <div class="panel-kutu-baslik"><h3>Talep Durumları</h3><span class="kucuk-etiket">Toplam <?php echo $toplam_talep; ?></span></div>
                <!-- Her durum bir çubuk. -->
                <?php foreach ($durum_sayilari as $durum => $adet) { ?>
                    <?php
                    // Çubuğun genişliği: bu durumdaki talep / bütün talep * 100.
                    $yuzde = 0;
                    // Talep varsa hesaplarız. Sıfıra bölme hatası olmasın diye kontrol.
                    if ($toplam_talep > 0) {
                        // round: küsuratı yuvarlar. Örnek: 33.3 -> 33
                        $yuzde = round($adet / $toplam_talep * 100);
                    }
                    ?>
                    <!-- Satır: ad, çubuk, sayı. -->
                    <div class="durum-satiri">
                        <!-- Durumun Türkçe adı. -->
                        <span class="durum-adi"><?php echo durum_adi($durum); ?></span>
                        <!-- Gri zemin. -->
                        <span class="durum-cubugu"><span style="width: <?php echo $yuzde; ?>%;"></span></span>
                        <!-- Sayı. -->
                        <strong><?php echo $adet; ?></strong>
                    </div>
                <?php } ?>
            </div>

            <!-- 6. kutu: içerik yönetimi kısayolları. -->
            <div class="panel-kutu">
                <!-- Kutu başlığı. -->
                <div class="panel-kutu-baslik"><h3>Site İçeriği</h3><span class="kucuk-etiket">kayıt sayısı</span></div>
                <!-- Kısayol kutuları. -->
                <div class="icerik-kisayollari">
                    <!-- Her içerik sayfası bir link. -->
                    <?php foreach ($icerik_sayfalari as $icerik) { ?>
                        <!-- Link: ad ve sayı. -->
                        <a href="<?php echo $icerik["adres"]; ?>"><strong><?php echo $icerik["sayi"]; ?></strong><?php echo $icerik["ad"]; ?></a>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
