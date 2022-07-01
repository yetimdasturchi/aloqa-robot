- [Aloqa robot](#aloqa-robot)
  * [Xususiyatlar](#xususiyatlar)
  * [Demonstratsiya](#demonstratsiya)
  * [O'rnatish](#o-rnatish)
    + [Talablar](#talablar)
    + [Jarayon](#jarayon)
    + [Cronjob](#cronjob)
    + [Webhook](#webhook)
  * [Veb interfeys](#veb-interfeys)
  * [Danat](#danat)
# Aloqa robot
Ushbu bot Namangan viloyati, Kosonsoy tumani, aholisining raqamli turmush tarzini rivojlantirish maqsadida ishlab chiqilgan. O'zbekistondagi boshqa hududlarda ham shunday jarayonga hissa qo'shish maqsadida bot kodlari ochiqlanmoqda. 

 ![screen](https://i.ibb.co/F6bW3FF/photo-2022-07-01-05-02-09.jpg)

## Xususiyatlar
- Guruhlar asosida kontaktlarni boshqarish
- Kontaktlardan izlash
- Kontaktlarni reyting asosida qidiruv uchun saralash
- Kontaktlarni botdan va veb interfeysdan boshqarish
- Qayta aloqa va murojaatlar bilan ishlash
- Foydalanuvchilarga ommaviy xabarnomalar yo'llash (matn, video, rasm)
- Foydalanuvchilar haqida ma'lumotnoma olish
- Lotin va kiril alifbosi bilan ishlay olish, taabga ko'ra boshqa tillarni ham til paketlari orqali qo'shish

## Demonstratsiya
[https://t.me/kosonsoyaloqa_bot](https://t.me/kosonsoyaloqa_bot)

## O'rnatish

### Talablar
1. **PHP 7.1** yoki undan yuqori versiya
2. **Nginx** yoki **apache**

### Jarayon

1. Kerakli veb domenning `public_html` yoki `www` papkasiga barcha kodlarni ko'chirish
2. `config.php` faylidan bot uchun `token` kiritish
3. `config.php` faylidan bot administratorlarining telegram idenfikatorini  `owner` massiviga kiritish

### Cronjob
OImmaviy bildirishnomalarni yuborish uchun cronjob zarur hisoblanadi. Cronjobga kiritishning ikki usuli mavjud. Agarda sizga to'g'ridan to'g'ri bash orqali serverga kiritish lozim bo'lsa quyidagi buyruqni `crontab -e` orqali server yonish jarayoniga sozlang.
```bash
@reboot /usr/bin/php /var/www/domen.uz/crone.php > /dev/null 2>&1
```
Internet panel orqali cronjob kiritish uchun `crone.php` faylining 60-qatoridagi ushbu kodni
```php
while (1){
	send_notifications();
	usleep(2000);
}
```
quyidagiga almashtirib cronjobni har daqiqaga ishga tushirishga sozlang.
```php
send_notifications();
```
### Webhook

```
https://api.telegram.org/botTOKEN/setWebhook?url=https://domen.uz/bot/hook.php
```

## Veb interfeys
![enter image description here](https://i.ibb.co/b2RR1kz/download-4.jpg)
Kontaktlarni veb interfeys orqali boshqarish domenda joylashgan bot papkasida brauzer orqali kirish orqali amalga oshiriladi.

**Parol:** admin123

**URL:** `https://domen.uz/bot/manager.php`

## Danat
  
Ushbu loyiha o'zbek internet segmentida _opensource_ manbalarni rivojlantirish uchun ishlab chiqilgan. Agarda loyiha bardavom ishlashi va yanada rivojlanishini istasangiz loyihani moliyaviy qo'llab quvvatlashingiz mumkin.

### Payme orqali qo'llab quvvatlash:

O'tkazma uchun havola: [https://payme.uz/5e0f1d58672f9f51948124b0](https://payme.uz/5e0f1d58672f9f51948124b0)


### Click orqali qo'llab quvvatlash:

O'tkazma uchun [buyerga](https://my.click.uz/clickp2p/EB98FF51ADBC2C3E115C409D117A0BDDB2EA85202E774A1210879388506CAD6B) bosing.
