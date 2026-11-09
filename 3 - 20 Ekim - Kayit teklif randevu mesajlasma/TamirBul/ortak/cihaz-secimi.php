<?php
/*
DOSYA: ortak/cihaz-secimi.php
KİM: Kişi 3
NE YAPAR: Ana kategori ve cihaz türü seçim kutularını çizer. Talep oluştur ve talep düzenle kullanır.
Çağırmadan önce sayfa $secili_kategori ve $secili_cihaz değişkenlerini doldurur.
Cihaz kutusuna bütün türler yazılır. js/site.js seçilen kategoriye ait olmayanları gizler.
*/

// HARİTA: Kategori-cihaz seçim kutuları (form parçası)
// Sayfa vermediyse 0 kabul ederiz.
if (!isset($secili_kategori)) {
    // Kategori seçilmedi.
    $secili_kategori = 0;
}
// Cihaz türü de boş.
if (!isset($secili_cihaz)) {
    // Cihaz seçilmedi.
    $secili_cihaz = "";
}

// Kategorileri okuruz.
$secim_kategoriler = satirlar("SELECT * FROM kategoriler ORDER BY id");
// Cihaz türlerini okuruz.
$secim_turler = satirlar("SELECT * FROM cihaz_turleri ORDER BY kategori_id, id");
?>
<!-- İki kutu yan yana. -->
<div class="form-row">
    <!-- Kategori kutusu. -->
    <div class="form-group">
        <!-- Kutunun adı. -->
        <label for="cmbAnaKategori">Ana Kategori</label>
        <!-- data-alt: js bu kutu değişince cihaz kutusunu doldurur. -->
        <select id="cmbAnaKategori" name="kategori_id" data-alt="cmbCihazTuru">
            <!-- İlk seçenek boş. -->
            <option value="">Kategori Seçiniz</option>
            <!-- Her kategori bir seçenek. -->
            <?php foreach ($secim_kategoriler as $kategori) { ?>
                <!-- Değeri kategori numarası, yazısı adı. -->
                <option value="<?php echo (int) $kategori["id"]; ?>" <?php if ((int) $secili_kategori == (int) $kategori["id"]) { echo "selected"; } ?>><?php echo temiz_yaz($kategori["ad"]); ?></option>
            <?php } ?>
        </select>
    </div>
    <!-- Cihaz türü kutusu. -->
    <div class="form-group">
        <!-- Kutunun adı. -->
        <label for="cmbCihazTuru">Cihaz Türü</label>
        <!-- Cihaz seçim kutusu. -->
        <select id="cmbCihazTuru" name="cihaz_turu">
            <!-- İlk seçenek boş. -->
            <option value="">Cihaz Türü Seçiniz</option>
            <!-- Her tür bir seçenek. data-üst hangi kategoriye ait olduğunu söyler. -->
            <?php foreach ($secim_turler as $tur) { ?>
                <!-- Değeri ve yazısı türün adı. -->
                <option value="<?php echo temiz_yaz($tur["ad"]); ?>" data-ust="<?php echo (int) $tur["kategori_id"]; ?>" <?php if ($secili_cihaz == $tur["ad"]) { echo "selected"; } ?>><?php echo temiz_yaz($tur["ad"]); ?></option>
            <?php } ?>
        </select>
    </div>
</div>
