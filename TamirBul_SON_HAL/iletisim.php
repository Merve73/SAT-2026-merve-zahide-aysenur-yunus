<?php
/*
SAYFA: İletişim
DOSYA: iletisim.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Üstte fotoğraflı kapak
- Dört bilgi kartı: telefon, e-posta, adres, çalışma saati (site yazılarından). Telefona ve e-postaya tıklanabilir.
- Solda form: ad, e-posta, konu, mesaj. mesajlar tablosuna yazar, yönetici okur.
- Sağda: hangi konularda yazılabilir, sık sorulan sorular linki, tamirci başvurusu linki
*/

// Sekme başlığı.
$sayfa_basligi = "İletişim";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Mesaj gönderildiyse adres ?gonderildi=1 olur.
if (isset($_GET["gonderildi"])) {
    // Teşekkür yazısı.
    $tamam = "Mesajınız alındı, teşekkür ederiz. En kısa sürede e-posta adresinize dönüş yapacağız.";
}
// Formdaki ad.
$ad = formdan_al("ad");
// E-posta.
$eposta = formdan_al("eposta");
// Konu.
$konu = formdan_al("konu");
// Mesaj.
$mesaj = formdan_al("mesaj");

// HARİTA: İletişim formu (mesajlar tablosuna kayıt)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Boş kutu varsa:
    if ($ad == "" || $eposta == "" || $konu == "" || $mesaj == "") {
        // Hata.
        $hata = "Bütün kutuları doldurun.";
    // E-postada @ yoksa:
    } else if (!eposta_dogru_mu($eposta)) {
        // Hata.
        $hata = "E-posta adresini doğru yazın. Örnek: ali@ornek.com";
    } else {
        // Mesajı kaydederiz. okundu = 0: yönetici daha okumadı.
        kaydet("mesajlar", array("ad" => $ad, "eposta" => $eposta, "konu" => $konu, "mesaj" => $mesaj, "okundu" => 0, "tarih" => simdi()));
        // Gelen mesajı kayda yazarız.
        log_yaz("İletişim mesajı", $konu);
        // Sayfayı boş formla yeniden açarız, üstte teşekkür yazısı çıkar.
        yonlendir("iletisim.php?gonderildi=1");
    }
}

// HARİTA: İletişim bilgileri (site yazılarından)
// Telefon numarası. Yönetici admin/ayarlar.php sayfasından değiştirir.
$telefon = ayar_getir("telefon", "");
// Telefon linki için boşluklar silinir. Örnek: "0501 056" -> "0501056"
$telefon_linki = str_replace(" ", "", $telefon);
// E-posta adresi.
$site_eposta = ayar_getir("eposta", "");
// Açık adres.
$adres = ayar_getir("adres", "");
// Çalışma saatleri.
$saat = ayar_getir("calisma_saatleri", "");
// Kapak fotoğrafı: elinde telefon tutan kişi. İnternetten (Unsplash) gelir.
$kapak_resmi = "https://images.unsplash.com/photo-1512428559087-560fa5ceab42?auto=format&fit=crop&w=1600&q=75";

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa kapağı: arkada fotoğraf, önünde başlık. -->
<section class="sayfa-kapagi" <?php echo arka_plan($kapak_resmi); ?>>
    <!-- Ortadaki yazılar. -->
    <div class="kapsayici sayfa-kapagi-ici">
        <!-- Küçük hap yazı. -->
        <div class="hero-rozet"><?php echo ikon("mesaj"); ?> <span<?php echo yazi_stili("iletisim_rozet"); ?>><?php echo temiz_yaz(yazi_getir("iletisim_rozet")); ?></span></div>
        <!-- Başlık. -->
        <h1>İletişim</h1>
        <!-- Açıklama. Yönetim > Site yazıları > İletişim'den değişir. -->
        <p<?php echo yazi_stili("iletisim_kapak_aciklama"); ?>><?php echo temiz_yaz(yazi_getir("iletisim_kapak_aciklama")); ?></p>
    </div>
