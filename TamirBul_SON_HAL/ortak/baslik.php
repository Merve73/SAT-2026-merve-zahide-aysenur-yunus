<?php
/*
DOSYA: ortak/baslik.php
KİM: Kişi 1
NE YAPAR: Her sayfanın üst kısmı: <head>, logo, koyu/açık mod düğmesi ve role özel açılır menü.
Menü, giriş yapan kişinin rolüne göre değişir (ziyaretçi, müşteri, tamirci, yönetici).
Yeni link eklemek için ilgili rolün grubuna bir menu_linki(...) satırı yazılır.
Sayfa bunu çağırmadan önce $kok ve $sayfa_basligi değişkenlerini doldurur.
DİKKAT: Bu dosya <main> etiketini açar. ortak/altlik.php kapatır.
*/

// HARİTA: Sayfa iskeleti (bağlantı > güvenlik > başlık > içerik > altlık)
// Sayfa $kok vermediyse ana klasörde olduğunu varsayarız.
if (!isset($kok)) {
    // Ana klasör için boş yazı.
    $kok = "";
}
// Sayfa başlık vermediyse genel başlık.
if (!isset($sayfa_basligi)) {
    // Varsayılan başlık.
    $sayfa_basligi = "TamirBul";
}

// Site adı veritabanından gelir. Yönetici "Site yazıları" sayfasından değiştirir.
$site_adi = ayar_getir("site_adi", "TamirBul");
// Logonun ilk 5 harfi beyaz yazılır. Örnek: Tamir
$logo_on = mb_substr($site_adi, 0, 5, "UTF-8");
// Kalan harfler renkli yazılır. Örnek: Bul
$logo_arka = mb_substr($site_adi, 5, null, "UTF-8");

// HARİTA: Koyu mod / açık mod (PHP tarafı)
// Sitenin varsayılanı koyu mod. Ziyaretçi düğmeyle açık modu seçtiyse js/site.js "tema" çerezine "acik" yazar.
$tema = "koyu";
// Çerez varsa ve "acik" ise:
if (isset($_COOKIE["tema"]) && $_COOKIE["tema"] == "acik") {
    // Açık mod.
    $tema = "acik";
}

// HARİTA: Role özel açılır menü (PHP tarafı)
// Açık olan sayfanın ana klasöre göre adresi. Menüde o link renkli olsun diye.
// Örnek: ana klasörde "hizmetler.php", admin klasöründe "admin/panel.php"
$acik_sayfa = basename($_SERVER["PHP_SELF"]);
// Alt klasördeysek klasör adını başa ekleriz. dirname: dosyanın bulunduğu klasör.
if ($kok != "") {
    // Örnek: "admin/" + "panel.php"
    $acik_sayfa = basename(dirname($_SERVER["PHP_SELF"])) . "/" . $acik_sayfa;
}

// Menüdeki bir linki dizi olarak hazırlar. Böylece her linki tek satırda yazarız.
// $sayi: linkin yanındaki kırmızı sayı (örnek: okunmamış mesaj). 0 ise görünmez.
function menu_linki($adres, $ikon, $yazi, $aciklama, $sayi) {
    // Linkin bilgileri.
    return array("adres" => $adres, "ikon" => $ikon, "yazi" => $yazi, "aciklama" => $aciklama, "sayi" => $sayi);
}

// Giriş yapan kişinin rolü. Giriş yoksa "ziyaretci".
$rol = "ziyaretci";
// Giriş yapıldıysa:
if (giris_yapildi_mi()) {
    // Örnek: musteri, tamirci, admin
    $rol = $_SESSION["rol"];
}
// Sağdaki kişi menüsünün başlığı. Giriş yoksa "Hesap".
$kisi_basligi = "Hesap";
// Giriş yapıldıysa:
if (giris_yapildi_mi()) {
    // Giriş yapanın adı. Örnek: Ayşe
    $kisi_basligi = $_SESSION["ad"];
}

// Menü grupları. Her grup üstte bir düğme olur. Düğmeye basınca altındaki linkler açılır.
// "sag" true ise grup menünün en sağında durur (kişi menüsü).
$menu_gruplari = array();

