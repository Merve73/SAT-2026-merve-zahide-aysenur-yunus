# Temel seviye kod ve satır yorumları

Bu projeyi 4 bilgisayar programcılığı öğrencisi geliştiriyor. Bilgi düzeyi çok temel; neredeyse yok sayılacak kadar az. Proje boyunca yazılan her kod bu kurala uyar.

(Bu kural `.cursor/rules/temel-seviye-kod.mdc` ile aynıdır. Birini değiştirirsen diğerini de güncelle.)

## Nasıl yazılır

- En basit söz dizimini kullan. İleri konular (sınıf, generic, decorator, async/await, lambda, kısaltılmış söz dizimi) gerekmedikçe kullanılmaz.
- Bir işi tek satırda sıkıştırmak yerine adım adım, ayrı satırlarda yaz.
- Değişken ve fonksiyon adları ne yaptığını söylesin. Kısaltma kullanma.
- Açıklamalar Türkçe olsun. Öğrencinin o satırı tek başına okuyup anlaması hedeflenir.

## Yorum kuralı

- Yazılan her satırın üstüne, o satırın ne yaptığını anlatan bir yorum ekle.
- Yorum, kodun Türkçe tekrarı değil; neden yazıldığını da kısaca söylesin.
- Boş satır, yalnızca küme parantezi olan satır ve zaten yorum olan satır bu kuralın dışındadır.

## Örnek

```javascript
// Sayfanın başlığını tutan kutu. Sonra ekrana yazılacak.
var sayfaBasligi = "Tamir Bul";

// Başlığı tarayıcı penceresinin başlık çubuğuna yazar.
document.title = sayfaBasligi;
```
