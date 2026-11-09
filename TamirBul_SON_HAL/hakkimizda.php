<?php
/*
SAYFA: Hakkımızda
DOSYA: hakkimizda.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Üstte fotoğraflı kapak (başlık ve kısa söz)
- Hikayemiz: başlık ve uzun metin sayfa_icerikleri tablosundan gelir, yanında fotoğraf
- Rakamlarla TamirBul: tamirci, hizmet, kategori, talep sayıları veritabanından canlı sayılır
- Misyon, vizyon, değerlerimiz kartları
- Neden TamirBul: altı özellik
- Nasıl çalışır: adimlar tablosundan gelen adımlar
- Proje ekibi: dört kişinin görevleri
- Sık sorulan sorular (tıklayınca açılır)
- En altta talep oluşturma ve iletişim çağrısı
- Yönetim: admin/sayfa-icerik.php (hikaye yazısı ve stili), admin/adimlar.php (adımlar)
*/

// Sekme başlığı.
$sayfa_basligi = "Hakkımızda";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// HARİTA: Hakkımızda yazısı (sayfa_icerikleri tablosu)
// Hakkımızda yazısını okuruz.
$sayfa = tek_satir("SELECT * FROM sayfa_icerikleri WHERE sayfa = 'hakkimizda'");
// Bulunamazsa görünecek yedek yazı.
if (!$sayfa) {
    // Yedek başlık ve metin.
    $sayfa = array("baslik" => "Hakkımızda", "icerik" => "Yazı veritabanı açılınca burada görünecek.");
}

// HARİTA: Hakkımızda sayaçları (canlı rakamlar)
// Yönetici onayından geçmiş tamirci sayısı.
$tamirci_sayisi = sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 1");
// Sitede görünen (aktif) hizmet sayısı.
$hizmet_sayisi = sayi_getir("SELECT COUNT(*) FROM hizmetler WHERE aktif = 1");
// Cihaz kategorisi sayısı.
$kategori_sayisi = sayi_getir("SELECT COUNT(*) FROM kategoriler");
// Şimdiye kadar açılan talep sayısı.
$talep_sayisi = sayi_getir("SELECT COUNT(*) FROM talepler");

// Nasıl çalışır adımları, sıra numarasına göre (ana sayfadakiyle aynı tablo).
$adimlar = satirlar("SELECT * FROM adimlar ORDER BY sira, id");

// HARİTA: Hakkımızda fotoğrafları
// Kapak fotoğrafı: tezgâhta tamir yapılan bir cihaz. İnternetten (Unsplash) gelir, dosya indirilmez.
$kapak_resmi = "https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1600&q=75";
// Hikayemiz bölümünün yanındaki fotoğraf: ekip çalışması.
$hikaye_resmi = "https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1000&q=75";
// En alttaki çağrı bölümünün arka plan fotoğrafı: el sıkışma.
$cagri_resmi = "https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1600&q=70";

// Üst menü.
include "ortak/baslik.php";
?>

<!-- HARİTA: Sayfa kapağı (fotoğraflı başlık alanı) -->
<!-- Kapak: arkada fotoğraf, önünde koyu perde ve yazılar. arka_plan() style="background-image..." yazar. -->
<section class="sayfa-kapagi" <?php echo arka_plan($kapak_resmi); ?>>
    <!-- Ortadaki yazılar. -->
    <div class="kapsayici sayfa-kapagi-ici">
        <!-- Küçük hap yazı. -->
        <div class="hero-rozet"><?php echo ikon("kalkan"); ?> <span<?php echo yazi_stili("hakkimizda_rozet"); ?>><?php echo temiz_yaz(yazi_getir("hakkimizda_rozet")); ?></span></div>
        <!-- Büyük başlık. yazi_getir: Yönetim > Site yazıları > Hakkımızda'dan değişir. -->
        <h1<?php echo yazi_stili("hakkimizda_kapak_baslik"); ?>><?php echo temiz_yaz(yazi_getir("hakkimizda_kapak_baslik")); ?></h1>
        <!-- Kısa açıklama. -->
        <p<?php echo yazi_stili("hakkimizda_kapak_aciklama"); ?>><?php echo temiz_yaz(yazi_getir("hakkimizda_kapak_aciklama")); ?></p>
    </div>
</section>

