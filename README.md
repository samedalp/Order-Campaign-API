# Order-Campaign-API

EP içerisinde tanımlı kampanyalar<br><br>
**Quantity Based Campaign**

Belirli bir üründe X al Y öde

Örnek:

2 al 1 öde
max 1 ücretsiz<br><br>
<hr>

**Category Percentage Campaign**

Belirli kategori için yüzde indirim uygulanır.

Örnek:

Kategori: Kişisel Gelişim
İndirim: %10
<br>
<hr>

**Order Total Percentage Campaign**

Sipariş toplamı belirli bir tutarı geçtiğinde indirim uygulanır.<br>

Örnek:

100 TL üzeri siparişlere %5 indirim

**Kargo Kuralları**

50 TL ve üzeri siparişlerde kargo ücretsiz

50 TL altı siparişlerde 10 TL kargo ücreti

<br>
<hr>

**Kurulm için**
<br>
composer install<br>
cp .env.example .env (API KEY .env.example içerisinde)


 **DB yi oluşturabilmek için** 

php artisan migrate<br>
php artisan db:seed (Var olan json dosyalarının içerisindeki fataları seed edecektir.)


EP testleri için Postman collection
https://web.postman.co/workspace/My-Workspace~e382add4-fc8b-43e8-8f4d-0da468575ba4/collection/14745227-a8742590-0c5d-401e-805f-c54866a83a2f?action=share&source=copy-link&creator=14745227
<br>
**Temsili notificationı çalıştırmak için**<br>
php artisan queue:work 
