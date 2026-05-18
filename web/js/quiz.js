/* =========================================
   KarirMatch — Application Logic
   HYBRID MBTI + RIASEC ENGINE v3.0
   ========================================= */

/* ----------------------------------------
   GLOBAL DATA (loaded from API)
---------------------------------------- */

let MBTI_DATA   = {};
let RIASEC_Q    = [];
let RIASEC_INFO = {};
let JURUSAN     = {};

/* ----------------------------------------
   STATE
---------------------------------------- */

let selectedMBTI = null;
let answers      = {};       // { qi: { val, t } }

/* ----------------------------------------
   CONSTANTS — weight tuning
   
   PERUBAHAN v3.0:
   - WEIGHT_QUIZ naik dari 0.60 → 0.65
     (hasil tes langsung lebih dominan)
   - WEIGHT_MBTI tetap 0.35 sebagai anchor kepribadian
   - TOP_N_RIASEC tetap 3 (Holland hexagon best practice)
   - CAREER_MBTI_BONUS: bobot khusus karir yang muncul
     di BOTH RIASEC dan MBTI (double confirmed)
---------------------------------------- */

const WEIGHT_QUIZ        = 0.65;
const WEIGHT_MBTI        = 0.35;
const TOP_N_RIASEC       = 3;
const CAREER_MBTI_BONUS  = 0.45;  // bonus jika karir dikonfirmasi oleh MBTI juga
const MAJOR_MBTI_BONUS   = 0.35;  // bonus serupa untuk jurusan


/* ========================================
   SECTION 1 — DATA LOADING
======================================== */

async function loadQuizData() {
  try {
    const res  = await fetch("api/get-quiz-data.php");

    if (!res.ok) {
      const errText = await res.text();
      throw new Error(`Server error ${res.status}: ${errText.slice(0, 200)}`);
    }

    const data = await res.json();

    MBTI_DATA   = data.MBTI_DATA   || {};
    RIASEC_Q    = data.RIASEC_Q    || [];
    RIASEC_INFO = data.RIASEC_INFO || {};
    JURUSAN     = data.JURUSAN     || {};

    init();
  } catch (err) {
    console.error("Gagal load data quiz:", err);
    alert("Gagal mengambil data quiz dari server.\n\nDetail: " + err.message);
  }
}

function init() {
  buildMBTIGrid();
  buildRIASECQuiz();
}


/* ========================================
   SECTION 2 — BUILD UI
======================================== */

function buildMBTIGrid() {
  const grid = document.getElementById("mbtiGrid");
  if (!grid) return;
  grid.innerHTML = "";

  if (!MBTI_DATA || Object.keys(MBTI_DATA).length === 0) {
    grid.innerHTML = "<p style='color: red;'>⚠️ Data MBTI tidak dimuat. Refresh halaman.</p>";
    return;
  }

  Object.keys(MBTI_DATA).forEach((type) => {
    const d  = MBTI_DATA[type];
    const el = document.createElement("div");
    el.className = "mbti-card";
    el.id        = "mc_" + type;
    el.innerHTML = `
      <div class="mbti-type">${type}</div>
      <div class="mbti-name">${d.name}</div>
      <div class="mbti-desc">${d.desc}</div>
    `;
    el.addEventListener("click", () => {
      pickMBTI(type);
    });
    grid.appendChild(el);
  });
}

