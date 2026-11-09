<?php
/*
DOSYA: ortak/altlik.php
KİM: Kişi 1
NE YAPAR: Her sayfanın en altı. baslik.php'nin açtığı <main> burada kapanır.
Yazıların hepsi site_ayarlari tablosundan gelir.
yazi_stili(): yönetimde seçilen renk, boyut gibi stil. Seçilmediyse boş yazı verir.
*/

// Sayfa altındaki yazılar. İkinci değer veritabanı kapalıysa görünen yedek yazıdır.
$alt_site_adi = ayar_getir("site_adi", "TamirBul");
// Kısa tanıtım yazısı.
$alt_aciklama = ayar_getir("altlik_aciklama", "");
// Telefon.
$alt_telefon = ayar_getir("telefon", "");
// E-posta.
$alt_eposta = ayar_getir("eposta", "");
// Adres.
$alt_adres = ayar_getir("adres", "");
// Çalışma saatleri.
$alt_saat = ayar_getir("calisma_saatleri", "");
// En alttaki küçük not.
$alt_not = ayar_getir("altlik_not", "");
?>
    </main>

    <!-- Sayfanın en altındaki koyu şerit. id="altlik": yönetimdeki "Sayfada gör" linki buraya kaydırır. -->
    <footer id="altlik" class="footer">
        <!-- Üç sütun: tanıtım, sayfalar, iletişim. -->
        <div class="container footer-container">
            <!-- 1. sütun: site adı ve tanıtım. -->
            <div>
                <!-- Site adı. -->
                <strong<?php echo yazi_stili("site_adi"); ?>><?php echo temiz_yaz($alt_site_adi); ?></strong>
                <!-- Tanıtım yazısı. -->
                <p<?php echo yazi_stili("altlik_aciklama"); ?>><?php echo temiz_yaz($alt_aciklama); ?></p>
            </div>
            <!-- 2. sütun: sayfa linkleri. -->
            <div>
                <!-- Sütun başlığı. -->
                <strong>Sayfalar</strong>
                <!-- Hakkımızda linki. -->
                <p><a href="<?php echo $kok; ?>hakkimizda.php">Hakkımızda</a></p>
                <!-- Hizmetler linki. -->
                <p><a href="<?php echo $kok; ?>hizmetler.php">Hizmetler</a></p>
                <!-- Tamirciler linki. -->
                <p><a href="<?php echo $kok; ?>tamirciler.php">Tamirciler</a></p>
                <!-- Talep takip linki. -->
                <p><a href="<?php echo $kok; ?>talep-takip.php">Talep takip</a></p>
                <!-- Tamirci kayıt linki. -->
                <p><a href="<?php echo $kok; ?>tamirci-kayit.php">Tamirci olarak katıl</a></p>
            </div>
            <!-- 3. sütun: iletişim bilgileri. -->
            <div>
                <!-- Sütun başlığı. -->
                <strong>İletişim</strong>
                <!-- Telefon. -->
                <p><?php echo ikon("ahize"); ?> <span<?php echo yazi_stili("telefon"); ?>><?php echo temiz_yaz($alt_telefon); ?></span></p>
                <!-- E-posta. -->
                <p><?php echo ikon("zarf"); ?> <span<?php echo yazi_stili("eposta"); ?>><?php echo temiz_yaz($alt_eposta); ?></span></p>
                <!-- Adres. -->
                <p><?php echo ikon("konum"); ?> <span<?php echo yazi_stili("adres"); ?>><?php echo temiz_yaz($alt_adres); ?></span></p>
                <!-- Çalışma saati. -->
                <p><?php echo ikon("saat"); ?> <span<?php echo yazi_stili("calisma_saatleri"); ?>><?php echo temiz_yaz($alt_saat); ?></span></p>
            </div>
        </div>
        <!-- En alttaki küçük not. -->
        <div class="container alt-not">
            <!-- Not yazısı. -->
            <p<?php echo yazi_stili("altlik_not"); ?>><?php echo temiz_yaz($alt_not); ?></p>
        </div>
    </footer>

    <!-- HARİTA: Ortak pop-up penceresi (dialog) -->
    <!-- Sitedeki bütün pop-up'lar bu tek pencereyi kullanır: onay sorusu, bilgi, büyük resim. -->
    <!-- <dialog> HTML'in hazır pencere etiketidir. js/site.js (9. bölüm) içini doldurup açar. -->
    <dialog id="ortak-pencere" class="pencere">
        <!-- Pencerenin üst kısmı: ikon ve başlık. -->
        <div class="pencere-ust">
            <!-- Yuvarlak içinde işaret: ? (soru), i (bilgi), + (büyük resim). JS değiştirir. -->
            <span class="pencere-ikonu">i</span>
            <!-- Başlık. -->
            <h3 class="pencere-baslik">Bilgi</h3>
        </div>
        <!-- İçerik: JS doldurur. -->
        <div class="pencere-icerik"></div>
        <!-- Düğmeler. -->
        <div class="pencere-dugmeleri">
            <!-- Kapat. -->
            <button type="button" class="btn btn-outline btn-sm pencere-kapat">Kapat</button>
            <!-- Onay düğmesi. Sadece soru sorulunca görünür. -->
            <button type="button" class="btn btn-primary btn-sm pencere-evet">Evet</button>
        </div>
    </dialog>

    <!-- Menü, bağlı listeler, slayt ve sohbet için tek javascript dosyası. -->
    <script src="<?php echo $kok; ?>js/site.js"></script>
</body>
</html>