<!-- HARİTA: Hikayemiz (veritabanındaki yazı + fotoğraf) -->
<section class="hakkimizda-bolum">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- İki sütun: solda yazı, sağda fotoğraf. Dar ekranda alt alta. -->
        <div class="hikaye-izgara">
            <!-- Sol: yazı. -->
            <div class="hikaye-yazi">
                <!-- Küçük üst yazı. -->
                <span class="ust-yazi">Hikayemiz</span>
                <!-- Başlık. yazi_stili: yönetimde seçilen renk, boyut gibi stil. -->
                <h2<?php echo yazi_stili("sayfa_hakkimizda_baslik"); ?>><?php echo temiz_yaz($sayfa["baslik"]); ?></h2>
                <!-- Metin. nl2br: Enter tuşlarını satır başı yapar. Yönetici admin/sayfa-icerik.php sayfasından değiştirir. -->
                <div class="hikaye-metni"<?php echo yazi_stili("sayfa_hakkimizda_icerik"); ?>><?php echo nl2br(temiz_yaz($sayfa["icerik"])); ?></div>
                <!-- Kısa maddeler. -->
                <ul class="tik-listesi">
                    <!-- Birinci madde. -->
                    <li><?php echo ikon("onay"); ?> Talep açmak ve teklif almak ücretsiz</li>
                    <!-- İkinci madde. -->
                    <li><?php echo ikon("onay"); ?> Her tamirci yönetici onayından geçer</li>
                    <!-- Üçüncü madde. -->
                    <li><?php echo ikon("onay"); ?> Son kararı her zaman müşteri verir</li>
                </ul>
            </div>
            <!-- Sağ: fotoğraf. loading="lazy": sayfa aşağı kayınca yüklenir, sayfa hızlı açılır. -->
            <div class="hikaye-resim">
                <!-- Fotoğraf. -->
                <img src="<?php echo temiz_yaz($hikaye_resmi); ?>" alt="Masa başında birlikte çalışan ekip" loading="lazy">
                <!-- Fotoğrafın üstündeki küçük kutu. -->
                <div class="resim-rozeti"><?php echo ikon("anahtar"); ?> <span><strong><?php echo $kategori_sayisi; ?> kategori</strong> tek çatı altında</span></div>
            </div>
        </div>
    </div>
</section>

<!-- HARİTA: Rakamlarla TamirBul (sayaçlar) -->
<section class="rakam-seridi">
    <!-- Kapsayıcı. -->
    <div class="kapsayici rakam-izgara">
        <!-- Birinci rakam. -->
        <div class="rakam-kutu">
            <!-- İkon. -->
            <?php echo ikon("kisiler"); ?>
            <!-- Sayı. -->
            <strong><?php echo $tamirci_sayisi; ?></strong>
            <!-- Açıklama. -->
            <span>Onaylı tamirci</span>
        </div>
        <!-- İkinci rakam. -->
        <div class="rakam-kutu">
            <!-- İkon. -->
            <?php echo ikon("anahtar"); ?>
            <!-- Sayı. -->
            <strong><?php echo $hizmet_sayisi; ?></strong>
            <!-- Açıklama. -->
            <span>Hizmet çeşidi</span>
        </div>
        <!-- Üçüncü rakam. -->
        <div class="rakam-kutu">
            <!-- İkon. -->
            <?php echo ikon("katman"); ?>
            <!-- Sayı. -->
            <strong><?php echo $kategori_sayisi; ?></strong>
            <!-- Açıklama. -->
            <span>Cihaz kategorisi</span>
        </div>
        <!-- Dördüncü rakam. -->
        <div class="rakam-kutu">
            <!-- İkon. -->
            <?php echo ikon("liste"); ?>
            <!-- Sayı. -->
            <strong><?php echo $talep_sayisi; ?></strong>
            <!-- Açıklama. -->
            <span>Açılan tamir talebi</span>
        </div>
    </div>
</section>

<!-- HARİTA: Misyon, vizyon, değerler -->
<section class="hakkimizda-bolum">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title">Neye inanıyoruz?</h2>
        <!-- Üç kart yan yana. -->
        <div class="ozellik-izgara uclu">
            <!-- Misyon kartı. -->
            <div class="ozellik-kart">
                <!-- İkon kutusu. -->
                <div class="kart-ikonu"><?php echo ikon("pusula"); ?></div>
                <!-- Başlık. -->
                <h3>Misyonumuz</h3>
                <!-- Açıklama. -->
                <p<?php echo yazi_stili("hakkimizda_misyon"); ?>><?php echo temiz_yaz(yazi_getir("hakkimizda_misyon")); ?></p>
            </div>
            <!-- Vizyon kartı. -->
            <div class="ozellik-kart">
                <!-- İkon kutusu. -->
                <div class="kart-ikonu"><?php echo ikon("ampul"); ?></div>
                <!-- Başlık. -->
                <h3>Vizyonumuz</h3>
                <!-- Açıklama. -->
                <p<?php echo yazi_stili("hakkimizda_vizyon"); ?>><?php echo temiz_yaz(yazi_getir("hakkimizda_vizyon")); ?></p>
            </div>
            <!-- Değerler kartı. -->
            <div class="ozellik-kart">
                <!-- İkon kutusu. -->
                <div class="kart-ikonu"><?php echo ikon("kalkan"); ?></div>
                <!-- Başlık. -->
                <h3>Değerlerimiz</h3>
                <!-- Açıklama. -->
                <p<?php echo yazi_stili("hakkimizda_degerler"); ?>><?php echo temiz_yaz(yazi_getir("hakkimizda_degerler")); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- HARİTA: Neden TamirBul (özellikler) -->
