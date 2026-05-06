<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Platform rekomendasi jurusan dan karir berdasarkan MBTI dan tes minat bakat RIASEC" />
    <title>KarirMatch — Temukan Karir Idealmu</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans:wght@400;600&display=swap"
      rel="stylesheet"
    />

    <!-- Stylesheet -->
    <link rel="stylesheet" href="css/quiz.css" />
  </head>

  <body>

    <!-- ============ HERO ============ -->
    <div class="hero">
      <h1>Temukan Karir Idealmu</h1>
      <p>Platform rekomendasi jurusan &amp; karir berbasis MBTI dan Minat Bakat</p>
    </div>

    <!-- ============ MAIN APP ============ -->
    <div class="app">

      <!-- PROGRESS BAR -->
      <div class="progress-wrap">
        <div class="progress-inner">
          <div class="step-item">
            <div class="step-circle active" id="dot1">1</div>
            <div class="step-label active" id="lbl1">MBTI</div>
          </div>
          <div class="step-line" id="line1"></div>
          <div class="step-item">
            <div class="step-circle" id="dot2">2</div>
            <div class="step-label" id="lbl2">Rekomendasi</div>
          </div>
          <div class="step-line" id="line2"></div>
          <div class="step-item">
            <div class="step-circle" id="dot3">3</div>
            <div class="step-label" id="lbl3">Minat & Bakat</div>
          </div>
          <div class="step-line" id="line3"></div>
          <div class="step-item">
            <div class="step-circle" id="dot4">4</div>
            <div class="step-label" id="lbl4">Hasil</div>
          </div>
        </div>
      </div>

      <!-- ========== PAGE 1: Apakah tahu MBTI? ========== -->
      <div id="page1" class="page">
        <div class="card">
          <div class="card-title">Apakah kamu sudah tahu tipe MBTI-mu?</div>
          <div class="card-sub">
            MBTI membantu memahami cara berpikir, bekerja, dan berinteraksimu.
            Pilih opsi di bawah untuk memulai.
          </div>
          <div class="flex-gap">
            <button class="btn btn-primary" onclick="showPage(2)">✓ Ya, saya sudah mengetahui MBTI saya</button>
            <a
              class="btn-ext"
              href="https://www.16personalities.com/id/tes-kepribadian"
              target="_blank"
              rel="noopener noreferrer"
            >Tes MBTI Sekarang ↗</a>
          </div>
        </div>
        <div class="highlight-card">
          <strong>Sudah selesai tes di 16personalities?</strong>
          Kembali ke sini setelah mendapatkan hasil tesmu, lalu klik tombol
          <em>"Ya, saya sudah mengetahui MBTI saya"</em> untuk melanjutkan ke langkah berikutnya.
        </div>
      </div>

      <!-- ========== PAGE 2: Pilih MBTI ========== -->
      <div id="page2" class="page hidden">
        <div class="card">
          <div class="card-title">Pilih Tipe MBTI-mu</div>
          <div class="card-sub">
            Klik salah satu dari 16 tipe kepribadian di bawah ini sesuai hasil tes kamu.
          </div>
          <!-- Grid MBTI diisi oleh JS -->
          <div class="mbti-grid" id="mbtiGrid"></div>
          <div class="divider"></div>
          <div class="flex-gap">
            <button class="btn btn-primary disabled" id="mbtiNext" onclick="goToFilter()">
              Lanjutkan →
            </button>
            <button class="btn btn-light" onclick="showPage(1)">← Kembali</button>
          </div>
        </div>
      </div>

      <!-- ========== PAGE 3: Filter Preview ========== -->
      <div id="page3" class="page hidden">
        <div class="card">
          <div class="card-title" id="filterTitle">Hasil Rekomendasi Berdasarkan MBTI</div>
          <div class="card-sub">
            Berdasarkan kepribadianmu, ini bidang dan karir yang paling relevan.
            Selanjutnya kita akan memperhalus hasil ini dengan tes minat bakat.
          </div>
          <div class="match-alert" id="matchAlert"></div>
          <div class="sec-head">Bidang yang Cocok</div>
          <div class="filter-wrap" id="filterFields"></div>
          <div class="sec-head">Karir yang Potensial</div>
          <div class="filter-wrap" id="filterCareers"></div>
          <div class="divider"></div>
          <div class="flex-gap">
            <button class="btn btn-primary" onclick="showPage(4)">Mulai Tes Minat Bakat →</button>
            <button class="btn btn-light" onclick="showPage(2)">← Kembali</button>
          </div>
        </div>
      </div>

      <!-- ========== PAGE 4: RIASEC Quiz ========== -->
      <div id="page4" class="page hidden">
        <div class="card">
          <div class="card-title">Tes Minat Bakat</div>
          <div class="card-sub">
            Jawab 12 pertanyaan berikut dengan jujur.
            Nilai 1 = Tidak suka sama sekali, nilai 5 = Sangat suka.
          </div>
          <!-- Pertanyaan diisi oleh JS -->
          <div id="riasecQuestions"></div>
          <div class="divider"></div>
          <div class="flex-gap">
            <button class="btn btn-primary disabled" id="riasecSubmit" onclick="calcResults()">
              Lihat Rekomendasi →
            </button>
            <button class="btn btn-light" onclick="showPage(3)">← Kembali</button>
          </div>
        </div>
      </div>

      <!-- ========== PAGE 5: Hasil ========== -->
      <div id="page5" class="page hidden">
        <!-- Diisi oleh JS -->
        <div id="resultHero"></div>
        <div class="rec-grid" id="recGrid"></div>
        <div id="topCareers"></div>
        <div style="margin-top: 24px; text-align: center;">
          <button class="reset-btn" onclick="resetApp()">↺ Mulai ulang dari awal</button>
        </div>
      </div>

    </div><!-- /.app -->

    <!-- ============ JAVASCRIPT ============ -->
    <script src="js/quiz.js"></script>

  </body>
</html>
