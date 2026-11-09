<?php
/*
SAYFA: Talep durum kaydı
DOSYA: admin/talep-durum.php
KİM: Kişi 4
BU SAYFA EKRAN BASMAZ. Gelen durum listede varsa talebi günceller.
*/

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Talep numarası.
$id = gelen_sayi("id");
// Yeni durum.
$durum = formdan_al("durum");
// Kabul edilen durumlar.
$durumlar = array("beklemede", "tamirci_atandi", "randevu_kuruldu", "tamirde", "tamamlandi", "iptal");
// Gelen durum listede varsa:
if (in_array($durum, $durumlar)) {
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
