<?php
/*
DOSYA: musteri/teklif-kartlari.php
KİM: Kişi 2
NE YAPAR: Bir talebe gelen teklifleri kart kart yazar. panel.php ve talep-detay.php kullanır.
Çağırmadan önce sayfa $talep değişkenini (talepler tablosundan bir satır) doldurur.
Talep "beklemede" ise her teklifin yanında "Teklifi Kabul Et" düğmesi çıkar.
*/

// HARİTA: Gelen teklifleri listeleme (müşteri)
// Bu talebin teklifleri, ucuzdan pahalıya. Tamircinin işletme adı da gelir.
$teklifler = satirlar("SELECT teklifler.*, kullanicilar.ad, kullanicilar.soyad, kullanicilar.isletme_adi FROM teklifler INNER JOIN kullanicilar ON kullanicilar.id = teklifler.tamirci_id WHERE teklifler.talep_id = " . (int) $talep["id"] . " ORDER BY teklifler.fiyat");
?>
<!-- Teklifler alanı. -->
<div class="offers-subpanel">
    <!-- Başlık ve teklif sayısı. -->
    <h5>Gelen Tamirci Teklifleri (<?php echo count($teklifler); ?> Teklif)</h5>
    <!-- Hiç teklif yoksa bilgi. -->
    <?php if (count($teklifler) == 0) { ?>
        <!-- Bilgi. -->
        <p class="form-help">Henüz teklif gelmedi. Bölgenizdeki tamirciler talebinizi görünce teklif verecek.</p>
    <?php } ?>
    <!-- Her teklif bir kart. -->
    <?php foreach ($teklifler as $teklif) { ?>
        <!-- Kabul edilen teklif "kabul" sınıfıyla yeşil çerçeveli. -->
        <div class="offer-card <?php if ($teklif["durum"] == "kabul") { echo "kabul"; } ?>">
            <!-- Sol: tamirci bilgisi. -->
            <div class="offer-mechanic-info">
                <!-- İşletme ve yetkili. -->
                <strong><?php echo temiz_yaz($teklif["isletme_adi"] . " (" . $teklif["ad"] . " " . $teklif["soyad"] . ")"); ?></strong>
                <!-- Puan. -->
                <span class="rating"><?php echo tamirci_puan_metni($teklif["tamirci_id"]); ?></span>
                <!-- Tamircinin notu. -->
                <small><?php echo temiz_yaz($teklif["aciklama"]); ?></small>
            </div>
            <!-- Sağ: fiyat ve düğmeler. -->
            <div class="offer-details">
                <!-- Fiyat. -->
                <span class="offer-price"><?php echo fiyat_yaz($teklif["fiyat"]); ?></span>
                <!-- Süre. -->
                <span class="offer-duration"><?php echo temiz_yaz($teklif["sure"]); ?></span>
                <!-- Talep beklemedeyse kabul düğmesi. -->
                <?php if ($talep["durum"] == "beklemede") { ?>
                    <!-- Kabul formu. confirm: emin mısınız kutusu. -->
                    <form method="post" action="teklif-kabul.php" onsubmit="return confirm('Bu teklifi kabul ediyor musunuz?');">
                        <!-- Teklifin numarası gizli kutuda gider. -->
                        <input type="hidden" name="teklif_id" value="<?php echo (int) $teklif["id"]; ?>">
                        <!-- Kabul düğmesi. -->
                        <button class="btn btn-sm btn-success" type="submit">Teklifi Kabul Et</button>
                    </form>
                <?php } ?>
                <!-- Kabul edilen teklifte etiket. -->
                <?php if ($teklif["durum"] == "kabul") { ?>
                    <!-- Etiket. -->
                    <span class="badge badge-success">Seçtiğiniz teklif</span>
                <?php } ?>
                <!-- Reddedilen teklifte sohbet kapalı, düğme çıkmaz. -->
                <?php if ($teklif["durum"] != "red") { ?>
                    <!-- Tamirciyle sohbet. -->
                    <a class="btn btn-sm btn-outline" href="../sohbet.php?talep=<?php echo (int) $talep["id"]; ?>&tamirci=<?php echo (int) $teklif["tamirci_id"]; ?>">Mesaj Gönder</a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
