<?php
/*
DOSYA: ortak/stil-araclari.php
KİM: Kişi 4
NE YAPAR: Yönetim formlarında bir yazının üstüne Word'deki gibi bir araç çubuğu çizer:
  [Yazı tipi] [Boyut] | K (kalın) T (eğik) | renk kutuları | sola, ortaya, sağa | Temizle
Türkçe Word'de kalın düğmesi K, eğik (italik) düğmesi T harfidir. Burada da öyle.
Çağırmadan önce sayfa $stil_anahtar değişkenini doldurur. Örnek: $stil_anahtar = "anasayfa_baslik";
Araç çubuğu, yazı kutusu ve önizleme aynı kutunun (class="stil-alani") içinde durur.
js/site.js (5. bölüm) bir düğmeye basılınca önizlemeyi hemen değiştirir.
Kutuların adı stil[anahtar][renk] gibidir. Sayfa yazi_stili_kaydet ile kaydeder.
Seçeneklerin listesi ortak/yazi-stili.php içindedir.
*/

// HARİTA: Word benzeri yazı stili araç çubuğu (form parçası)
// Bu yazının kayıtlı seçimleri. Örnek: array("renk" => "kirmizi", "boyut" => "", ...)
$stil_secimleri = yazi_stili_oku($stil_anahtar);
// Kutu adlarının başı. Örnek: stil[anasayfa_baslik]
$stil_ad = "stil[" . temiz_yaz($stil_anahtar) . "]";
?>
<!-- Araç çubuğu. role="toolbar": ekran okuyucuya "araç çubuğu" der. -->
<div class="kelime-araclari" role="toolbar" aria-label="Yazı stili">
    <!-- 1) Yazı tipi seçimi. -->
    <select class="arac-secim" name="<?php echo $stil_ad; ?>[yazi_tipi]" title="Yazı tipi">
        <!-- Boş seçenek: sitenin kendi yazı tipi. -->
        <option value="">Yazı tipi</option>
        <!-- Her yazı tipi bir seçenek. style: seçeneğin kendisi o yazı tipiyle görünür. data-css: önizleme için. -->
        <?php foreach ($yazi_stil_secenekleri["yazi_tipi"]["secenekler"] as $kod => $secenek) { ?>
            <!-- Seçenek. Kayıtlı seçimse selected. -->
            <option value="<?php echo $kod; ?>" data-css="<?php echo temiz_yaz($secenek[1]); ?>" style="<?php echo temiz_yaz($secenek[1]); ?>" <?php if ($stil_secimleri["yazi_tipi"] == $kod) { echo "selected"; } ?>><?php echo temiz_yaz($secenek[0]); ?></option>
        <?php } ?>
    </select>

    <!-- 2) Boyut seçimi. -->
    <select class="arac-secim arac-dar" name="<?php echo $stil_ad; ?>[boyut]" title="Yazı boyutu">
        <!-- Boş seçenek: sitenin kendi boyutu. -->
        <option value="">Boyut</option>
        <!-- Her boyut bir seçenek. -->
        <?php foreach ($yazi_stil_secenekleri["boyut"]["secenekler"] as $kod => $secenek) { ?>
            <!-- Seçenek. Örnek yazı: "28 - Büyük". str_replace CSS'ten sadece sayıyı alır. -->
            <option value="<?php echo $kod; ?>" data-css="<?php echo temiz_yaz($secenek[1]); ?>" <?php if ($stil_secimleri["boyut"] == $kod) { echo "selected"; } ?>><?php echo str_replace(array("font-size: ", "px;"), "", $secenek[1]) . " - " . temiz_yaz($secenek[0]); ?></option>
        <?php } ?>
    </select>

    <!-- Ayraç çizgisi. -->
    <span class="arac-ayrac"></span>

    <!-- 3) Kalın düğmesi (K). Onay kutusu: basılıysa "kalin" gider, değilse hiçbir şey gitmez (varsayılan). -->
    <label class="arac-dugmesi" title="Kalın">
        <!-- Görünmeyen onay kutusu. -->
        <input type="checkbox" name="<?php echo $stil_ad; ?>[kalinlik]" value="kalin" data-css="font-weight: 800;" <?php if ($stil_secimleri["kalinlik"] == "kalin") { echo "checked"; } ?>>
        <!-- Görünen yüz. -->
        <span class="arac-yuzu"><b>K</b></span>
    </label>
    <!-- 4) Eğik düğmesi (T). -->
    <label class="arac-dugmesi" title="Eğik (italik)">
        <!-- Görünmeyen onay kutusu. -->
        <input type="checkbox" name="<?php echo $stil_ad; ?>[egik]" value="egik" data-css="font-style: italic;" <?php if ($stil_secimleri["egik"] == "egik") { echo "checked"; } ?>>
        <!-- Görünen yüz. -->
        <span class="arac-yuzu"><i>T</i></span>
    </label>

    <!-- Ayraç çizgisi. -->
    <span class="arac-ayrac"></span>

    <!-- 5) Renk kutuları. Seçim yuvarlakları (radio): aynı anda bir renk seçilir. -->
    <span class="renk-paleti" title="Yazı rengi">
        <!-- Otomatik: sitenin kendi rengi. Boş değer gider. -->
        <label class="renk-kutusu" title="Otomatik (sitenin rengi)">
            <!-- Görünmeyen seçim yuvarlağı. Kayıtlı renk yoksa seçili. -->
            <input type="radio" name="<?php echo $stil_ad; ?>[renk]" value="" <?php if ($stil_secimleri["renk"] == "") { echo "checked"; } ?>>
            <!-- Görünen kutu: üzerinde A harfi. -->
            <span class="arac-yuzu renk-otomatik">A</span>
        </label>
        <!-- Her renk bir kutu. -->
        <?php foreach ($yazi_stil_secenekleri["renk"]["secenekler"] as $kod => $secenek) { ?>
            <?php
            // CSS'ten renk kodunu alırız. Örnek: "color: #dc2626;" -> "#dc2626"
            $renk_kodu = str_replace(array("color: ", ";"), "", $secenek[1]);
            ?>
            <!-- Kutu. title: üstüne gelince rengin adı görünür. -->
            <label class="renk-kutusu" title="<?php echo temiz_yaz($secenek[0]); ?>">
                <!-- Görünmeyen seçim yuvarlağı. -->
                <input type="radio" name="<?php echo $stil_ad; ?>[renk]" value="<?php echo $kod; ?>" data-css="<?php echo temiz_yaz($secenek[1]); ?>" <?php if ($stil_secimleri["renk"] == $kod) { echo "checked"; } ?>>
                <!-- Görünen kutu: o renge boyalı. -->
                <span class="arac-yuzu" style="background: <?php echo temiz_yaz($renk_kodu); ?>;"></span>
            </label>
        <?php } ?>
    </span>

    <!-- Ayraç çizgisi. -->
    <span class="arac-ayrac"></span>

    <!-- 6) Hizalama düğmeleri. -->
    <span class="hiza-dugmeleri">
        <!-- Görünmeyen varsayılan seçenek. Temizle düğmesi bunu seçer. -->
        <input class="gizli" type="radio" name="<?php echo $stil_ad; ?>[hizalama]" value="" <?php if ($stil_secimleri["hizalama"] == "") { echo "checked"; } ?>>
        <!-- Her hizalama bir düğme: sol, orta, sağ. -->
        <?php foreach ($yazi_stil_secenekleri["hizalama"]["secenekler"] as $kod => $secenek) { ?>
            <!-- Düğme. -->
            <label class="arac-dugmesi" title="<?php echo temiz_yaz($secenek[0]); ?> hizala">
                <!-- Görünmeyen seçim yuvarlağı. -->
                <input type="radio" name="<?php echo $stil_ad; ?>[hizalama]" value="<?php echo $kod; ?>" data-css="<?php echo temiz_yaz($secenek[1]); ?>" <?php if ($stil_secimleri["hizalama"] == $kod) { echo "checked"; } ?>>
                <!-- Görünen yüz: üç çizgi. CSS çizgileri sola, ortaya veya sağa dizer. -->
                <span class="arac-yuzu hiza-ikonu hiza-<?php echo $kod; ?>"><i></i><i></i><i></i></span>
            </label>
        <?php } ?>
    </span>

    <!-- Ayraç çizgisi. -->
    <span class="arac-ayrac"></span>

    <!-- 7) Biçimi temizle: hepsini varsayılana döndürür. js/site.js çalıştırır. -->
    <button type="button" class="arac-temizle" title="Biçimi temizle"><?php echo ikon("silgi"); ?> Temizle</button>
</div>