function showMBTIModal(type, data) {
  const modal = document.getElementById("mbtiModal");
  if (!modal) return;

  const riasecLabels = data.riasec
    .map(r => RIASEC_INFO[r]?.label || r)
    .join(", ");

  const fieldsHTML = data.fields
    .map(f => `<span class="modal-tag">${f}</span>`)
    .join("");

  const careersHTML = data.careers.slice(0, 5)
    .map(c => `<span class="modal-tag">${c}</span>`)
    .join("");

  const riasecHTML = data.riasec
    .map(r => `<div class="riasec-badge ${r.toLowerCase()}">${r}</div>`)
    .join("");

  modal.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <div class="modal-title">${type} — ${data.name}</div>
          <div style="font-size: 12px; color: var(--p-color); margin-top: 4px;">"${data.desc}"</div>
        </div>
        <button class="modal-close" onclick="closeMBTIModal()">✕</button>
      </div>

      <div class="modal-section">
        <div class="modal-section-title">Profil RIASEC</div>
        <div class="modal-section-list riasec">
          ${riasecHTML}
        </div>
        <div style="font-size: 11px; color: var(--p-color); margin-top: 8px;">
          <strong>Dominan:</strong> ${riasecLabels}
        </div>
      </div>

      <div class="modal-section">
        <div class="modal-section-title">Bidang Kerja</div>
        <div class="modal-section-list">
          ${fieldsHTML}
        </div>
      </div>

      <div class="modal-section">
        <div class="modal-section-title">Contoh Karir</div>
        <div class="modal-section-list">
          ${careersHTML}
        </div>
      </div>

      <div class="modal-action">
        <button class="btn-select" onclick="selectMBTIFromModal('${type}')">
          ✓ Pilih ${type}
        </button>
        <button class="btn-cancel" onclick="closeMBTIModal()">
          Batal
        </button>
      </div>
    </div>
  `;

  const overlay = document.getElementById("mbtiModalOverlay");
  if (overlay) {
    overlay.classList.add("active");
  }
}

function closeMBTIModal() {
  const overlay = document.getElementById("mbtiModalOverlay");
  if (overlay) {
    overlay.classList.remove("active");
  }
}

function selectMBTIFromModal(type) {
  pickMBTI(type);
  closeMBTIModal();
  
  setTimeout(() => {
    const btn = document.getElementById("mbtiNext");
    if (btn && !btn.classList.contains("disabled")) {
      goToFilter();
    }
  }, 300);
}

function buildRIASECQuiz() {
  const wrap = document.getElementById("riasecQuestions");
  if (!wrap) return;
  wrap.innerHTML = "";

  RIASEC_Q.forEach((q, i) => {
    const div = document.createElement("div");
    div.className = "riasec-q";
    div.innerHTML = `
      <div class="q-text">${i + 1}. ${q.q}</div>
      <div class="scale-wrap">
        <span class="scale-lbl">Tidak suka</span>
        <div class="scale-btns" id="sb_${i}">
          ${[1,2,3,4,5].map(v => `
            <button class="scale-btn" data-qi="${i}" data-val="${v}">${v}</button>
          `).join("")}
        </div>
        <span class="scale-lbl r">Sangat suka</span>
      </div>
    `;
    wrap.appendChild(div);
  });

  wrap.addEventListener("click", (e) => {
    const btn = e.target.closest(".scale-btn");
    if (!btn) return;
    pickScale(parseInt(btn.dataset.qi), parseInt(btn.dataset.val), btn);
  });
}


/* ========================================
   SECTION 3 — INTERACTION
======================================== */

function pickMBTI(type) {
  if (selectedMBTI) {
    const old = document.getElementById("mc_" + selectedMBTI);
    if (old) old.className = "mbti-card";
  }
  selectedMBTI = type;
  document.getElementById("mc_" + type).className = "mbti-card selected";

  const btn = document.getElementById("mbtiNext");
  if (btn) btn.classList.remove("disabled");
}

function pickScale(qi, val, btn) {
  answers[qi] = { val, t: RIASEC_Q[qi].t };

  document.querySelectorAll(`#sb_${qi} .scale-btn`)
    .forEach(b => b.className = "scale-btn");
  btn.className = "scale-btn sel";

  if (Object.keys(answers).length === RIASEC_Q.length) {
    const submit = document.getElementById("riasecSubmit");
    if (submit) submit.classList.remove("disabled");
  }
}


/* ========================================
   SECTION 4 — PAGE NAVIGATION
======================================== */