</section>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- HARİTA: İletişim bilgi kartları -->
        <!-- Dört kart yan yana. Dar ekranda alt alta. -->
        <div class="iletisim-kartlari">
            <!-- Telefon kartı. tel: linki telefonda tıklayınca arama ekranını açar. -->
            <a class="iletisim-kart" href="tel:<?php echo temiz_yaz($telefon_linki); ?>">
                <!-- İkon. -->
                <span class="kart-ikonu"><?php echo ikon("ahize"); ?></span>
                <!-- Kart adı. -->
                <strong>Telefon</strong>
                <!-- Değer. span: yönetimde seçilen stil sadece yazının kendisine uygulanır. -->
                <span<?php echo yazi_stili("telefon"); ?>><?php echo temiz_yaz($telefon); ?></span>
            </a>
            <!-- E-posta kartı. mailto: linki tıklayınca e-posta programını açar. -->
            <a class="iletisim-kart" href="mailto:<?php echo temiz_yaz($site_eposta); ?>">
                <!-- İkon. -->
                <span class="kart-ikonu"><?php echo ikon("zarf"); ?></span>
                <!-- Kart adı. -->
                <strong>E-posta</strong>
                <!-- Değer. -->
                <span<?php echo yazi_stili("eposta"); ?>><?php echo temiz_yaz($site_eposta); ?></span>
            </a>
            <!-- Adres kartı. Link değil, sadece bilgi. -->
            <div class="iletisim-kart">
                <!-- İkon. -->
                <span class="kart-ikonu"><?php echo ikon("konum"); ?></span>
                <!-- Kart adı. -->
                <strong>Adres</strong>
                <!-- Değer. -->
                <span<?php echo yazi_stili("adres"); ?>><?php echo temiz_yaz($adres); ?></span>
            </div>
            <!-- Saat kartı. -->
            <div class="iletisim-kart">
                <!-- İkon. -->
                <span class="kart-ikonu"><?php echo ikon("saat"); ?></span>
                <!-- Kart adı. -->
                <strong>Çalışma saatleri</strong>
                <!-- Değer. -->
                <span<?php echo yazi_stili("calisma_saatleri"); ?>><?php echo temiz_yaz($saat); ?></span>
            </div>
        </div>

        <!-- İki kutu yan yana: solda form, sağda yardım bilgileri. -->
        <div class="iletisim-izgara">
            <!-- Sol: iletişim formu. -->
            <form class="form-kutu" method="post" action="iletisim.php">
                <!-- Form başlığı. -->
                <h2>Bize yazın</h2>
                <!-- Kısa açıklama. -->
                <p class="giris-metni">Mesajınız doğrudan yönetici paneline düşer. Cevabımızı yazdığınız e-posta adresine göndeririz.</p>
                <!-- Hata ve başarı kutuları. -->
                <?php include "ortak/mesajlar.php"; ?>
                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                <?php echo guvenlik_alani(); ?>
                <!-- Ad. -->
                <label for="ad">Adınız</label>
                <!-- Ad kutusu. -->
                <input id="ad" name="ad" maxlength="80" type="text" placeholder="Adınız ve soyadınız" value="<?php echo temiz_yaz($ad); ?>">
                <!-- E-posta. -->
                <label for="eposta">E-posta</label>
                <!-- E-posta kutusu. Cevabı bu adrese yazarız. -->
                <input id="eposta" name="eposta" maxlength="100" type="text" placeholder="ornek@eposta.com" value="<?php echo temiz_yaz($eposta); ?>">
                <!-- Konu. -->
                <label for="konu">Konu</label>
                <!-- Konu kutusu. list="konu-onerileri": yazarken aşağıdaki hazır konular önerilir. -->
                <input id="konu" name="konu" maxlength="150" type="text" list="konu-onerileri" placeholder="Örnek: Şifremi unuttum" value="<?php echo temiz_yaz($konu); ?>">
                <!-- Hazır konu önerileri. Kişi seçebilir ya da kendisi yazabilir. -->
                <datalist id="konu-onerileri">
                    <!-- Öneri 1. -->
                    <option value="Talebim hakkında">
                    <!-- Öneri 2. -->
                    <option value="Şifremi unuttum">
                    <!-- Öneri 3. -->
                    <option value="Tamirci başvurusu">
                    <!-- Öneri 4. -->
                    <option value="Şikâyet">
                    <!-- Öneri 5. -->
                    <option value="Öneri">
                </datalist>
                <!-- Mesaj. -->
                <label for="mesaj">Mesaj</label>
                <!-- Mesaj kutusu. -->
                <textarea id="mesaj" name="mesaj" placeholder="Mesajınızı buraya yazın. Talebinizle ilgiliyse takip kodunu da ekleyin."><?php echo temiz_yaz($mesaj); ?></textarea>
                <!-- Gönder düğmesi. -->
                <p><button class="btn btn-primary" type="submit"><?php echo ikon("zarf"); ?> Mesajı Gönder</button></p>
            </form>

            <!-- Sağ: yardım bilgileri. -->
            <div class="iletisim-yan">
                <!-- Konular kutusu. -->
                <div class="bilgi-kutu">
                    <!-- Başlık. -->
                    <h2>Hangi konularda yazabilirsiniz?</h2>
                    <!-- Liste. -->
                    <ul class="tik-listesi">
                        <!-- Konu 1. -->
                        <li><?php echo ikon("onay"); ?> Talebiniz veya teklifinizle ilgili sorular</li>
                        <!-- Konu 2. -->
                        <li><?php echo ikon("onay"); ?> Şifrenizi unuttuysanız</li>
                        <!-- Konu 3. -->
                        <li><?php echo ikon("onay"); ?> Bir tamirciyle yaşadığınız sorun</li>
                        <!-- Konu 4. -->
                        <li><?php echo ikon("onay"); ?> Siteyle ilgili öneri ve fikirleriniz</li>
                    </ul>
                </div>
                <!-- Sık sorulan sorular kutusu. -->
                <div class="bilgi-kutu">
                    <!-- Başlık. -->
                    <h2><?php echo ikon("soru"); ?> Cevabı belki hazırdır</h2>
                    <!-- Açıklama. -->
                    <p>Ücretler, tamirci onayı ve talep takibi gibi soruların cevapları sık sorulan sorularda.</p>
                    <!-- Link: hakkımızda sayfasının sorular bölümüne gider. -->
                    <a class="btn btn-sm btn-primary" href="hakkimizda.php#sss">Sık Sorulan Sorular</a>
                </div>
                <!-- Tamirci kutusu. -->
                <div class="bilgi-kutu">
                    <!-- Başlık. -->
                    <h2><?php echo ikon("canta"); ?> Tamirci misiniz?</h2>
                    <!-- Açıklama. -->
                    <p>Mesaj yazmanıza gerek yok, başvurunuzu doğrudan yapabilirsiniz.</p>
                    <!-- Link. -->
                    <a class="btn btn-sm btn-primary" href="tamirci-kayit.php">Tamirci Olarak Katıl</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
