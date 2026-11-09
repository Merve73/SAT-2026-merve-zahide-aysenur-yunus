<?php
/*
DOSYA: ortak/resim-alani.php
KİM: Kişi 4
NE YAPAR: Yönetim formlarındaki resim alanını çizer (kategori, hizmet, adım, slayt).
Çağırmadan önce sayfa $resim_mevcut değişkenini (veritabanındaki değer) doldurur.
DİKKAT: Formda enctype="multipart/form-data" olmalı, yoksa dosya gitmez.
Kaydederken ortak/guvenlik.php içindeki resim_formdan_al bu iki kutuyu okur.
*/

// Sayfa vermediyse resim yok.
if (!isset($resim_mevcut)) {
    // Boş resim.
    $resim_mevcut = "";
}
?>
<!-- Resim alanı. -->
<div class="form-group resim-alani">
    <!-- Alan başlığı. -->
    <label>Resim</label>
    <!-- Şu anki resim varsa küçük önizleme. -->
    <?php if ($resim_mevcut != "") { ?>
        <!-- Önizleme resmi. -->
        <img class="resim-onizleme" src="<?php echo temiz_yaz(resim_adresi($resim_mevcut)); ?>" alt="Şu anki resim">
    <?php } ?>
    <!-- 1. yol: internet adresi yazmak. -->
    <label for="resim_adresi" class="kucuk-etiket">İnternet adresi (veya yüklenmiş dosya adı). Silerseniz resim kaldırılır.</label>
    <!-- Adres kutusu, şu anki değerle dolu gelir. -->
    <input id="resim_adresi" name="resim_adresi" type="text" value="<?php echo temiz_yaz($resim_mevcut); ?>" placeholder="https://...">
    <!-- 2. yol: bilgisayardan dosya seçmek. -->
    <label for="resim_dosya" class="kucuk-etiket">Ya da bilgisayardan yükleyin (jpg, png, webp, gif - en fazla 3 MB)</label>
    <!-- Dosya seçme kutusu. -->
    <input id="resim_dosya" name="resim_dosya" type="file" accept="image/*">
</div>
