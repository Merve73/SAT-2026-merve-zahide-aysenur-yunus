<?php
/*
DOSYA: ortak/ikonlar.php
KİM: Kişi 1
NE YAPAR: Sitedeki bütün ikonları verir. İkonlar SVG çizimidir (resim dosyası değil),
bu yüzden internetten bir şey indirilmez ve her ekranda net görünür.
İkonun rengi yazının rengini alır (stroke="currentColor"). Boyutu css/stil.css içindeki .svg-ikon kuralı verir.
Kullanım: echo ikon("telefon");
Yeni ikon eklemek için $cizimler dizisine bir satır yazılır.
ortak/hazirla.php bu dosyayı her sayfada yükler.
*/

// HARİTA: SVG ikonlar (ikon fonksiyonu)
// İstenen ikonun SVG kodunu verir. Örnek: ikon("takvim")
function ikon($ad) {
    // İkon adı => çizim. Çizimler 24x24 karelik bir alana yapılır. path: çizgi, rect: dikdörtgen, circle: daire.
    $cizimler = array(
        "telefon" => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>',
        "bilgisayar" => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M2 20h20"/>',
        "camasir" => '<rect x="3" y="2" width="18" height="20" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M7 6h.01M10 6h.01"/>',
        "fis" => '<path d="M12 22v-5M9 8V2M15 8V2"/><path d="M18 8v5a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V8Z"/>',
        "televizyon" => '<rect x="2" y="7" width="20" height="15" rx="2"/><path d="m17 2-5 5-5-5"/>',
        "hoparlor" => '<rect x="4" y="2" width="16" height="20" rx="2"/><circle cx="12" cy="14" r="4"/><path d="M12 6h.01"/>',
        "oyun" => '<rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 12h4M8 10v4M15 13h.01M18 11h.01"/>',
        "devre" => '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/>',
        "anahtar" => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        "kalem" => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        "mesaj" => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        "secim" => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/>',
        "yildiz" => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
        "onay" => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
        "saat" => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        "takvim" => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        "iptal" => '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/>',
        "soru" => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/>',
        "bilgi" => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>',
        "ev" => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        "pusula" => '<circle cx="12" cy="12" r="10"/><path d="m16.24 7.76-2.12 6.36-6.36 2.12 2.12-6.36 6.36-2.12z"/>',
        "liste" => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 11h8M8 16h5"/>',
        "kisi" => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        "kisiler" => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        "kisi-ekle" => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/>',
        "giris" => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5M15 12H3"/>',
        "cikis" => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>',
        "arti" => '<circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>',
        "ara" => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        "klasor" => '<path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/>',
        "grafik" => '<path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/>',
        "ayar" => '<path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/>',
        "canta" => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        "gelen" => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
        "zarf" => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/>',
        "belge" => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>',
        "duzen" => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
        "resim" => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.09-3.09a2 2 0 0 0-2.82 0L6 21"/>',
        "sira" => '<path d="M10 6h11M10 12h11M10 18h11M4 6h1v4M4 10h2M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/>',
        "katman" => '<path d="m12 2 10 5-10 5L2 7z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
        "konum" => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        "kilit" => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        "ampul" => '<path d="M9 18h6M10 22h4"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/>',
        "ahize" => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
        "silgi" => '<path d="m7 21-4.3-4.3c-1-1-1-2.5 0-3.4l9.6-9.6c1-1 2.5-1 3.4 0l5.6 5.6c1 1 1 2.5 0 3.4L13 21"/><path d="M22 21H7M5 11l9 9"/>',
        "uyari" => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/>',
        "kalkan" => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
        "gunes" => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
        "ay" => '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>',
        "asagi" => '<path d="m6 9 6 6 6-6"/>',
        "ok" => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        "menu" => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        "kapat" => '<path d="M18 6 6 18M6 6l12 12"/>',
        "simsek" => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>',
        "geri" => '<path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-15-6.7L3 13"/>'
    );
    // İstenen ikon listede yoksa anahtar ikonunu kullanırız. Sayfa boş kalmaz.
    if (!isset($cizimler[$ad])) {
        // Varsayılan ikon.
        $ad = "anahtar";
    }
    // SVG etiketi. aria-hidden: ekran okuyucu ikonu okumaz, yanındaki yazıyı okur.
    return '<svg class="svg-ikon" viewBox="0 0 24 24" aria-hidden="true">' . $cizimler[$ad] . '</svg>';
}

// HARİTA: Kategori ikonları
// Kategori adına bakıp uygun ikonu verir. Ad içinde geçen kelimeye bakarız.
// Örnek: "Mobil & Bilişim" -> telefon, "Beyaz Eşya" -> çamaşır makinesi. Hiçbiri tutmazsa anahtar.
function kategori_ikonu($kategori_adi) {
    // Kelime => ikon adı listesi. Yeni kategori eklenirse buraya bir satır yazılır.
    $liste = array(
        "Mobil" => "telefon",
        "Bilgisayar" => "bilgisayar",
        "Beyaz" => "camasir",
        "Küçük" => "fis",
        "TV" => "televizyon",
        "Ses" => "hoparlor",
        "Oyun" => "oyun",
        "Elektronik" => "devre"
    );
    // Listedeki her kelime için:
    foreach ($liste as $kelime => $ikon_adi) {
        // strpos: kelime adın içinde geçiyor mu? Geçmiyorsa false verir.
        if (strpos($kategori_adi, $kelime) !== false) {
            // İlk tutan ikonu veririz.
            return ikon($ikon_adi);
        }
    }
    // Hiçbiri tutmadı: anahtar ikonu.
    return ikon("anahtar");
}

// HARİTA: Adım ikonları (Nasıl çalışır)
// Ana sayfadaki "Nasıl çalışır" kartının sırasına göre ikon verir. $sira 0'dan başlar.
function adim_ikonu($sira) {
    // Sırayla: talep yaz, teklif gelsin, tamirciyi seç, puan ver.
    $ikonlar = array("kalem", "mesaj", "secim", "yildiz", "onay", "kalkan");
    // Sıra listede varsa:
    if (isset($ikonlar[$sira])) {
        // O sıradaki ikon.
        return ikon($ikonlar[$sira]);
    }
    // Liste bittiyse onay işareti.
    return ikon("onay");
}

// Talep durumuna göre ikon verir. Örnek: "tamamlandi" -> onay işareti
function durum_ikonu($durum) {
    // Durum => ikon adı tablosu.
    $ikonlar = array(
        "beklemede" => "saat",
        "tamirci_atandi" => "secim",
        "randevu_kuruldu" => "takvim",
        "tamirde" => "anahtar",
        "tamamlandi" => "onay",
        "iptal" => "iptal"
    );
    // Tabloda varsa:
    if (isset($ikonlar[$durum])) {
        // O durumun ikonu.
        return ikon($ikonlar[$durum]);
    }
    // Yoksa soru işareti.
    return ikon("soru");
}