// 1) Herkesin gördüğü grup: Keşfet. [] = dizinin sonuna ekle.
$menu_gruplari[] = array("baslik" => "Keşfet", "ikon" => "pusula", "sag" => false, "linkler" => array(
    menu_linki("hizmetler.php", "anahtar", "Hizmetler", "Tamir çeşitleri ve başlangıç fiyatları", 0),
    menu_linki("tamirciler.php", "kisiler", "Tamirciler", "Onaylı tamirciler ve puanları", 0),
    menu_linki("hakkimizda.php", "ampul", "Hakkımızda", "TamirBul nedir, nasıl çalışır?", 0),
    menu_linki("iletisim.php", "zarf", "İletişim", "Bize yazın, sorunuzu sorun", 0)
));

// 2) Ziyaretçi (giriş yapmamış kişi).
if ($rol == "ziyaretci") {
    // Talep grubu.
    $menu_gruplari[] = array("baslik" => "Talep", "ikon" => "liste", "sag" => false, "linkler" => array(
        menu_linki("talep-olustur.php", "arti", "Talep Oluştur", "Arızanı yaz, teklif topla", 0),
        menu_linki("talep-takip.php", "ara", "Talep Takip", "Takip kodunla durumu gör", 0)
    ));
    // Sağda hesap grubu: giriş ve kayıt.
    $menu_gruplari[] = array("baslik" => $kisi_basligi, "ikon" => "kisi", "sag" => true, "linkler" => array(
        menu_linki("giris.php", "giris", "Giriş Yap", "Müşteri, tamirci ve yönetici", 0),
        menu_linki("musteri-kayit.php", "kisi-ekle", "Müşteri Ol", "Ücretsiz hesap aç", 0),
        menu_linki("tamirci-kayit.php", "canta", "Tamirci Olarak Katıl", "İşletmeni kaydet, teklif ver", 0)
    ));
}

// 3) Müşteri.
if ($rol == "musteri") {
    // Cevap bekleyen teklif sayısı: müşterinin teklif bekleyen taleplerine gelen teklifler.
    $menu_bekleyen_teklif = sayi_getir("SELECT COUNT(*) FROM teklifler INNER JOIN talepler ON talepler.id = teklifler.talep_id WHERE talepler.musteri_id = " . (int) $_SESSION["kullanici_id"] . " AND talepler.durum = 'beklemede' AND teklifler.durum = 'bekliyor'");
    // Talep grubu.
    $menu_gruplari[] = array("baslik" => "Taleplerim", "ikon" => "liste", "sag" => false, "linkler" => array(
        menu_linki("talep-olustur.php", "arti", "Yeni Talep Oluştur", "Arızanı yaz, teklif topla", 0),
        menu_linki("musteri/taleplerim.php", "klasor", "Bütün Taleplerim", "Açık ve biten talepler", 0),
        menu_linki("talep-takip.php", "ara", "Talep Takip", "Takip koduyla sorgula", 0)
    ));
    // Sağda kişi menüsü.
    $menu_gruplari[] = array("baslik" => $kisi_basligi, "ikon" => "kisi", "sag" => true, "linkler" => array(
        menu_linki("musteri/panel.php", "grafik", "Kontrol Paneli", "Gelen teklifler burada", $menu_bekleyen_teklif),
        menu_linki("musteri/profil.php", "ayar", "Profilim", "Bilgiler ve şifre", 0),
        menu_linki("cikis.php", "cikis", "Çıkış Yap", "", 0)
    ));
}

