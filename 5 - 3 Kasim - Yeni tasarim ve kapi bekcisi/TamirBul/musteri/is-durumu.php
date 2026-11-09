<?php
/*
DOSYA: musteri/is-durumu.php
KİM: Kişi 2
NE YAPAR: Tamirci seçildikten sonraki durumu yazar: seçilen tamirci, tutar, telefon, randevu, puan formu.
panel.php ve talep-detay.php kullanır. Çağırmadan önce sayfa $talep değişkenini doldurur.
*/

// Talebin numarası.
$durum_talep_id = (int) $talep["id"];
// Kabul edilen teklif ve tamircinin bilgileri.
$secilen = tek_satir("SELECT teklifler.fiyat, teklifler.sure, teklifler.tamirci_id, kullanicilar.ad, kullanicilar.soyad, kullanicilar.isletme_adi, kullanicilar.telefon, kullanicilar.adres FROM teklifler INNER JOIN kullanicilar ON kullanicilar.id = teklifler.tamirci_id WHERE teklifler.talep_id = $durum_talep_id AND teklifler.durum = 'kabul'");
// En son kurulan randevu. ORDER BY id DESC: en yeni önce.
$randevu = tek_satir("SELECT * FROM randevular WHERE talep_id = $durum_talep_id ORDER BY id DESC LIMIT 1");
?>
<!-- Seçilen teklif varsa kutu. -->
<?php if ($secilen) { ?>
    <!-- Yeşil bilgi kutusu. -->
    <div class="completed-note">
        <!-- Tamirci, tutar, süre. -->
        <p><strong>Seçilen Tamirci:</strong> <?php echo temiz_yaz($secilen["isletme_adi"] . " (" . $secilen["ad"] . " " . $secilen["soyad"] . ")"); ?> — <strong>Tutar:</strong> <?php echo fiyat_yaz($secilen["fiyat"]); ?> — <strong>Süre:</strong> <?php echo temiz_yaz($secilen["sure"]); ?></p>
        <!-- Telefon ve adres sadece teklif kabul edildikten sonra görünür. -->
        <p><strong>Telefon:</strong> <?php echo temiz_yaz($secilen["telefon"]); ?> — <strong>Adres:</strong> <?php echo temiz_yaz($secilen["adres"]); ?></p>
        <!-- Randevu varsa tarihi. -->
        <?php if ($randevu) { ?>
            <!-- Randevu bilgisi. -->
            <p><strong>Randevu:</strong> <?php echo tarih_yaz($randevu["randevu_tarihi"]) . " " . temiz_yaz($randevu["randevu_saati"]); ?> (<?php echo randevu_durum_adi($randevu["durum"]); ?>) <?php echo temiz_yaz($randevu["not_metni"]); ?></p>
        <!-- Randevu yoksa bilgi. -->
        <?php } else { ?>
            <!-- Bilgi. -->
            <p>Tamirci size randevu günü ve saati önerecek.</p>
        <?php } ?>
        <!-- Sohbet düğmesi. -->
        <p><a class="btn btn-sm btn-outline" href="../sohbet.php?talep=<?php echo $durum_talep_id; ?>&tamirci=<?php echo (int) $secilen["tamirci_id"]; ?>">Mesajlar</a></p>

        <!-- HARİTA: Puan verme formu (müşteri) -->
        <!-- İş bitti ve daha puan verilmediyse puan formu. -->
        <?php if ($talep["durum"] == "tamamlandi" && $talep["puan"] == 0) { ?>
            <!-- Puan formu. -->
            <form class="puan-formu" method="post" action="puan-ver.php">
                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                <?php echo guvenlik_alani(); ?>
                <!-- Talep numarası gizli gider. -->
                <input type="hidden" name="talep_id" value="<?php echo $durum_talep_id; ?>">
                <!-- Yazı. -->
                <span>Tamirciyi puanlayın:</span>
                <!-- 1-5 arası seçim. -->
                <select name="puan">
                    <!-- 5 puan. -->
                    <option value="5">5 - Çok iyi</option>
                    <!-- 4 puan. -->
                    <option value="4">4 - İyi</option>
                    <!-- 3 puan. -->
                    <option value="3">3 - Orta</option>
                    <!-- 2 puan. -->
                    <option value="2">2 - Kötü</option>
                    <!-- 1 puan. -->
                    <option value="1">1 - Çok kötü</option>
                </select>
                <!-- Gönder düğmesi. -->
                <button class="btn btn-sm btn-primary" type="submit">Puan Ver</button>
            </form>
        <?php } ?>
        <!-- Puan verildiyse gösterilir. -->
        <?php if ($talep["puan"] > 0) { ?>
            <!-- Verilen puan. -->
            <p><strong>Verdiğiniz puan:</strong> <?php echo (int) $talep["puan"]; ?> / 5</p>
        <?php } ?>
    </div>
<?php } ?>