<section class="hakkimizda-bolum zemin-farkli">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title">Neden TamirBul?</h2>
        <!-- Altı özellik kartı. -->
        <div class="ozellik-izgara">
            <!-- 1. özellik. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("onay"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Ücretsiz teklif</h3>
                    <!-- Açıklama. -->
                    <p>Talep açmak, teklif almak ve teklifleri karşılaştırmak tamamen ücretsizdir.</p>
                </div>
            </div>
            <!-- 2. özellik. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("kalkan"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Onaylı tamirciler</h3>
                    <!-- Açıklama. -->
                    <p>Tamirciler başvurur, yönetici bilgilerini kontrol eder. Onaysız tamirci teklif veremez.</p>
                </div>
            </div>
            <!-- 3. özellik. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("yildiz"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Gerçek müşteri puanları</h3>
                    <!-- Açıklama. -->
                    <p>Puanı sadece işi tamamlanan müşteri verir. Tamirci kendi puanını değiştiremez.</p>
                </div>
            </div>
            <!-- 4. özellik. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("ara"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Takip kodu</h3>
                    <!-- Açıklama. -->
                    <p>Her talebe özel kod verilir. Talebinizin hangi aşamada olduğunu istediğiniz an görürsünüz.</p>
                </div>
            </div>
            <!-- 5. özellik. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("mesaj"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Site içi mesajlaşma</h3>
                    <!-- Açıklama. -->
                    <p>Teklifini kabul ettiğiniz tamirciyle sitenin içinden yazışır, cihazın teslimini konuşursunuz.</p>
                </div>
            </div>
            <!-- 6. özellik. -->
            <div class="ozellik-satir">
                <!-- İkon. -->
                <div class="kart-ikonu"><?php echo ikon("takvim"); ?></div>
                <!-- Yazılar. -->
                <div>
                    <!-- Başlık. -->
                    <h3>Randevu takibi</h3>
                    <!-- Açıklama. -->
                    <p>Tamirci randevu tarihini sisteme girer, siz panelinizden takip edersiniz.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HARİTA: Hakkımızda nasıl çalışır (adimlar tablosu) -->
<section class="hakkimizda-bolum">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title">Dört adımda tamir</h2>
        <!-- Adımlar yan yana, aralarında çizgi. -->
        <ol class="adim-cizgisi">
            <!-- Her adım bir madde. $sira 0'dan başlar, ekranda +1 yazarız. -->
            <?php foreach ($adimlar as $sira => $adim) { ?>
                <!-- Adım. -->
                <li>
                    <!-- Adım numarası yuvarlak içinde. -->
                    <span class="adim-no"><?php echo $sira + 1; ?></span>
                    <!-- Adım başlığı. -->
                    <h3><?php echo temiz_yaz($adim["baslik"]); ?></h3>
                    <!-- Adım açıklaması. -->
                    <p><?php echo temiz_yaz($adim["aciklama"]); ?></p>
                </li>
            <?php } ?>
        </ol>
    </div>
</section>

<!-- HARİTA: Proje ekibi -->
<section class="hakkimizda-bolum zemin-farkli">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title">Proje ekibi</h2>
        <!-- Açıklama. -->
        <p class="bolum-aciklamasi">TamirBul'u dört bilgisayar programcılığı öğrencisi birlikte geliştirdi. Her birimiz sitenin bir bölümünden sorumluyduk.</p>
        <!-- Dört kart. -->
        <div class="ozellik-izgara dortlu">
            <!-- 1. kişi. -->
            <div class="ekip-kart">
                <!-- Yuvarlak ikon. -->
                <div class="ekip-avatar"><?php echo ikon("duzen"); ?></div>
                <!-- Görev adı. -->
                <h3>Ön yüz ve tasarım</h3>
                <!-- Kim. -->
                <span class="ekip-kim">Kişi 1</span>
                <!-- Ne yaptı. -->
                <p>Ana sayfa, hizmetler, tamirciler, menü ve sitenin bütün görünüşü.</p>
            </div>
            <!-- 2. kişi. -->
            <div class="ekip-kart">
                <!-- Yuvarlak ikon. -->
                <div class="ekip-avatar"><?php echo ikon("grafik"); ?></div>
                <!-- Görev adı. -->
                <h3>Müşteri ve tamirci panelleri</h3>
                <!-- Kim. -->
                <span class="ekip-kim">Kişi 2</span>
                <!-- Ne yaptı. -->
                <p>Talepler, teklifler, randevular ve iş durumu ekranları.</p>
            </div>
            <!-- 3. kişi. -->
            <div class="ekip-kart">
                <!-- Yuvarlak ikon. -->
                <div class="ekip-avatar"><?php echo ikon("kilit"); ?></div>
                <!-- Görev adı. -->
                <h3>Güvenlik ve altyapı</h3>
                <!-- Kim. -->
                <span class="ekip-kim">Kişi 3</span>
                <!-- Ne yaptı. -->
                <p>Giriş ve kayıt, talep formu, mesajlaşma, veritabanı bağlantısı ve güvenlik.</p>
            </div>
            <!-- 4. kişi. -->
            <div class="ekip-kart">
                <!-- Yuvarlak ikon. -->
                <div class="ekip-avatar"><?php echo ikon("ayar"); ?></div>
                <!-- Görev adı. -->
                <h3>Yönetim paneli</h3>
                <!-- Kim. -->
                <span class="ekip-kim">Kişi 4</span>
                <!-- Ne yaptı. -->
                <p>Site içeriğinin yönetimi, tamirci onayları ve işlem kayıtları.</p>
            </div>
        </div>
    </div>
</section>

<!-- HARİTA: Sık sorulan sorular -->
<!-- id="sss": iletişim sayfası buraya link verir (hakkimizda.php#sss). -->
<section id="sss" class="hakkimizda-bolum">
    <!-- Dar kapsayıcı: sorular rahat okunsun. -->
    <div class="kapsayici dar">
        <!-- Bölüm başlığı. -->
        <h2 class="section-title">Sık sorulan sorular</h2>
        <!-- Altı soru. Sorular ve cevaplar Yönetim > Site yazıları > Sık sorulan sorular'dan değişir. -->
        <?php for ($soru_no = 1; $soru_no <= 6; $soru_no++) { ?>
            <?php
            // Sorunun anahtarı. Örnek: sss_1_soru
            $soru_anahtari = "sss_" . $soru_no . "_soru";
            // Cevabın anahtarı. Örnek: sss_1_cevap
            $cevap_anahtari = "sss_" . $soru_no . "_cevap";
            // Sorunun yazısı.
            $soru = yazi_getir($soru_anahtari);
            ?>
            <!-- Soru boş bırakıldıysa bu soru görünmez. -->
            <?php if ($soru != "") { ?>
                <!-- details: tıklayınca açılan kutu. JavaScript gerekmez, tarayıcı kendisi açar kapatır. -->
                <details class="sss">
                    <!-- Soru. summary: her zaman görünen kısım. -->
                    <summary<?php echo yazi_stili($soru_anahtari); ?>><?php echo temiz_yaz($soru); ?></summary>
                    <!-- Cevap. nl2br: Enter tuşları satır başı olur. -->
                    <p<?php echo yazi_stili($cevap_anahtari); ?>><?php echo nl2br(temiz_yaz(yazi_getir($cevap_anahtari))); ?></p>
                </details>
            <?php } ?>
        <?php } ?>
    </div>
</section>

<!-- HARİTA: Hakkımızda alt çağrı bölümü -->
<section class="cagri-bolumu" <?php echo arka_plan($cagri_resmi); ?>>
    <!-- Kapsayıcı. -->
    <div class="kapsayici cagri-ici">
        <!-- Başlık. Yönetim > Site yazıları > Hakkımızda'dan değişir. -->
        <h2<?php echo yazi_stili("hakkimizda_cagri_baslik"); ?>><?php echo temiz_yaz(yazi_getir("hakkimizda_cagri_baslik")); ?></h2>
        <!-- Açıklama. -->
        <p<?php echo yazi_stili("hakkimizda_cagri_aciklama"); ?>><?php echo temiz_yaz(yazi_getir("hakkimizda_cagri_aciklama")); ?></p>
        <!-- İki düğme. -->
        <div class="hero-actions">
            <!-- Talep oluştur. -->
            <a class="btn btn-hero" href="talep-olustur.php">Hemen Talep Oluştur</a>
            <!-- İletişim. -->
            <a class="btn btn-outline btn-hero" href="iletisim.php">Bize Ulaşın</a>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
