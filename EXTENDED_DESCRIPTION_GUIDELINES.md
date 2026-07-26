# EXTENDED DESCRIPTION GUIDELINES

**قابِلِ اطلاق ہدایت نامہ برائے ہر کمٹ**

ہر بار جب بھی اس رپو (`Moon060781/ng-web-m81`) میں کوئی فائل، فنکشن یا UI/UX میں تبدیلی کی جاتی ہے تو **EXTENDED DESCRIPTION** لازمی طور پر تیار کرنی ہوگی۔ یہ دستاویز `git commit` کے ساتھ `-m` اور `-e` (یا `--author`) استعمال کرتے ہوئے شامل کی جائے گی۔

### فارمیٹ
1. **پاتھ** – وہ فائل یا صفحے کا رشتہ دار پاتھ جہاں تبدیلی کی گئی (مثال: `deploy.php`، `index.html`). 
2. **تبدیلی کے نکات** – تبدیلی کے اہم نکات کی فہرست (بُلڈ پوائنٹس یا سادہ جملے).
3. **دستخط** – آخر میں درج کریں:
   ```
   change commit by Antigravity (Geo PC)
   ```
   جہاں `Geo PC` اس اینٹی‑گریویٹی لاگ‑ان کے نام یا شناخت ہے (آپ کے جیو کے آفس پر نصب شدہ PC کا نام)۔

### مثال
```
Path: deploy.php

- Added commit hash display above "Commit Highlight".
- Enhanced PKT time display using JavaScript.

change commit by Antigravity (Geo PC)
```

یہ گائیڈ لائنز رپو کی جڑ (root) میں `EXTENDED_DESCRIPTION_GUIDELINES.md` کے نام سے محفوظ کی گئی ہیں۔