// 4) Tamirci.
if ($rol == "tamirci") {
    // Bekleyen randevu sayısı.
    $menu_bekleyen_randevu = sayi_getir("SELECT COUNT(*) FROM randevular WHERE durum = 'bekliyor' AND tamirci_id = " . (int) $_SESSION["kullanici_id"]);
    // İş grubu.
    $menu_gruplari[] = array("baslik" => "İşlerim", "ikon" => "canta", "sag" => false, "linkler" => array(
        menu_linki("tamirci/panel.php", "grafik", "Kontrol Paneli", "Size verilen işler", 0),
        menu_linki("tamirci/gelen-talepler.php", "gelen", "Uygun Talepler", "İlinizde teklif bekleyenler", 0),
        menu_linki("tamirci/randevularim.php", "takvim", "Randevularım", "Bekleyen ve biten randevular", $menu_bekleyen_randevu)
    ));
    // Sağda kişi menüsü.
    $menu_gruplari[] = array("baslik" => $kisi_basligi, "ikon" => "kisi", "sag" => true, "linkler" => array(
        menu_linki("tamirci/profil.php", "ayar", "Servis Profilim", "Bilgiler, yetkinlik, şifre", 0),
        menu_linki("cikis.php", "cikis", "Çıkış Yap", "", 0)
    ));
}

// 5) Yönetici.
if ($rol == "admin") {
    // Onay bekleyen tamirci sayısı.
    $menu_bekleyen_usta = sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 0");
    // Okunmamış mesaj sayısı.
    $menu_okunmamis_mesaj = sayi_getir("SELECT COUNT(*) FROM mesajlar WHERE okundu = 0");
    // Günlük işler grubu.
    $menu_gruplari[] = array("baslik" => "Yönetim", "ikon" => "klasor", "sag" => false, "linkler" => array(
        menu_linki("admin/panel.php", "grafik", "Özet", "Her şey tek ekranda", 0),
        menu_linki("admin/kullanicilar.php", "kisiler", "Kullanıcılar", "Üyeler ve tamirci onayı", $menu_bekleyen_usta),
        menu_linki("admin/talepler.php", "liste", "Talepler", "Bütün talepler ve durumları", 0),
        menu_linki("admin/randevular.php", "takvim", "Randevular", "Kurulan randevular", 0),
        menu_linki("admin/mesajlar.php", "zarf", "Mesajlar", "İletişim formundan gelenler", $menu_okunmamis_mesaj),
        menu_linki("admin/kayitlar.php", "belge", "İşlem kayıtları", "Kim, ne zaman, ne yaptı (log)", 0)
    ));
    // Site içeriği grubu.
    $menu_gruplari[] = array("baslik" => "Site İçeriği", "ikon" => "duzen", "sag" => false, "linkler" => array(
        menu_linki("admin/ayarlar.php", "kalem", "Site yazıları", "Yazılar ve yazı stilleri", 0),
        menu_linki("admin/slaytlar.php", "resim", "Ana sayfa resimleri", "Üstte değişen resimler", 0),
        menu_linki("admin/adimlar.php", "sira", "Nasıl çalışır", "Adım kartları", 0),
        menu_linki("admin/kategoriler.php", "katman", "Kategoriler", "Ana kategoriler", 0),
        menu_linki("admin/cihaz-turleri.php", "telefon", "Cihaz türleri", "Formdaki cihaz listesi", 0),
        menu_linki("admin/hizmetler.php", "anahtar", "Hizmetler", "Tamir çeşitleri ve fiyatlar", 0),
        menu_linki("admin/konumlar.php", "konum", "İl / İlçe", "Konum listesi", 0),
        menu_linki("admin/sayfa-icerik.php", "belge", "Sayfa yazıları", "Hakkımızda gibi uzun yazılar", 0)
    ));
    // Sağda kişi menüsü.
    $menu_gruplari[] = array("baslik" => $kisi_basligi, "ikon" => "kisi", "sag" => true, "linkler" => array(
        menu_linki("admin/sifre.php", "kilit", "Şifre değiştir", "Yönetici şifresi", 0),
        menu_linki("cikis.php", "cikis", "Çıkış Yap", "", 0)
    ));
}

