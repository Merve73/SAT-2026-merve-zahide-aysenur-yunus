<?php
/*
DOSYA: ortak/ikonlar.php
KİM: Kişi 1
NE YAPAR: Sayfalarda kullanılan ikonları seçer. İkonlar emoji'dir (resim dosyası değil),
bu yüzden internetten bir şey indirilmez. Hareketi css/stil.css içindeki animasyonlar verir:
  ikon-zipla   yukarı aşağı zıplar
  ikon-salla   sağa sola sallanır
  ikon-don     kendi etrafında döner
  ikon-nabiz   büyüyüp küçülür (kalp atışı gibi)
  ikon-suzul   yavaşça yukarı aşağı süzülür
Kullanım: <span class="ikon ikon-zipla">📱</span>
ortak/hazirla.php bu dosyayı her sayfada yükler.
*/

// HARİTA: Kategori ikonları
// Kategori adına bakıp uygun ikonu verir. Ad içinde geçen kelimeye bakarız.
// Örnek: "Mobil & Bilişim" -> 📱, "Beyaz Eşya" -> 🧺. Hiçbiri tutmazsa 🔧.
function kategori_ikonu($kategori_adi) {
    // Kelime => ikon listesi. Yeni kategori eklenirse buraya bir satır yazılır.
    $liste = array(
        "Mobil" => "📱",
        "Bilgisayar" => "💻",
        "Beyaz" => "🧺",
        "Küçük" => "🔌",
        "TV" => "📺",
        "Ses" => "🔊",
        "Oyun" => "🎮",
        "Elektronik" => "🔋"
    );
    // Listedeki her kelime için:
    foreach ($liste as $kelime => $ikon) {
        // strpos: kelime adın içinde geçiyor mu? Geçmiyorsa false verir.
        if (strpos($kategori_adi, $kelime) !== false) {
            // İlk tutan ikonu veririz.
            return $ikon;
        }
    }
    // Hiçbiri tutmadı: anahtar ikonu.
    return "🔧";
}

// HARİTA: Adım ikonları (Nasıl çalışır)
// Ana sayfadaki "Nasıl çalışır" kartının sırasına göre ikon verir. $sira 0'dan başlar.
function adim_ikonu($sira) {
    // Sırayla: talep yaz, teklif gelsin, tamirciyi seç, puan ver.
    $ikonlar = array("📝", "💬", "🤝", "⭐", "✅", "🎉");
    // Sıra listede varsa:
    if (isset($ikonlar[$sira])) {
        // O sıradaki ikon.
        return $ikonlar[$sira];
    }
    // Liste bittiyse onay işareti.
    return "✅";
}

// Talep durumuna göre ikon verir. Örnek: "tamamlandi" -> ✅
function durum_ikonu($durum) {
    // Durum => ikon tablosu.
    $ikonlar = array(
        "beklemede" => "⏳",
        "tamirci_atandi" => "🤝",
        "randevu_kuruldu" => "📅",
        "tamirde" => "🔧",
        "tamamlandi" => "✅",
        "iptal" => "❌"
    );
    // Tabloda varsa:
    if (isset($ikonlar[$durum])) {
        // O durumun ikonu.
        return $ikonlar[$durum];
    }
    // Yoksa soru işareti.
    return "❔";
}
