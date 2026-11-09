<?php
/*
DOSYA: ortak/stil-onizleme.php
KİM: Kişi 4
NE YAPAR: Yönetim formunda yazı kutusunun altına önizleme çizer. Yazı iki zeminde görünür:
koyu zemin (ana sayfanın resimli üstü, sayfanın altı) ve açık zemin (açık moddaki sayfalar).
Çağırmadan önce sayfa $stil_anahtar ve $stil_ornek_yazi değişkenlerini doldurur.
js/site.js (5. bölüm) araç çubuğu veya yazı değişince önizlemeyi hemen günceller.
*/

// Sayfa önizleme yazısı vermediyse veya boşsa:
if (!isset($stil_ornek_yazi) || $stil_ornek_yazi == "") {
    // Yedek yazı.
    $stil_ornek_yazi = "Örnek yazı";
}
?>
<!-- İki önizleme yan yana. -->
<div class="stil-onizlemeler">
    <!-- Koyu zemin. -->
    <div class="stil-onizleme koyu-zemin"><span class="kucuk-etiket">Koyu zeminde</span><div class="onizleme-yazisi"<?php echo yazi_stili($stil_anahtar); ?>><?php echo temiz_yaz($stil_ornek_yazi); ?></div></div>
    <!-- Açık zemin. -->
    <div class="stil-onizleme acik-zemin"><span class="kucuk-etiket">Açık zeminde</span><div class="onizleme-yazisi"<?php echo yazi_stili($stil_anahtar); ?>><?php echo temiz_yaz($stil_ornek_yazi); ?></div></div>
</div>
