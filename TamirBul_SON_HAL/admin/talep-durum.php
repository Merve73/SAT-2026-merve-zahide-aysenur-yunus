<?php
/*
SAYFA: Talep durum kaydı
DOSYA: admin/talep-durum.php
KİM: Kişi 4
BU SAYFA EKRAN BASMAZ. Gelen durum listede varsa talebi günceller.
"Tamirci seçildi", "Randevu kuruldu", "Tamirde", "Tamamlandı" durumları için talepte tamirci olmalı.
Tamircisi olmayan talep bu durumlara geçirilmez (yoksa kim tamir ediyor belli olmaz).
*/

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// HARİTA: Sadece form ile çalışan sayfa (adres çubuğundan açılamaz)
// Bu sayfa bir iş yapar (kaydeder, değiştirir). Sadece formla (POST) gelinirse çalışır.
// Böylece başkası "şu linke tıkla" diyerek bu işi bizim adımıza yaptıramaz (form güvenlik kodu gerekir).
if (!form_geldi_mi()) {
    // Adresle gelindiyse hiçbir şey yapmadan listeye döneriz.
    yonlendir("talepler.php");
}

// Talep numarası.
$id = gelen_sayi("id");
// Yeni durum.
$durum = formdan_al("durum");
// Kabul edilen durumlar.
$durumlar = array("beklemede", "tamirci_atandi", "randevu_kuruldu", "tamirde", "tamamlandi", "iptal");
// Gelen durum listede varsa:
// Bu talepte seçilmiş tamirci var mı? IS NOT NULL: boş değil.
$tamircisi_var = sayi_getir("SELECT COUNT(*) FROM talepler WHERE id = $id AND tamirci_id IS NOT NULL") > 0;
// Tamirci isteyen durumlar.
$tamirci_isteyen = array("tamirci_atandi", "randevu_kuruldu", "tamirde", "tamamlandi");
// Tamirci isteyen bir durum seçildiyse ama talepte tamirci yoksa:
if (in_array($durum, $tamirci_isteyen) && !$tamircisi_var) {
    // Uyarıyı talepler sayfasında göstermek için oturuma yazarız.
    $_SESSION["sonraki_uyari"] = "Bu talepte seçilmiş tamirci yok. Önce müşteri bir teklifi kabul etmeli.";
    // Talepler sayfasına döneriz, durum değişmez.
    yonlendir("talepler.php");
}
// Durum listedeyse:
if (in_array($durum, $durumlar)) {
    // Durum değişikliğini kayda yazarız.
    log_yaz("Talep durumu değişti", "Talep numarası " . $id . ": " . $durum);
    // Talebi güncelleriz.
    guncelle("talepler", array("durum" => $durum), "id = $id");
    // Talep yeniden "beklemede" yapıldıysa seçilen tamirci silinir, teklifler yeniden seçilebilir olur.
    if ($durum == "beklemede") {
        // Tamirci boşaltılır (NULL).
        guncelle("talepler", array("tamirci_id" => null), "id = $id");
        // Bütün teklifler "bekliyor" olur.
        guncelle("teklifler", array("durum" => "bekliyor"), "talep_id = $id");
    }
}
// Talepler sayfasına döneriz.
yonlendir("talepler.php");
