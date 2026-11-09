<?php
/*
DOSYA: ortak/konum-secimi.php
KİM: Kişi 3
NE YAPAR: İl ve ilçe seçim kutularını çizer. Kayıt, profil ve talep formları kullanır.
Çağırmadan önce sayfa $secili_il ve $secili_ilce değişkenlerini doldurur.
İlçe kutusuna bütün ilçeler yazılır. js/site.js seçilen ile ait olmayanları gizler.
*/

// HARİTA: İl-ilçe seçim kutuları (form parçası)
// Sayfa vermediyse boş kabul ederiz.
if (!isset($secili_il)) {
    // Boş il.
    $secili_il = "";
}
// İlçe de boş.
if (!isset($secili_ilce)) {
    // Boş ilçe.
    $secili_ilce = "";
}

// İlleri alfabe sırasıyla okuruz.
$konum_iller = satirlar("SELECT * FROM iller ORDER BY ad");
// İlçeleri, bağlı oldukları ilin adıyla birlikte okuruz.
$konum_ilceler = satirlar("SELECT ilceler.ad, iller.ad AS il_adi FROM ilceler INNER JOIN iller ON iller.id = ilceler.il_id ORDER BY ilceler.ad");
?>
<!-- İki kutu yan yana. -->
<div class="form-row">
    <!-- İl kutusu. -->
    <div class="form-group">
        <!-- Kutunun adı. -->
        <label for="cmbIl">İl</label>
        <!-- data-alt: js bu kutu değişince hangi kutuyu dolduracağını buradan okur. -->
        <select id="cmbIl" name="sehir" data-alt="cmbIlce">
            <!-- İlk seçenek boş. -->
            <option value="">İl Seçiniz</option>
            <!-- Her il bir seçenek. Kayıtlı il seçili gelir. -->
            <?php foreach ($konum_iller as $il) { ?>
                <!-- Seçeneğin değeri ve yazısı ilin adı. -->
                <option value="<?php echo temiz_yaz($il["ad"]); ?>" <?php if ($secili_il == $il["ad"]) { echo "selected"; } ?>><?php echo temiz_yaz($il["ad"]); ?></option>
            <?php } ?>
        </select>
    </div>
    <!-- İlçe kutusu. -->
    <div class="form-group">
        <!-- Kutunun adı. -->
        <label for="cmbIlce">İlçe</label>
        <!-- İlçe seçim kutusu. -->
        <select id="cmbIlce" name="ilce">
            <!-- İlk seçenek boş. -->
            <option value="">İlçe Seçiniz</option>
            <!-- Her ilçe bir seçenek. data-üst hangi ile ait olduğunu söyler. -->
            <?php foreach ($konum_ilceler as $ilce) { ?>
                <!-- İlçe adı ve ili ikisi de tutarsa seçili gelir (Merkez gibi adlar birden çok ilde var). -->
                <option value="<?php echo temiz_yaz($ilce["ad"]); ?>" data-ust="<?php echo temiz_yaz($ilce["il_adi"]); ?>" <?php if ($secili_ilce == $ilce["ad"] && $secili_il == $ilce["il_adi"]) { echo "selected"; } ?>><?php echo temiz_yaz($ilce["ad"]); ?></option>
            <?php } ?>
        </select>
    </div>
</div>