// Her grubun toplam kırmızı sayısını ve "açık sayfa bu grupta mı" bilgisini hesaplarız.
// $sira: grubun dizideki sırası. Sonucu $menu_gruplari[$sira] ile asıl gruba yazarız.
foreach ($menu_gruplari as $sira => $grup) {
    // Toplam sayı 0'dan başlar.
    $toplam_sayi = 0;
    // Açık sayfa bu grupta mı? Önce hayır.
    $grupta_mi = false;
    // Grubun her linki için:
    foreach ($grup["linkler"] as $link) {
        // Linkin sayısını ekleriz.
        $toplam_sayi = $toplam_sayi + $link["sayi"];
        // Link açık sayfaysa:
        if ($link["adres"] == $acik_sayfa) {
            // Grup düğmesi renkli görünecek.
            $grupta_mi = true;
        }
    }
    // Toplam sayıyı gruba yazarız.
    $menu_gruplari[$sira]["toplam_sayi"] = $toplam_sayi;
    // Açık sayfa bilgisini gruba yazarız.
    $menu_gruplari[$sira]["aktif"] = $grupta_mi;
}
?>
<!DOCTYPE html>
<!-- Sayfanın dili Türkçe. class: koyu veya acik. css/stil.css renkleri buna göre seçer. -->
<html lang="tr" class="<?php echo $tema; ?>">
<!-- head: sayfanın görünmeyen bilgileri. -->
<head>
    <!-- Türkçe karakterler için. -->
    <meta charset="UTF-8">
    <!-- Telefonda sayfa ekrana sığsın. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tarayıcı sekmesindeki yazı. -->
    <title><?php echo temiz_yaz($sayfa_basligi . " | " . $site_adi); ?></title>
    <!-- Arama motorlarında görünen kısa açıklama. -->
    <meta name="description" content="TamirBul: arızalı cihazınız için onaylı tamircilerden teklif alın, karşılaştırın ve seçin.">
    <!-- Sekmedeki küçük simge: mavi kare içinde anahtar. Dosya yerine SVG yazısı kullanılır. -->
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%232563eb'/%3E%3Cpath d='M19.6 9.4a1.3 1.3 0 0 0 0 1.9l1.1 1.1a1.3 1.3 0 0 0 1.9 0l2.5-2.5a6 6 0 0 1-7.9 7.9l-6.1 6.1a1.9 1.9 0 0 1-2.7-2.7l6.1-6.1a6 6 0 0 1 7.9-7.9z' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E">
    <!-- Inter yazı tipi (Google Fonts). İnternet yoksa sistemin yazı tipi kullanılır, site bozulmaz. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- Yazı tipi dosyalarının geldiği sunucuya önceden bağlanır, sayfa hızlı açılır. -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Yazı tipinin kendisi: normal, yarı kalın, kalın ve çok kalın. -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Tek stil dosyası. -->
    <link rel="stylesheet" href="<?php echo $kok; ?>css/stil.css">
