# 🆘 TROUBLESHOOTING - MBTI Grid Tidak Tampil

## MASALAH: Quiz tidak menampilkan opsi MBTI

### 🔍 Solusi Cepat

#### **Step 1: Refresh Browser (Paling Penting!)**
```
Tekan: Ctrl + Shift + R  (Windows/Linux)
atau   Cmd + Shift + R   (Mac)
```
Ini akan clear cache browser dan reload semua file CSS/JS.

---

#### **Step 2: Check Browser Console**
1. Buka halaman quiz
2. Tekan **F12** untuk buka Developer Tools
3. Klik tab **Console**
4. Lihat apakah ada error merah

**Error yang harus dihindari:**
- ❌ `Gagal load data quiz dari server`
- ❌ `Cannot read property 'forEach' of undefined`
- ❌ `buildMBTIGrid is not defined`

Jika ada error, screenshot dan laporkan.

---

#### **Step 3: Check API Response**
Buka di tab browser baru:
```
http://localhost/KomasYoubrikmaihart/web/api/get-quiz-data.php
```

**Pastikan:**
- [ ] Halaman tidak kosong
- [ ] Bukan error message
- [ ] Ada JSON dengan struktur MBTI_DATA
- [ ] Bukan "500 Internal Server Error"

Jika error, buka terminal dan:
```bash
# Check MySQL status
# Ensure MySQL running di XAMPP Control Panel
```

---

#### **Step 4: Hard Refresh File CSS**
Jika grid MBTI tampil tapi styling jelek:
1. Buka `web/css/quiz.css` di editor
2. Cari baris yang dimulai dengan `/* MBTI GRID */`
3. Pastikan ada di file

---

## KEMUNGKINAN MASALAH & SOLUSI

### ❌ Masalah: "Quiz tidak muncul sama sekali"
**Solusi:**
1. Pastikan sudah login dulu
2. Buka: `http://localhost/KomasYoubrikmaihart/web/quiz.php`
3. Jika masih blank, cek error di console (F12)

---

### ❌ Masalah: "MBTI grid kosong / tidak ada kartu"
**Solusi:**
1. API tidak loaded
   - Check: `http://localhost/KomasYoubrikmaihart/web/api/get-quiz-data.php`
   - Pastikan ada data JSON
2. MBTI_DATA kosong
   - Console → type: `MBTI_DATA` → Enter
   - Harus menampilkan object dengan 16 keys
3. JavaScript error
   - Cek console (F12) untuk error merah

---

### ❌ Masalah: "Kartu MBTI tampil tapi tidak bisa diklik"
**Solusi:**
1. Refresh browser (Ctrl+Shift+R)
2. Check console untuk JavaScript error
3. Coba klik kartu lain

---

### ❌ Masalah: "Styling jelek / kartu tidak rapi"
**Solusi:**
1. Hard refresh CSS: Ctrl+Shift+R
2. Check apakah `.mbti-grid` ada di CSS
3. Check browser window size (mungkin mobile responsive)

---

## DEBUGGING CHECKLIST

### Browser Console (F12)

**Test 1: Check MBTI Data**
```javascript
console.log(MBTI_DATA);
// Hasil yang diharapkan: Object dengan 16 keys (INTJ, INTP, dst)
```

**Test 2: Check Grid Element**
```javascript
console.log(document.getElementById("mbtiGrid"));
// Hasil yang diharapkan: <div class="mbti-grid" id="mbtiGrid">...</div>
```

**Test 3: Check CSS Classes**
```javascript
console.log(document.querySelector(".mbti-card"));
// Hasil yang diharapkan: <div class="mbti-card">...</div>
```

---

## FULL FLOW VERIFICATION

### 1️⃣ Buka Quiz Page
```
http://localhost/KomasYoubrikmaihart/web/quiz.php
```
✓ Halaman load without error

### 2️⃣ Check Console (F12)
✓ Tidak ada error merah  
✓ Bisa lihat `MBTI_DATA` dengan 16 tipe

### 3️⃣ Look for MBTI Grid
✓ 16 kartu MBTI terlihat  
✓ Setiap kartu punya:
- Kode MBTI (INTJ, ENFP, dst)
- Nama tipe (Arsitek, Juru Kampanye, dst)
- Deskripsi singkat

### 4️⃣ Click MBTI Card
✓ Kartu berubah warna (selected)  
✓ Tombol "Lanjutkan" enabled  
✓ Bisa klik tombol untuk lanjut

---

## JIKA MASIH TIDAK BEKERJA

### Langkah Emergency:
1. **Backup** file `web/js/quiz.js`
2. **Replace** dengan versi backup (jika ada)
3. **Refresh** browser
4. **Test** lagi

### Atau:
1. Delete browser cache
2. Close semua tab dari aplikasi ini
3. Open tab baru
4. Buka quiz page lagi
5. Tekan F5 refresh

---

## COMMAND LINE CHECKS

**Check MySQL Connection:**
```bash
# Windows
mysql -h 127.0.0.1 -u root -p karirmatch

# Then type password (default kosong di XAMPP, just press Enter)
# Jika masuk, type: SELECT COUNT(*) FROM mbti_types;
# Hasil harus: 16
```

**Check API via cURL:**
```bash
curl http://localhost/KomasYoubrikmaihart/web/api/get-quiz-data.php
```

---

## REPORT BUG

Jika masih error, catat:
1. **URL** yang diakses
2. **Error message** dari console (F12)
3. **Browser** yang digunakan (Chrome, Firefox, Safari)
4. **OS** (Windows, Mac, Linux)
5. **Screenshot** jika bisa

Lalu laporkan dengan semua informasi di atas.

---

## QUICK FIXES (Most Likely Works)

### Fix #1: Hard Refresh
```
Ctrl + Shift + R
```

### Fix #2: Clear Cache
1. F12 → Settings (⚙️)
2. Cek "Disable cache (while DevTools open)"
3. Refresh page

### Fix #3: Check Database
```bash
# Di XAMPP, buka terminal/CMD:
mysql -h 127.0.0.1 -u root karirmatch
SELECT * FROM mbti_types LIMIT 1;
# Harus ada data, bukan error
```

---

**Update:** 2025-05-13  
**Version:** 1.3
