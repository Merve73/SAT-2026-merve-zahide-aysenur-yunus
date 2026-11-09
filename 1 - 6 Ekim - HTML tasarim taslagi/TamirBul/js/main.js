document.addEventListener("DOMContentLoaded", () => {
    
    // 0. Proje Testi İçin Sahte (Mock) Hesaplar ve Giriş Mantığı
    if (!localStorage.getItem("tamirbul_sahte_hesaplar")) {
        const testHesaplari = [
            {
                id: 1,
                adSoyad: "Ayşe Müşteri",
                eposta: "musteri@ornek.com",
                sifre: "12345",
                rol: "musteri",
                telefon: "05551112233"
            },
            {
                id: 2,
                adSoyad: "Ali Usta (Tamirci)",
                eposta: "tamirci@ornek.com",
                sifre: "12345",
                rol: "tamirci",
                uzmanlik: "Mobil & Bilişim"
            }
        ];
        
        localStorage.setItem("tamirbul_sahte_hesaplar", JSON.stringify(testHesaplari));
        console.log("Test için sahte hesaplar hafızaya kaydedildi!");
    }

    // Giriş Formu Kontrolü
    const girisFormu = document.getElementById("formGiris");
    if (girisFormu) {
        girisFormu.addEventListener("submit", function (e) {
            e.preventDefault();
            
            const epostaInput = document.getElementById("txtEposta").value.trim();
            const sifreInput = document.getElementById("txtSifre").value.trim();
            
            const kayitliHesaplar = JSON.parse(localStorage.getItem("tamirbul_sahte_hesaplar")) || [];
            
            // Kullanıcıyı eşleştir
            const bulunanKullanici = kayitliHesaplar.find(hesap => hesap.eposta === epostaInput && hesap.sifre === sifreInput);
            
            if (bulunanKullanici) {
                alert(`Giriş başarılı! Hoş geldiniz, ${bulunanKullanici.adSoyad}`);
                
                // Role göre ilgili panele yönlendir
                if (bulunanKullanici.rol === "musteri") {
                    window.location.href = "musteri-paneli.html";
                } else if (bulunanKullanici.rol === "tamirci") {
                    window.location.href = "tamirci-paneli.html";
                }
            } else {
                alert("Hatalı e-posta veya şifre! Lütfen test hesaplarını kontrol edin (örn: musteri@ornek.com / 12345).");
            }
        });
    }

    // 1. Ana Kategoriye Göre Cihaz Türlerini Doldurma (Bağımlı Seçim Listesi)
    const anaKategoriSelect = document.getElementById("cmbAnaKategori");
    const cihazTuruSelect = document.getElementById("cmbCihazTuru");

    // Kategorilere göre cihaz türü verileri
    const cihazTurleriVeri = {
        "1": [
            { id: "101", ad: "Akıllı Telefon (iOS / Android)" },
            { id: "102", ad: "Laptop / Dizüstü Bilgisayar" },
            { id: "103", ad: "Tablet" },
            { id: "104", ad: "Masaüstü Bilgisayar" }
        ],
        "2": [
            { id: "201", ad: "Çamaşır Makinesi" },
            { id: "202", ad: "Bulaşık Makinesi" },
            { id: "203", ad: "Buzdolabı" },
            { id: "204", ad: "Fırın / Ankastre Set" }
        ],
        "3": [
            { id: "301", ad: "Robot / Dikey Süpürge" },
            { id: "302", ad: "Kahve Makinesi" },
            { id: "303", ad: "Ütü" },
            { id: "304", ad: "Mutfak Robotu / Mikser" }
        ],
        "4": [
            { id: "401", ad: "Televizyon (LED / OLED)" },
            { id: "402", ad: "Ses Sistemi / Amfi" },
            { id: "403", ad: "Projeksiyon Cihazı" }
        ],
        "5": [
            { id: "501", ad: "PlayStation (PS4 / PS5)" },
            { id: "502", ad: "Xbox Konsol" },
            { id: "503", ad: "Oyun Kolu (Gamepad)" },
            { id: "504", ad: "Nintendo Switch" }
        ],
        "6": [
            { id: "601", ad: "Elektronik Kart / Güç Kaynağı" },
            { id: "602", ad: "Özel Tasarım / Diğer" }
        ]
    };

    if (anaKategoriSelect && cihazTuruSelect) {
        anaKategoriSelect.addEventListener("change", function () {
            const secilenKategori = this.value;
            
            // Sıfırla ve kilitle
            cihazTuruSelect.innerHTML = '<option value="">Cihaz Türü Seçiniz</option>';
            
            if (secilenKategori && cihazTurleriVeri[secilenKategori]) {
                cihazTuruSelect.removeAttribute("disabled");
                
                cihazTurleriVeri[secilenKategori].forEach(tur => {
                    const option = document.createElement("option");
                    option.value = tur.id;
                    option.textContent = tur.ad;
                    cihazTuruSelect.appendChild(option);
                });
            } else {
                cihazTuruSelect.setAttribute("disabled", "true");
                cihazTuruSelect.innerHTML = '<option value="">Önce Ana Kategori Seçiniz</option>';
            }
        });
    }

    // 2. İle Göre İlçeleri Doldurma (Örn: Kayseri, Yozgat, İstanbul vb.)
    const ilSelect = document.getElementById("cmbIl");
    const ilceSelect = document.getElementById("cmbIlce");

    const ilceVeri = {
        "38": [ // Kayseri
            { id: "381", ad: "Melikgazi" },
            { id: "382", ad: "Kocasinan" },
            { id: "383", ad: "Talas" },
            { id: "384", ad: "İncesu" }
        ],
        "66": [ // Yozgat
            { id: "661", ad: "Merkez" },
            { id: "662", ad: "Sorgun" },
            { id: "663", ad: "Yerköy" },
            { id: "664", ad: "Akdağmadeni" }
        ],
        "34": [ // İstanbul
            { id: "341", ad: "Kadıköy" },
            { id: "342", ad: "Beşiktaş" },
            { id: "343", ad: "Ümraniye" },
            { id: "344", ad: "Şişli" }
        ]
    };

    if (ilSelect && ilceSelect) {
        ilSelect.addEventListener("change", function () {
            const secilenIl = this.value;
            
            ilceSelect.innerHTML = '<option value="">İlçe Seçiniz</option>';
            
            if (secilenIl && ilceVeri[secilenIl]) {
                ilceSelect.removeAttribute("disabled");
                
                ilceVeri[secilenIl].forEach(ilce => {
                    const option = document.createElement("option");
                    option.value = ilce.id;
                    option.textContent = ilce.ad;
                    ilceSelect.appendChild(option);
                });
            } else {
                ilceSelect.setAttribute("disabled", "true");
                ilceSelect.innerHTML = '<option value="">Önce İl Seçiniz</option>';
            }
        });
    }

    // 3. Talep Formu Gönderim İşlemi (Simülasyon)
    const talepFormu = document.getElementById("formTalepOlustur");
    if (talepFormu) {
        talepFormu.addEventListener("submit", function (e) {
            e.preventDefault();
            
            // Başarılı bildirim simülasyonu
            alert("Tebrikler! Tamir talebiniz başarıyla oluşturuldu. Yakındaki tamircilere iletiliyor...");
            
            // Panele yönlendirme
            window.location.href = "musteri-paneli.html";
        });
    }

});