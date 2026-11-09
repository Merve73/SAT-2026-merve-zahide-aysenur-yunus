<?php
/*
SAYFA: Sohbet
DOSYA: sohbet.php
KİM: Kişi 3

BU SAYFADA BUNLAR OLACAK:
- Bir talep için müşteri ile bir tamirci arasındaki mesajlar. Adres: sohbet.php?talep=1&tamirci=4
- Benim mesajım sağda mavi, karşı tarafınki solda gri
- Altta mesaj yazma kutusu. Yeni mesajı görmek için Yenile düğmesi.
KİM GİREBİLİR:
- Talebin sahibi müşteri, o talebe teklif veren (veya seçilen) tamirciyle
- O tamirci, kendi sohbetine
*/

// Sekme başlığı.
$sayfa_basligi = "Mesajlar";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Giriş yoksa giriş sayfasına gideriz.
if (!giris_yapildi_mi()) {
    // Giriş sayfası.
    yonlendir("giris.php");
}

// Giriş yapan kişinin numarası.
$benim_id = (int) $_SESSION["kullanici_id"];
// Giriş yapan kişinin rolü.
$benim_rol = $_SESSION["rol"];
// Adres çubuğundaki talep numarası.
$talep_id = gelen_sayi("talep");
// Adres çubuğundaki tamirci numarası.
$tamirci_id = gelen_sayi("tamirci");
// Tamirci ise sohbetin tamirci tarafı her zaman kendisidir. Adres değiştirilse bile.
if ($benim_rol == "tamirci") {
    // Kendi numarası.
    $tamirci_id = $benim_id;
}

// Talebi okuruz.
$talep = tek_satir("SELECT * FROM talepler WHERE id = $talep_id");

// HARİTA: Sohbet izin kontrolü
// İzin var mı? Başlangıçta yok.
$izin_var = false;
// Talep bulunduysa:
if ($talep) {
    // Bu tamircinin bu talepte reddedilmemiş teklifi var mı? Müşteri başkasını seçtiyse sohbet kapanır.
    $teklif_var = sayi_getir("SELECT COUNT(*) FROM teklifler WHERE talep_id = $talep_id AND tamirci_id = $tamirci_id AND durum <> 'red'") > 0;
    // Müşteri ise talep onun olmalı.
    $sahibi_mi = $benim_rol == "musteri" && $talep["musteri_id"] == $benim_id;
    // Tamirci ise (numarası yukarıda kendisi yapıldı).
    $tamirci_mi = $benim_rol == "tamirci";
    // Taraflardan biriyse ve tamircinin teklifi varsa izin var.
    $izin_var = ($sahibi_mi || $tamirci_mi) && $teklif_var;
}

// HARİTA: Sohbet mesaj gönderme
// Mesaj gönderildiyse ve izin varsa:
if (form_geldi_mi() && $izin_var) {
    // Yazılan mesaj.
    $mesaj = formdan_al("mesaj");
    // Mesaj boşsa:
    if ($mesaj == "") {
        // Hata.
        $hata = "Boş mesaj gönderilemez.";
    } else {
        // Mesajı kaydederiz.
        kaydet("sohbet_mesajlari", array("talep_id" => $talep_id, "tamirci_id" => $tamirci_id, "gonderen_id" => $benim_id, "mesaj" => $mesaj, "tarih" => simdi()));
        // Mesajı kayda yazarız (mesajın kendisi değil, sadece hangi talepte yazıldığı).
        log_yaz("Sohbet mesajı", "Talep numarası " . $talep_id);
        // Aynı sayfaya döneriz. Böylece F5'e basınca mesaj ikinci kez gitmez.
        yonlendir("sohbet.php?talep=$talep_id&tamirci=$tamirci_id");
    }
}

// Karşı tarafın adı. Müşteri tamircinin işletmesini, tamirci müşterinin kısa adını görür.
$karsi_taraf = "";
// Mesajlar listesi.
$mesajlar = array();
// İzin varsa bilgileri okuruz.
if ($izin_var) {
    // Müşteri ise:
    if ($benim_rol == "musteri") {
        // Tamircinin bilgisi.
        $kisi = tek_satir("SELECT ad, soyad, isletme_adi FROM kullanicilar WHERE id = $tamirci_id");
        // İşletme (ad soyad).
        $karsi_taraf = $kisi["isletme_adi"] . " (" . $kisi["ad"] . " " . $kisi["soyad"] . ")";
    } else {
        // Müşterinin bilgisi.
        $kisi = tek_satir("SELECT ad, soyad FROM kullanicilar WHERE id = " . (int) $talep["musteri_id"]);
        // Kısa ad. Örnek: Ayşe Y.
        $karsi_taraf = kisa_ad($kisi["ad"], $kisi["soyad"]);
    }
    // HARİTA: Sohbet mesajlarını listeleme
    // Mesajlar, yazanın adıyla birlikte, eskiden yeniye.
    $mesajlar = satirlar("SELECT sohbet_mesajlari.*, kullanicilar.ad, kullanicilar.soyad FROM sohbet_mesajlari INNER JOIN kullanicilar ON kullanicilar.id = sohbet_mesajlari.gonderen_id WHERE talep_id = $talep_id AND tamirci_id = $tamirci_id ORDER BY sohbet_mesajlari.id");
}

