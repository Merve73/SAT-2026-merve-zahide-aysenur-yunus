<?php
/*
SAYFA: Teklif kabul kaydı
DOSYA: musteri/teklif-kabul.php
KİM: Kişi 3
BU SAYFA EKRAN BASMAZ.
- Seçilen teklif "kabul", aynı talebin diğer teklifleri "red" olur
- Talebe tamirci yazılır, durumu tamirci_atandi olur
*/

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece müşteri.
sadece_rol("musteri");

// HARİTA: Sadece form ile çalışan sayfa (adres çubuğundan açılamaz)
// Bu sayfa bir iş yapar (kaydeder, değiştirir). Sadece formla (POST) gelinirse çalışır.
// Böylece başkası "şu linke tıkla" diyerek bu işi bizim adımıza yaptıramaz (form güvenlik kodu gerekir).
if (!form_geldi_mi()) {
    // Adresle gelindiyse hiçbir şey yapmadan listeye döneriz.
    yonlendir("panel.php");
}

// HARİTA: Teklif kabul etme
// Formdan gelen teklif numarası.
$teklif_id = gelen_sayi("teklif_id");
// Müşterinin numarası.
$musteri_id = (int) $_SESSION["kullanici_id"];
// Teklif, bu müşterinin teklif bekleyen bir talebine mi ait ve tamirci hâlâ onaylı mı? Değilse bulunmaz.
$teklif = tek_satir("SELECT teklifler.talep_id, teklifler.tamirci_id FROM teklifler INNER JOIN talepler ON talepler.id = teklifler.talep_id INNER JOIN kullanicilar ON kullanicilar.id = teklifler.tamirci_id WHERE teklifler.id = $teklif_id AND talepler.musteri_id = $musteri_id AND talepler.durum = 'beklemede' AND kullanicilar.onayli = 1");

// Teklif bulunmadıysa panele döneriz.
if (!$teklif) {
    // Panel.
    yonlendir("panel.php");
}

// Talep numarası.
$talep_id = (int) $teklif["talep_id"];
// Tamirci numarası.
$tamirci_id = (int) $teklif["tamirci_id"];
// Önce bu talebin bütün tekliflerini red yaparız.
guncelle("teklifler", array("durum" => "red"), "talep_id = $talep_id");
// Sonra seçilen teklifi kabul yaparız.
guncelle("teklifler", array("durum" => "kabul"), "id = $teklif_id");
// Talebe tamirciyi yazarız. Artık diğer tamirciler bu talebi görmez.
guncelle("talepler", array("tamirci_id" => $tamirci_id, "durum" => "tamirci_atandi"), "id = $talep_id");
// Kabulü kayda yazarız.
log_yaz("Teklif kabul edildi", "Talep numarası " . $talep_id . ", teklif numarası " . $teklif_id);
// Talep detayına döneriz.
yonlendir("talep-detay.php?id=" . $talep_id);