function showPage(n) {
  [1,2,3,4,5].forEach(i => {
    const p = document.getElementById("page" + i);
    if (p) p.classList.add("hidden");
  });
  document.getElementById("page" + n)?.classList.remove("hidden");
  updateProgress(n);
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function updateProgress(page) {
  const stepMap = { 1:1, 2:1, 3:2, 4:3, 5:4 };
  const active  = stepMap[page] || 1;

  [1,2,3,4].forEach(i => {
    const dot  = document.getElementById("dot" + i);
    const lbl  = document.getElementById("lbl" + i);
    if (!dot || !lbl) return;

    if (i < active) {
      dot.className   = "step-circle done";
      dot.textContent = "✓";
    } else if (i === active) {
      dot.className   = "step-circle active";
      dot.textContent = i;
    } else {
      dot.className   = "step-circle";
      dot.textContent = i;
    }

    lbl.className = (i === active) ? "step-label active" : "step-label";

    if (i < 4) {
      const line = document.getElementById("line" + i);
      if (line) line.className = (i < active) ? "step-line done" : "step-line";
    }
  });
}

function goToFilter() {
  if (!selectedMBTI) return;

  const d = MBTI_DATA[selectedMBTI];

  document.getElementById("filterTitle").textContent =
    `Rekomendasi Awal: ${selectedMBTI} — ${d.name}`;

  document.getElementById("matchAlert").innerHTML = `
    <strong>${selectedMBTI} (${d.name})</strong> — ${d.desc}.<br>
    Profil RIASEC bawaan tipe ini:
    <strong>${d.riasec.map(r => RIASEC_INFO[r]?.label || r).join(", ")}</strong>.
    <br><small style="opacity:.7">
      Lanjutkan ke tes minat bakat agar rekomendasi akhir lebih personal dan akurat.
    </small>
  `;

  document.getElementById("filterFields").innerHTML =
    d.fields.map(f => `<span class="ftag">${f}</span>`).join("");

  document.getElementById("filterCareers").innerHTML =
    d.careers.map(c => `<span class="ftag">${c}</span>`).join("");

  showPage(3);
}


/* ========================================
   SECTION 5 — CORE HYBRID ALGORITHM v3.0
======================================== */

/**
 * Hitung skor RIASEC murni dari jawaban quiz.
 * Normalisasi ke 0–1 menggunakan rata-rata per kategori dibagi 5.
 */
function calcRIASECFromQuiz() {
  const sum   = { R:0, I:0, A:0, S:0, E:0, C:0 };
  const count = { R:0, I:0, A:0, S:0, E:0, C:0 };

  Object.values(answers).forEach(({ val, t }) => {
    sum[t]   += val;
    count[t] += 1;
  });

  const scores = {};
  ["R","I","A","S","E","C"].forEach(k => {
    scores[k] = count[k] > 0 ? (sum[k] / count[k]) / 5 : 0;
  });

  return scores;
}

/**
 * Vektor RIASEC bawaan MBTI — bobot bertingkat berdasarkan urutan dominansi.
 * RIASEC pertama (dominan) = 1.0, kedua = 0.65, ketiga = 0.35
 * (lebih tajam dari v2 untuk membedakan kekuatan utama vs pendukung)
 */
function getMBTIRIASECVector(mbtiCode) {
  const vec    = { R:0, I:0, A:0, S:0, E:0, C:0 };
  const riasec = MBTI_DATA[mbtiCode]?.riasec || [];
  const weights = [1.0, 0.65, 0.35];

  riasec.forEach((code, idx) => {
    if (vec[code] !== undefined) {
      vec[code] = weights[idx] ?? 0.2;
    }
  });

  return vec;
}

/**
 * FUNGSI UTAMA — weighted average hybrid.
 * hybridScore[k] = (WEIGHT_QUIZ × quizScore[k]) + (WEIGHT_MBTI × mbtiVector[k])
 */
function calcHybridRIASEC(quizScores, mbtiVector) {
  const hybrid = {};
  ["R","I","A","S","E","C"].forEach(k => {
    hybrid[k] = (WEIGHT_QUIZ * quizScores[k]) + (WEIGHT_MBTI * mbtiVector[k]);
  });
  return hybrid;
}

/**
 * Cosine similarity antara vektor quiz dan vektor MBTI default.
 * Mengukur seberapa konsisten hasil tes dengan kepribadian yang dipilih.
 * @returns {number} 0..100
 */
function calcConsistencyScore(quizScores, mbtiVector) {
  const keys = ["R","I","A","S","E","C"];
  let dot = 0, normA = 0, normB = 0;

  keys.forEach(k => {
    dot   += quizScores[k] * mbtiVector[k];
    normA += quizScores[k] ** 2;
    normB += mbtiVector[k] ** 2;
  });

  if (normA === 0 || normB === 0) return 0;
  return Math.round((dot / (Math.sqrt(normA) * Math.sqrt(normB))) * 100);
}

/**
 * SCORING KARIR v3.0 — "Double Confirmation" method
 *
 * Setiap karir mendapat skor dari dua sumber:
 *   A. RIASEC hybrid score × rank multiplier
 *   B. CAREER_MBTI_BONUS jika karir juga ada di MBTI_DATA (double confirmed)
 *
 * Ini memastikan karir yang dikonfirmasi oleh KEDUANYA mendapat prioritas lebih tinggi,
 * bukan sekadar muncul dari satu dimensi saja.
 */
function scoreCareersByHybrid(topRIASEC, hybridScores) {
  const careerMap = {};

  // Sumber A: karir dari RIASEC_INFO berdasarkan hybrid top-N
  topRIASEC.forEach((rCode, rank) => {
    const multiplier = 1 - (rank * 0.20);   // rank-0=1.0, rank-1=0.80, rank-2=0.60
    const careers    = RIASEC_INFO[rCode]?.careers || [];

    careers.forEach(career => {
      if (!careerMap[career]) careerMap[career] = 0;
      careerMap[career] += hybridScores[rCode] * multiplier;
    });
  });

  // Sumber B: karir dari MBTI — bonus jika overlap dengan topRIASEC
  const mbtiCareers = MBTI_DATA[selectedMBTI]?.careers || [];
  const mbtiRIASEC  = MBTI_DATA[selectedMBTI]?.riasec  || [];

  mbtiCareers.forEach(career => {
    // Hitung seberapa banyak MBTI RIASEC yang masuk ke top hybrid user
    const overlapCount = mbtiRIASEC.filter(r => topRIASEC.includes(r)).length;
    // Bonus proporsional: makin banyak overlap, makin besar bonus
    const bonus = (overlapCount / Math.max(mbtiRIASEC.length, 1)) * CAREER_MBTI_BONUS;

    if (!careerMap[career]) careerMap[career] = 0;
    careerMap[career] += bonus;
  });

  return Object.entries(careerMap)
    .map(([name, score]) => ({ name, score }))
    .sort((a, b) => b.score - a.score);
}

/**
 * SCORING JURUSAN v3.0 — sama seperti scoreCareersByHybrid
 * tapi menggunakan tabel JURUSAN.
 * Jurusan dari tipe RIASEC yang overlap dengan MBTI mendapat bonus.
 */
function scoreMajorsByHybrid(topRIASEC, hybridScores) {
  const majorMap  = {};
  const mbtiRIASEC = MBTI_DATA[selectedMBTI]?.riasec || [];

  topRIASEC.forEach((rCode, rank) => {
    const multiplier  = 1 - (rank * 0.20);
    const majors      = JURUSAN[rCode] || [];

    // Apakah rCode ini juga ada di MBTI default user?
    const isConfirmed = mbtiRIASEC.includes(rCode);
    const bonus       = isConfirmed ? MAJOR_MBTI_BONUS : 0;

    majors.forEach(major => {
      if (!majorMap[major]) majorMap[major] = 0;
      majorMap[major] += (hybridScores[rCode] * multiplier) + bonus;
    });
  });

  return Object.entries(majorMap)
    .map(([name, score]) => ({ name, score }))
    .sort((a, b) => b.score - a.score);
}

/**
 * Narasi profil berdasarkan top RIASEC hybrid + konsistensi MBTI.
 */
function buildProfileNarrative(topRIASEC, consistencyScore, mbtiCode) {
  const mbti     = MBTI_DATA[mbtiCode];
  const labels   = topRIASEC.map(r => RIASEC_INFO[r]?.label || r);
  const dominant = labels[0];

  let alignment = "";
  if (consistencyScore >= 70) {
    alignment = "Profil minat bakatmu sangat selaras dengan kepribadian MBTI-mu";
  } else if (consistencyScore >= 40) {
    alignment = "Profil minat bakatmu cukup selaras dengan kepribadian MBTI-mu";
  } else {
    alignment = "Minat bakatmu memiliki nuansa unik yang melampaui tipikal kepribadian MBTI-mu";
  }

  return `${alignment} (${mbtiCode} — ${mbti.name}). 
    Kekuatan dominanmu ada di bidang <strong>${dominant}</strong>, 
    didukung oleh sifat ${labels.slice(1).join(" dan ")}. 
    Kombinasi ini menunjukkan kamu paling cocok di lingkungan yang 
    ${topRIASEC.includes("S") ? "kolaboratif dan berorientasi pada orang" : 
      topRIASEC.includes("I") ? "analitis dan berbasis riset" :
      topRIASEC.includes("E") ? "kompetitif dan penuh kepemimpinan" :
      topRIASEC.includes("A") ? "kreatif dan ekspresif" :
      topRIASEC.includes("R") ? "teknis dan hands-on" :
      "terstruktur dan sistematis"}.`;
}


/* ========================================
   SECTION 6 — CALCULATE & RENDER RESULTS
======================================== */

function calcResults() {
  if (!selectedMBTI || Object.keys(answers).length < RIASEC_Q.length) return;

  // 1. Skor RIASEC murni dari quiz
  const quizScores   = calcRIASECFromQuiz();

  // 2. Vektor RIASEC dari MBTI
  const mbtiVector   = getMBTIRIASECVector(selectedMBTI);

  // 3. Gabungkan → hybrid score
  const hybridScores = calcHybridRIASEC(quizScores, mbtiVector);

  // 4. Urutkan RIASEC hybrid dari tertinggi
  const sortedRIASEC = Object.entries(hybridScores)
    .sort((a, b) => b[1] - a[1]);

  // 5. Ambil top-N untuk matching
  const topRIASEC = sortedRIASEC.slice(0, TOP_N_RIASEC).map(e => e[0]);

  // 6. Seberapa konsisten quiz dengan MBTI default
  const consistencyScore = calcConsistencyScore(quizScores, mbtiVector);

  // 7. Ranking karir & jurusan menggunakan double-confirmation
  const rankedCareers = scoreCareersByHybrid(topRIASEC, hybridScores);
  const rankedMajors  = scoreMajorsByHybrid(topRIASEC, hybridScores);

  // 8. Narasi profil
  const narrative = buildProfileNarrative(topRIASEC, consistencyScore, selectedMBTI);

  // 9. Simpan ke DB (non-blocking)
  saveResultToDatabase(
    selectedMBTI,
    quizScores,
    hybridScores,
    topRIASEC,
    rankedCareers,
    rankedMajors
  );

  // 10. Render halaman hasil
  renderResults({
    quizScores,
    hybridScores,
    sortedRIASEC,
    topRIASEC,
    consistencyScore,
    rankedCareers,
    rankedMajors,
    narrative
  });

  showPage(5);
}


/* ========================================
   SECTION 7 — RENDER RESULTS PAGE
======================================== */

function renderResults({
  quizScores,
  hybridScores,
  sortedRIASEC,
  topRIASEC,
  consistencyScore,
  rankedCareers,
  rankedMajors,
  narrative
}) {
  const mbti   = MBTI_DATA[selectedMBTI];
  const maxVal = sortedRIASEC[0]?.[1] || 1;

  /* --- RIASEC bars (hybrid score) --- */
  const bars = sortedRIASEC.map(([k, v]) => {
    const pct     = Math.round((v / maxVal) * 100);
    const isTop   = topRIASEC.includes(k);

    return `
      <div class="rbar-row">
        <span class="rbar-lbl" style="${isTop ? "font-weight:800;" : "opacity:0.75;"}">
          ${RIASEC_INFO[k]?.label || k} (${k})
        </span>
        <div class="rbar-bg">
          <div class="rbar-fill" style="width:${pct}%;${isTop ? "" : "opacity:0.5;"}"></div>
        </div>
        <span class="rbar-pct">${pct}%</span>
      </div>
    `;
  }).join("");

  /* --- Consistency badge --- */
  const consistencyColor =
    consistencyScore >= 70 ? "#80d0c7" :
    consistencyScore >= 40 ? "#f0c040" : "#e08060";

  const consistencyLabel =
    consistencyScore >= 70 ? "Sangat Selaras" :
    consistencyScore >= 40 ? "Cukup Selaras"  : "Unik / Tidak Tipikal";

  /* --- Result hero card --- */
  document.getElementById("resultHero").innerHTML = `
    <div class="result-hero">
      <div class="badge-mbti">${selectedMBTI} — ${mbti.name}</div>
      <h2>Profil: ${topRIASEC.map(t => RIASEC_INFO[t]?.label || t).join(" · ")}</h2>
      <p>${narrative}</p>

      <div style="
        display:inline-flex;align-items:center;gap:8px;
        background:rgba(255,255,255,0.12);border-radius:100px;
        padding:5px 14px;margin:12px 0 16px;font-size:12px;
      ">
        <span style="
          width:8px;height:8px;border-radius:50%;
          background:${consistencyColor};display:inline-block;
        "></span>
        Keselarasan MBTI × RIASEC: <strong>${consistencyLabel} (${consistencyScore}%)</strong>
      </div>

      <div class="rbar-wrap">${bars}</div>
    </div>
  `;

  /* --- Recommendation cards (jurusan + karir) --- */
  const topMajors  = rankedMajors.slice(0, 8);
  const topCareers = rankedCareers.slice(0, 8);

  document.getElementById("recGrid").innerHTML = `
    <div class="rec-card">
      <h4>🎓 Jurusan Kuliah</h4>
      ${topMajors.map(m =>
        `<span class="rec-tag" title="Relevansi: ${Math.round(m.score * 100)}%">${m.name}</span>`
      ).join("")}
    </div>
    <div class="rec-card">
      <h4>💼 Jalur Karir</h4>
      ${topCareers.map(c =>
        `<span class="rec-tag" title="Relevansi: ${Math.round(c.score * 100)}%">${c.name}</span>`
      ).join("")}
    </div>
  `;

  /* --- Detail breakdown section --- */
  const detailEl = document.getElementById("topCareers");
  if (detailEl) {
    detailEl.innerHTML = `
      <div class="card" style="margin-top:0;">
        <div class="card-title" style="font-size:17px;">Detail Profil Minat Bakat</div>

        <div class="sec-head">Bagaimana Rekomendasi Dihitung (v3.0)</div>
        <div class="highlight-card" style="margin-bottom:16px;">
          Skor akhir menggunakan metode <strong>Double Confirmation</strong>:
          hasil tes minat bakat RIASEC (bobot <strong>${Math.round(WEIGHT_QUIZ*100)}%</strong>)
          + profil RIASEC kepribadian MBTI ${selectedMBTI} (bobot <strong>${Math.round(WEIGHT_MBTI*100)}%</strong>).
          Karir & jurusan yang muncul di <em>kedua</em> sumber mendapat bonus tambahan,
          sehingga rekomendasi yang paling tepat naik ke atas secara otomatis.
        </div>

        <div class="sec-head">Skor RIASEC Kamu vs Tipe MBTI ${selectedMBTI}</div>
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:13px;font-family:var(--font-title);">
            <thead>
              <tr style="border-bottom:2px solid #e0f0f8;">
                <th style="text-align:left;padding:8px 10px;color:var(--primary);">Tipe RIASEC</th>
                <th style="text-align:center;padding:8px 10px;color:var(--primary);">Quiz Kamu</th>
                <th style="text-align:center;padding:8px 10px;color:var(--primary);">Profil ${selectedMBTI}</th>
                <th style="text-align:center;padding:8px 10px;color:var(--primary);">Skor Hybrid</th>
              </tr>
            </thead>
            <tbody>
              ${sortedRIASEC.map(([k, hybrid]) => {
                const isTop    = topRIASEC.includes(k);
                const quizPct  = Math.round(quizScores[k] * 100);
                const mbtiVec  = getMBTIRIASECVector(selectedMBTI);
                const mbtiPct  = Math.round(mbtiVec[k] * 100);
                const hybPct   = Math.round(hybrid * 100);

                return `
                  <tr style="
                    border-bottom:1px solid #f0f8ff;
                    ${isTop ? "background:#f0f8ff;" : ""}
                  ">
                    <td style="padding:8px 10px;">
                      ${isTop ? "★ " : ""}<strong>${k}</strong> — ${RIASEC_INFO[k]?.label || k}
                    </td>
                    <td style="text-align:center;padding:8px 10px;">${quizPct}%</td>
                    <td style="text-align:center;padding:8px 10px;">${mbtiPct}%</td>
                    <td style="text-align:center;padding:8px 10px;
                      ${isTop ? "color:var(--primary);font-weight:700;" : "color:var(--p-color);"}
                    ">${hybPct}%</td>
                  </tr>
                `;
              }).join("")}
            </tbody>
          </table>
        </div>

        <div class="sec-head" style="margin-top:20px;">Top 5 Karir Paling Cocok</div>
        <div class="career-grid">
          ${rankedCareers.slice(0, 5).map((c, idx) => `
            <div class="career-card">
              <div class="career-num">${idx + 1}</div>
              <div class="career-name">${c.name}</div>
            </div>
          `).join("")}
        </div>
      </div>
    `;
  }

  /* --- Action buttons --- */
  const resetArea = document.querySelector("#page5 [style*='margin-top:24px']");
  if (resetArea) {
    resetArea.innerHTML = `
      <button class="reset-btn" onclick="resetApp()">↺ Mulai ulang dari awal</button>
      <a href="student-home.php" class="reset-btn" style="color:var(--primary);text-decoration:none;">
        🏠 Kembali ke Home
      </a>
    `;
  }
}


/* ========================================
   SECTION 8 — SAVE TO DATABASE v3.0
======================================== */

/**
 * Menyimpan hasil ke DB.
 * Baru di v3.0: juga mengirim recommended_careers.
 * Server mengembalikan saved_at (timestamp) yang ditampilkan di UI.
 */
async function saveResultToDatabase(
  mbti_code,
  quizScores,
  hybridScores,
  topRIASEC,
  rankedCareers,
  rankedMajors
) {
  const recommended_major   = rankedMajors
    .slice(0, 8)
    .map(m => m.name)
    .join(", ");

  const recommended_careers = rankedCareers
    .slice(0, 8)
    .map(c => c.name)
    .join(", ");

  const payload = {
    mbti:                 mbti_code,
    top_riasec:           topRIASEC.join(", "),
    riasec_r:             parseFloat((hybridScores.R * 5).toFixed(2)),
    riasec_i:             parseFloat((hybridScores.I * 5).toFixed(2)),
    riasec_a:             parseFloat((hybridScores.A * 5).toFixed(2)),
    riasec_s:             parseFloat((hybridScores.S * 5).toFixed(2)),
    riasec_e:             parseFloat((hybridScores.E * 5).toFixed(2)),
    riasec_c:             parseFloat((hybridScores.C * 5).toFixed(2)),
    recommended_major,
    recommended_careers
  };

  try {
    const res    = await fetch("save-result.php", {
      method:  "POST",
      headers: { "Content-Type": "application/json" },
      body:    JSON.stringify(payload)
    });
    const result = await res.json();

    if (result.success) {
      console.log("✓ Hasil berhasil disimpan. ID:", result.result_id, "| Waktu:", result.saved_at);

      // Tampilkan konfirmasi tanggal & waktu di UI hasil
      showSavedTimestamp(result.saved_at);
    } else {
      console.error("✗ Error menyimpan hasil:", result.message);
    }
  } catch (err) {
    console.error("✗ Gagal mengirim data:", err);
  }
}

/**
 * Tampilkan badge "tersimpan pada [tanggal]" di halaman hasil.
 */
function showSavedTimestamp(savedAt) {
  if (!savedAt) return;

  const date    = new Date(savedAt);
  const options = {
    day:    "numeric",
    month:  "long",
    year:   "numeric",
    hour:   "2-digit",
    minute: "2-digit",
    timeZone: "Asia/Makassar"  // WITA — sesuaikan jika berbeda
  };
  const formatted = date.toLocaleDateString("id-ID", options);

  // Sisipkan badge di bawah result-hero
  const hero = document.getElementById("resultHero");
  if (!hero) return;

  const badge = document.createElement("div");
  badge.style.cssText = `
    text-align:center;
    font-size:12px;
    color:#80d0c7;
    margin: -8px 0 16px;
    opacity: 0.85;
  `;
  badge.innerHTML = `✓ Hasil tersimpan ke dashboard · ${formatted}`;
  hero.insertAdjacentElement("afterend", badge);
}


/* ========================================
   SECTION 9 — RESET
======================================== */

function resetApp() {
  selectedMBTI = null;
  answers      = {};

  document.querySelectorAll(".mbti-card")
    .forEach(el => el.className = "mbti-card");

  document.querySelectorAll(".scale-btn")
    .forEach(el => el.className = "scale-btn");

  document.getElementById("mbtiNext")?.classList.add("disabled");
  document.getElementById("riasecSubmit")?.classList.add("disabled");

  showPage(1);
}


/* ========================================
   SECTION 10 — BOOTSTRAP
======================================== */

document.addEventListener("DOMContentLoaded", async () => {
  await loadQuizData();

  // Setup modal close on overlay click
  const overlay = document.getElementById("mbtiModalOverlay");
  if (overlay) {
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) {
        closeMBTIModal();
      }
    });
  }
});