// Geri dön linki: müşteri talep detayına, tamirci paneline.
$geri_linki = "musteri/talep-detay.php?id=" . $talep_id;
// Tamirci ise:
if ($benim_rol == "tamirci") {
    // Tamirci paneli.
    $geri_linki = "tamirci/panel.php";
}

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="dashboard-section">
    <!-- Kapsayıcı. -->
    <div class="container">
        <!-- İzin varsa sohbet. -->
        <?php if ($izin_var) { ?>
            <!-- Başlık alanı. -->
            <div class="dashboard-header">
                <!-- Başlık. -->
                <h2>Sohbet Ekranı</h2>
                <!-- Kiminle ve hangi talep. -->
                <p><strong>Görüşülen:</strong> <?php echo temiz_yaz($karsi_taraf); ?> — <?php echo temiz_yaz($talep["marka"] . " " . $talep["model"] . " (" . $talep["takip_kodu"] . ")"); ?></p>
            </div>
            <!-- Hata kutusu. -->
            <?php include "ortak/mesajlar.php"; ?>
            <!-- Sohbet kartı. -->
            <div class="sohbet-karti">
                <!-- Mesaj kutusu. js/site.js en alta kaydırır. -->
                <div id="sohbet-mesajlari" class="chat-messages">
                    <!-- Her mesaj bir balon. -->
                    <?php foreach ($mesajlar as $mesaj) { ?>
                        <!-- Benim mesajımsa "benim" sınıfı: sağda mavi. -->
                        <div class="mesaj-balonu <?php if ($mesaj["gonderen_id"] == $benim_id) { echo "benim"; } ?>">
                            <!-- Yazan kişi ve zaman. date("H:i") saat:dakika. -->
                            <small><?php echo temiz_yaz($mesaj["ad"] . " " . $mesaj["soyad"]); ?> (<?php echo tarih_yaz($mesaj["tarih"]) . " " . date("H:i", strtotime($mesaj["tarih"])); ?>)</small>
                            <!-- Mesaj. nl2br satır sonlarını korur. -->
                            <?php echo nl2br(temiz_yaz($mesaj["mesaj"])); ?>
                        </div>
                    <?php } ?>
                    <!-- Hiç mesaj yoksa bilgi. -->
                    <?php if (count($mesajlar) == 0) { ?>
                        <!-- Bilgi yazısı. -->
                        <p class="form-help">Henüz mesaj yok. İlk mesajı siz yazın.</p>
                    <?php } ?>
                </div>
                <!-- Mesaj yazma formu. -->
                <form class="sohbet-formu" method="post" action="sohbet.php?talep=<?php echo $talep_id; ?>&tamirci=<?php echo $tamirci_id; ?>">
                    <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                    <?php echo guvenlik_alani(); ?>
                    <!-- Mesaj kutusu. -->
                    <input type="text" name="mesaj" maxlength="1000" placeholder="Mesajınızı yazın..." autocomplete="off" required>
                    <!-- Gönder düğmesi. -->
                    <button class="btn btn-primary" type="submit">Gönder</button>
                </form>
            </div>
            <!-- Alt düğmeler. -->
            <div class="detail-actions">
                <!-- Geri dön. -->
                <a class="btn btn-outline" href="<?php echo $geri_linki; ?>">← Geri Dön</a>
                <!-- Sayfayı yenile, yeni mesajlar görünsün. -->
                <a class="btn btn-secondary" href="sohbet.php?talep=<?php echo $talep_id; ?>&tamirci=<?php echo $tamirci_id; ?>">Yenile</a>
            </div>
        <!-- İzin yoksa uyarı. -->
        <?php } else { ?>
            <!-- Uyarı yazısı. -->
            <p class="bos-liste">Bu sohbeti açma izniniz yok. Sohbet, talep sahibi ile o talebe teklif veren tamirci arasında açılır.</p>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
