<?php
/*
DOSYA: ortak/baslik.php
KİM: Kişi 1
NE YAPAR: Her sayfanın üst kısmı: <head>, logo, menü ve giriş düğmeleri.
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

// Açık olan dosyanın adı. Menüde o link renkli olsun diye. Örnek: hizmetler.php
$acik_dosya = basename($_SERVER["PHP_SELF"]);

// HARİTA: Üst menü linkleri
// Menü linkleri: dosya adi => ekranda yazan ad. Yeni link eklemek için buraya bir satır yazılır.
$menu_linkleri = array(
    "index.php" => "Ana Sayfa",
    "hizmetler.php" => "Hizmetler",
    "tamirciler.php" => "Tamirciler",
    "talep-olustur.php" => "Talep Oluştur",
    "talep-takip.php" => "Talep Takip",
    "iletisim.php" => "İletişim"
);

// Giriş yapan kişinin panel linki. Role göre değişir.
$panel_adresi = "musteri/panel.php";
// Panel düğmesinin yazısı.
$panel_yazisi = "Hesabım";
// Yönetici ise:
if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "admin") {
    // Yönetim paneli.
    $panel_adresi = "admin/panel.php";
    // Düğme yazısı.
    $panel_yazisi = "Yönetim";
}
// Tamirci ise:
if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "tamirci") {
    // Tamirci paneli.
    $panel_adresi = "tamirci/panel.php";
    // Düğme yazısı.
    $panel_yazisi = "Tamirci Paneli";
}
?>
<!DOCTYPE html>
<!-- Sayfanın dili Türkçe. -->
<html lang="tr">
<!-- head: sayfanın görünmeyen bilgileri. -->
<head>
    <!-- Türkçe karakterler için. -->
    <meta charset="UTF-8">
    <!-- Telefonda sayfa ekrana sığsın. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tarayıcı sekmesindeki yazı. -->
    <title><?php echo temiz_yaz($sayfa_basligi . " | " . $site_adi); ?></title>
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
                <h1><?php echo temiz_yaz($logo_on); ?><span><?php echo temiz_yaz($logo_arka); ?></span></h1>
            </a>

            <!-- Telefonda menüyü açan düğme. js/site.js kullanır. -->
            <button type="button" id="menu-dugmesi" class="menu-dugmesi">Menü</button>

            <!-- Linkler ve giriş düğmeleri. Telefonda gizlenir. -->
            <div id="menu" class="menu">
                <!-- Sayfa linkleri. -->
                <nav class="nav-menu">
                    <!-- Link listesi. -->
                    <ul>
                        <!-- Dizideki her link için bir satır. -->
                        <?php foreach ($menu_linkleri as $dosya => $yazi) { ?>
                            <!-- Açık sayfanın linki "active" sınıfı ile renkli olur. -->
                            <li><a href="<?php echo $kok . $dosya; ?>" <?php if ($acik_dosya == $dosya && $kok == "") { echo 'class="active"'; } ?>><?php echo $yazi; ?></a></li>
                        <?php } ?>
                    </ul>
                </nav>

                <!-- Giriş yapıldıysa: ad, panel ve çıkış. -->
                <?php if (giris_yapildi_mi()) { ?>
                    <!-- Kullanıcı kutusu. -->
                    <div class="user-profile-menu">
                        <!-- Giriş yapan kişinin adı. -->
                        <span class="user-name"><?php echo temiz_yaz($_SESSION["ad"] . " " . $_SESSION["soyad"]); ?></span>
                        <!-- Role göre panel linki. -->
                        <a class="btn btn-sm btn-outline" href="<?php echo $kok . $panel_adresi; ?>"><?php echo $panel_yazisi; ?></a>
                        <!-- Çıkış. -->
                        <a class="btn btn-sm btn-secondary" href="<?php echo $kok; ?>cikis.php">Çıkış Yap</a>
                    </div>
                <!-- Giriş yoksa: giriş ve kayıt düğmeleri. -->
                <?php } else { ?>
                    <!-- Düğme kutusu. -->
                    <div class="auth-buttons">
                        <!-- Giriş. -->
                        <a class="btn btn-sm btn-outline" href="<?php echo $kok; ?>giris.php">Giriş Yap</a>
                        <!-- Müşteri kaydı. -->
                        <a class="btn btn-sm btn-primary" href="<?php echo $kok; ?>musteri-kayit.php">Müşteri Ol</a>
                        <!-- Tamirci kaydı. -->
                        <a class="btn btn-sm btn-secondary" href="<?php echo $kok; ?>tamirci-kayit.php">Tamirci Katıl</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </header>

    <!-- Veritabanı kapalıysa sarı uyarı. -->
    <?php if (!$veritabani_hazir) { ?>
        <!-- Uyarı kutusu ve hatanın sebebi. -->
        <div class="uyari">Veritabanına bağlanılamadı. MySQL'i başlatın ve veritabani/tamirbul.sql dosyasını içe aktarın. Ayrıntı: <?php echo temiz_yaz($veritabani_hata); ?></div>
    <?php } ?>

    <!-- Sayfanın asıl içeriği burada başlar. ortak/altlik.php kapatır. -->
    <main>
