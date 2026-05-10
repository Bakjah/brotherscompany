# Brothers Company - Full Stack Website

Complete website for Brothers Company with 3 divisions and marketplace.

## 🚀 Quick Setup (5 Minutes)

1. **Extract** ZIP to `C:\xampp\htdocs\brothers-company\`
2. **Import Database** via phpMyAdmin → database/brothers_company_db.sql
3. **Start** XAMPP (Apache + MySQL)
4. **Open** http://localhost/brothers-company/

## 👤 Default Accounts

**Admin:**
- Email: admin
- Password: #?12jj16op

## 📑 Pages

- **Home** - Landing page with divisions
- **Marketplace** - Sell/buy items with photo upload
- **Workshop** - Info page
- **Farm** - Info page
- **Asian Food** - Info page
- **Admin Panel** - Manage announcements & marketplace posts

## 🛒 Marketplace Form

Post items with:
- Nama Barang (Product name)
- Jenis Barang (Category)
- No Telepon (Phone)
- ID Discord (Discord ID)
- Foto Barang (Photo upload)
- Keterangan (Description)

## 🔑 Admin Features

- Create/Delete Announcements
- Delete Marketplace Posts

## 🎨 Colors

- Blue: #1e40af (Main)
- Yellow: #fbbf24 (Workshop)
- Green: #10b981 (Farm)
- Red: #ef4444 (Asian Food)
- Purple: #8b5cf6 (Marketplace)

## 💻 Tech Stack

- HTML5, CSS3, Vanilla JS
- PHP (Native)
- MySQL

## 📂 Structure

```
brothers-company/
├── index.php
├── login.php & register.php
├── marketplace/ (upload foto)
├── workshop/
├── farm/
├── asianfood/
├── admin/
├── assets/
│   ├── css/style.css
│   └── uploads/
└── database/SQL
```

## ⚠️ Important

- Password stored as plain text for simplicity
- Admin only can delete/create announcements
- Anyone logged in can post to marketplace
- Photos saved in assets/uploads/

## 🎯 Marketplace Form Fields

✅ Nama Barang - Required  
✅ Jenis Barang - Required  
✅ No Telepon - Required  
✅ ID Discord - Required  
✅ Foto Barang - Optional (JPG/PNG, max 5MB)  
✅ Keterangan - Required  