</head>
<!-- body: sayfada görünen her şey. -->
<body>
    <!-- Üst şerit: koyu lacivert, sayfa kayarken üstte kalır. -->
    <header class="navbar">
        <!-- Ortalanmış içerik. -->
        <div class="container navbar-container">
            <!-- Logo. Basınca ana sayfaya gider. -->
            <a class="logo" href="<?php echo $kok; ?>index.php">
                <!-- İlk 5 harf beyaz, kalanı span içinde renkli. -->
                <h1<?php echo yazi_stili("site_adi"); ?>><?php echo temiz_yaz($logo_on); ?><span><?php echo temiz_yaz($logo_arka); ?></span></h1>
            </a>

            <!-- Koyu mod / açık mod düğmesi. js/site.js (6. bölüm) kullanır. -->
            <!-- İki yazı da durur. CSS o anki moda göre birini gizler (koyu modda "Açık mod" görünür). -->
            <button type="button" id="tema-dugmesi" class="tema-dugmesi" aria-label="Koyu ve açık mod arasında geç">
                <!-- Koyu moddayken görünen yazı. -->
                <span class="tema-acik-yazi"><?php echo ikon("gunes"); ?> Açık mod</span>
                <!-- Açık moddayken görünen yazı. -->
                <span class="tema-koyu-yazi"><?php echo ikon("ay"); ?> Koyu mod</span>
            </button>

            <!-- Telefonda menüyü açan düğme. js/site.js kullanır. -->
            <button type="button" id="menu-dugmesi" class="menu-dugmesi"><?php echo ikon("menu"); ?> Menü</button>

            <!-- HARİTA: Role özel açılır menü (HTML) -->
            <!-- Menü. Telefonda gizlenir, Menü düğmesiyle açılır. -->
            <nav id="menu" class="menu">
                <!-- Ana sayfa linki her zaman tek başına durur. -->
                <a class="menu-linki <?php if ($acik_sayfa == "index.php") { echo "aktif"; } ?>" href="<?php echo $kok; ?>index.php"><?php echo ikon("ev"); ?> Ana Sayfa</a>
                <!-- Her grup bir açılır menü. -->
                <?php foreach ($menu_gruplari as $grup) { ?>
                    <!-- Açılır menü kutusu. sag: en sağa yaslanır. aktif: açık sayfa bu grupta. -->
                    <div class="acilir <?php if ($grup["sag"]) { echo "sag"; } ?> <?php if ($grup["aktif"]) { echo "aktif"; } ?>">
                        <!-- Düğme. js/site.js (8. bölüm) basınca listeyi açar. aria-expanded: ekran okuyucuya "açık mı" der. -->
                        <button type="button" class="acilir-dugme" aria-expanded="false">
                            <!-- Grubun ikonu. -->
                            <?php echo ikon($grup["ikon"]); ?>
                            <!-- Grubun adı. Kişi menüsünde giriş yapanın adı. -->
                            <?php echo temiz_yaz($grup["baslik"]); ?>
                            <!-- Grupta bekleyen iş varsa kırmızı sayı. -->
                            <?php if ($grup["toplam_sayi"] > 0) { ?>
                                <!-- Sayı. -->
                                <span class="menu-sayisi"><?php echo $grup["toplam_sayi"]; ?></span>
                            <?php } ?>
                            <!-- Aşağı ok. Menü açılınca CSS döndürür. -->
                            <span class="acilir-ok"><?php echo ikon("asagi"); ?></span>
                        </button>
                        <!-- Açılan liste. 5'ten fazla link varsa iki sütun olur (genis). -->
                        <div class="acilir-liste <?php if (count($grup["linkler"]) > 5) { echo "genis"; } ?>">
                            <!-- Her link bir satır. -->
                            <?php foreach ($grup["linkler"] as $link) { ?>
                                <!-- Link. Açık sayfaysa renkli. -->
                                <a class="acilir-link <?php if ($link["adres"] == $acik_sayfa) { echo "aktif"; } ?>" href="<?php echo $kok . $link["adres"]; ?>">
                                    <!-- Linkin ikonu. -->
                                    <span class="acilir-ikon"><?php echo ikon($link["ikon"]); ?></span>
                                    <!-- Yazı ve açıklama alt alta. -->
                                    <span class="acilir-yazi">
                                        <!-- Linkin adı. -->
                                        <strong><?php echo temiz_yaz($link["yazi"]); ?></strong>
                                        <!-- Açıklama varsa küçük yazı. -->
                                        <?php if ($link["aciklama"] != "") { ?>
                                            <!-- Açıklama. -->
                                            <small><?php echo temiz_yaz($link["aciklama"]); ?></small>
                                        <?php } ?>
                                    </span>
                                    <!-- Bekleyen iş varsa sayı. -->
                                    <?php if ($link["sayi"] > 0) { ?>
                                        <!-- Sayı. -->
                                        <span class="menu-sayisi"><?php echo $link["sayi"]; ?></span>
                                    <?php } ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </nav>
        </div>
    </header>

    <!-- Veritabanı kapalıysa sarı uyarı. -->
    <?php if (!$veritabani_hazir) { ?>
        <!-- Uyarı kutusu ve hatanın sebebi. -->
        <div class="uyari">Veritabanına bağlanılamadı. MySQL'i başlatın ve veritabani/tamirbul.sql dosyasını içe aktarın. Ayrıntı: <?php echo temiz_yaz($veritabani_hata); ?></div>
    <?php } ?>

    <!-- Sayfanın asıl içeriği burada başlar. ortak/altlik.php kapatır. -->
    <main>
