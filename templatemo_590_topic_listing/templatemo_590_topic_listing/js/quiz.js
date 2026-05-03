/* =========================================
   KarirMatch — Application Logic
   ========================================= */

/* ----------------------------------------
   DATABASE: MBTI
---------------------------------------- */
const MBTI_DATA = {
  INTJ: {
    name: "Arsitek",
    desc: "Strategis, mandiri, dan logis",
    fields: ["Teknologi", "Riset", "Keuangan", "Hukum", "Arsitektur"],
    careers: ["Software Engineer", "Data Scientist", "Analis Keuangan", "Pengacara", "Arsitek", "Peneliti", "Product Manager"],
    riasec: ["I", "C", "E"],
  },
  INTP: {
    name: "Logisi",
    desc: "Analitis, kreatif, dan objektif",
    fields: ["Teknologi", "Sains", "Pendidikan", "Matematika"],
    careers: ["Programmer", "Matematikawan", "Dosen", "Peneliti AI", "Analis Sistem", "Ilmuwan Data"],
    riasec: ["I", "A", "C"],
  },
  ENTJ: {
    name: "Komandan",
    desc: "Tegas, ambisius, dan pemimpin",
    fields: ["Bisnis", "Hukum", "Keuangan", "Manajemen"],
    careers: ["CEO", "Pengacara", "Manajer Proyek", "Konsultan", "Entrepreneur", "Direktur"],
    riasec: ["E", "C", "I"],
  },
  ENTP: {
    name: "Debater",
    desc: "Inovatif, energetik, dan argumentatif",
    fields: ["Bisnis", "Teknologi", "Hukum", "Jurnalisme"],
    careers: ["Entrepreneur", "Pengacara", "Konsultan Inovasi", "Jurnalis", "Pengembang Produk", "Marketer"],
    riasec: ["E", "I", "A"],
  },
  INFJ: {
    name: "Advokat",
    desc: "Idealis, empati, dan visioner",
    fields: ["Psikologi", "Pendidikan", "Seni", "Sosial"],
    careers: ["Psikolog", "Konselor", "Penulis", "Guru", "Pekerja Sosial", "HR Manager"],
    riasec: ["S", "I", "A"],
  },
  INFP: {
    name: "Mediator",
    desc: "Empatik, kreatif, dan idealistis",
    fields: ["Seni", "Sastra", "Psikologi", "Sosial"],
    careers: ["Penulis", "Desainer", "Psikolog", "Guru", "Jurnalis", "Seniman"],
    riasec: ["A", "S", "I"],
  },
  ENFJ: {
    name: "Protagonis",
    desc: "Karismatik, inspiratif, dan pemimpin",
    fields: ["Pendidikan", "Psikologi", "Komunikasi", "Sosial"],
    careers: ["Guru", "Konselor", "HR Manager", "Public Relations", "Diplomat"],
    riasec: ["S", "E", "A"],
  },
  ENFP: {
    name: "Juru Kampanye",
    desc: "Antusias, kreatif, dan sosial",
    fields: ["Komunikasi", "Seni", "Bisnis", "Psikologi"],
    careers: ["Jurnalis", "PR Specialist", "Guru", "Konselor", "Desainer", "Marketer"],
    riasec: ["A", "S", "E"],
  },
  ISTJ: {
    name: "Logisi",
    desc: "Bertanggung jawab, teratur, dan loyal",
    fields: ["Akuntansi", "Hukum", "Militer", "Administrasi", "IT"],
    careers: ["Akuntan", "Manajer Keuangan", "Auditor", "Manajer Proyek", "Analis Sistem"],
    riasec: ["C", "R", "I"],
  },
  ISFJ: {
    name: "Pembela",
    desc: "Peduli, teliti, dan setia",
    fields: ["Kesehatan", "Pendidikan", "Administrasi", "Sosial"],
    careers: ["Perawat", "Dokter", "Guru", "Apoteker", "Konselor"],
    riasec: ["S", "C", "R"],
  },
  ESTJ: {
    name: "Eksekutif",
    desc: "Terorganisir, tegas, dan efisien",
    fields: ["Manajemen", "Hukum", "Bisnis", "Militer"],
    careers: ["Manajer", "Pengacara", "Direktur", "Financial Planner", "Pegawai Pemerintah"],
    riasec: ["E", "C", "R"],
  },
  ESFJ: {
    name: "Konsul",
    desc: "Peduli, sosial, dan populer",
    fields: ["Kesehatan", "Pendidikan", "Sosial", "Hospitality"],
    careers: ["Perawat", "Guru", "HR Manager", "Event Organizer", "Sales Manager"],
    riasec: ["S", "E", "C"],
  },
  ISTP: {
    name: "Virtuoso",
    desc: "Praktis, analitis, dan tenang",
    fields: ["Teknik", "Teknologi", "Mekanik", "Sains"],
    careers: ["Engineer Mesin", "Teknisi", "Pilot", "Programmer", "Mekanik"],
    riasec: ["R", "I", "C"],
  },
  ISFP: {
    name: "Petualang",
    desc: "Fleksibel, ramah, dan artistik",
    fields: ["Seni", "Desain", "Kesehatan", "Kuliner"],
    careers: ["Desainer", "Seniman", "Chef", "Perawat", "Fotografer", "Fashion Designer"],
    riasec: ["A", "R", "S"],
  },
  ESTP: {
    name: "Pengusaha",
    desc: "Energetik, adaptif, dan praktis",
    fields: ["Bisnis", "Olahraga", "Pariwisata", "Sales"],
    careers: ["Sales Manager", "Entrepreneur", "Marketer", "Event Organizer", "Broker"],
    riasec: ["E", "R", "S"],
  },
  ESFP: {
    name: "Penghibur",
    desc: "Spontan, energik, dan sosial",
    fields: ["Seni", "Hiburan", "Hospitality", "Olahraga"],
    careers: ["Aktor", "MC", "Event Organizer", "Guru SD", "Entertainer"],
    riasec: ["S", "A", "E"],
  },
};

/* ----------------------------------------
   DATABASE: RIASEC QUESTIONS
---------------------------------------- */
const RIASEC_Q = [
  { q: "Saya senang memperbaiki atau membuat sesuatu secara fisik (mesin, bangunan, kerajinan tangan)", t: "R" },
  { q: "Saya suka bekerja di luar ruangan dan menggunakan alat atau mesin", t: "R" },
  { q: "Saya senang menganalisis data, memecahkan masalah matematika, atau bereksperimen", t: "I" },
  { q: "Saya tertarik mempelajari konsep ilmiah dan melakukan penelitian", t: "I" },
  { q: "Saya senang menggambar, menulis, bermain musik, atau berekspresi kreatif", t: "A" },
  { q: "Saya suka pekerjaan yang tidak memiliki aturan ketat dan memberikan kebebasan berkreasi", t: "A" },
  { q: "Saya senang membantu orang lain, mengajar, atau memberikan dukungan emosional", t: "S" },
  { q: "Saya peduli dengan kesejahteraan orang lain dan senang bekerja dalam tim", t: "S" },
  { q: "Saya senang memimpin, membujuk, atau memengaruhi orang lain untuk mencapai tujuan", t: "E" },
  { q: "Saya tertarik pada bisnis, politik, atau posisi yang memiliki pengaruh dan kekuasaan", t: "E" },
  { q: "Saya senang pekerjaan yang teratur, sistematis, dan mengikuti prosedur yang jelas", t: "C" },
  { q: "Saya teliti dalam mengorganisir data, dokumen, atau hal-hal yang berhubungan dengan angka", t: "C" },
];

/* ----------------------------------------
   DATABASE: RIASEC INFO
---------------------------------------- */
const RIASEC_INFO = {
  R: {
    label: "Realistis",
    careers: ["Insinyur Sipil", "Teknisi Mesin", "Pilot", "Petani Modern", "Mekanik", "Operator CNC"],
  },
  I: {
    label: "Investigatif",
    careers: ["Data Scientist", "Peneliti", "Dokter", "Ilmuwan", "Programmer", "Ahli Bioteknologi"],
  },
  A: {
    label: "Artistik",
    careers: ["Desainer Grafis", "Penulis", "Arsitek", "Seniman", "Fotografer", "UI/UX Designer"],
  },
  S: {
    label: "Sosial",
    careers: ["Psikolog", "Guru", "Perawat", "Konselor", "HR Manager", "Pekerja Sosial"],
  },
  E: {
    label: "Enterprising",
    careers: ["Entrepreneur", "Manajer Bisnis", "Pengacara", "Sales Director", "Marketing Manager", "CEO"],
  },
  C: {
    label: "Konvensional",
    careers: ["Akuntan", "Auditor", "Analis Data", "Banker", "Manajer Operasional", "Notaris"],
  },
};

/* ----------------------------------------
   DATABASE: JURUSAN PER RIASEC
---------------------------------------- */
const JURUSAN = {
  R: ["Teknik Mesin", "Teknik Sipil", "Teknik Elektro", "Pertanian", "Kehutanan", "Teknik Penerbangan"],
  I: ["Matematika", "Fisika", "Informatika", "Statistika", "Kedokteran", "Bioteknologi"],
  A: ["Desain Komunikasi Visual", "Seni Rupa", "Arsitektur", "Sastra", "Film & TV", "Fotografi"],
  S: ["Psikologi", "Pendidikan", "Sosiologi", "Keperawatan", "Ilmu Komunikasi", "Pekerjaan Sosial"],
  E: ["Manajemen", "Bisnis Internasional", "Hukum", "Ilmu Politik", "Ekonomi", "Marketing"],
  C: ["Akuntansi", "Keuangan", "Administrasi Bisnis", "Sistem Informasi", "Perpajakan"],
};

/* ----------------------------------------
   STATE
---------------------------------------- */
let selectedMBTI = null;
let answers = {};

/* ----------------------------------------
   INIT: Build MBTI Grid & RIASEC Quiz
---------------------------------------- */
function init() {
  buildMBTIGrid();
  buildRIASECQuiz();
}

function buildMBTIGrid() {
  const grid = document.getElementById("mbtiGrid");
  Object.keys(MBTI_DATA).forEach((type) => {
    const d = MBTI_DATA[type];
    const el = document.createElement("div");
    el.className = "mbti-card";
    el.id = "mc_" + type;
    el.innerHTML = `
      <div class="mbti-type">${type}</div>
      <div class="mbti-name">${d.name}</div>
    `;
    el.addEventListener("click", () => pickMBTI(type));
    grid.appendChild(el);
  });
}

function buildRIASECQuiz() {
  const wrap = document.getElementById("riasecQuestions");
  RIASEC_Q.forEach((q, i) => {
    const div = document.createElement("div");
    div.className = "riasec-q";
    div.innerHTML = `
      <div class="q-text">${i + 1}. ${q.q}</div>
      <div class="scale-wrap">
        <span class="scale-lbl">Tidak suka</span>
        <div class="scale-btns" id="sb_${i}">
          ${[1, 2, 3, 4, 5]
            .map(
              (v) =>
                `<button class="scale-btn" data-qi="${i}" data-val="${v}">${v}</button>`
            )
            .join("")}
        </div>
        <span class="scale-lbl r">Sangat suka</span>
      </div>
    `;
    wrap.appendChild(div);
  });

  // Delegasi event untuk semua tombol skala
  document.getElementById("riasecQuestions").addEventListener("click", (e) => {
    const btn = e.target.closest(".scale-btn");
    if (!btn) return;
    const qi = parseInt(btn.dataset.qi);
    const val = parseInt(btn.dataset.val);
    pickScale(qi, val, btn);
  });
}

/* ----------------------------------------
   INTERACTIONS
---------------------------------------- */
function pickMBTI(type) {
  // Hapus seleksi lama
  if (selectedMBTI) {
    const old = document.getElementById("mc_" + selectedMBTI);
    if (old) old.className = "mbti-card";
  }
  selectedMBTI = type;
  document.getElementById("mc_" + type).className = "mbti-card selected";

  // Aktifkan tombol Lanjutkan
  const btn = document.getElementById("mbtiNext");
  btn.classList.remove("disabled");
}

function pickScale(qi, val, btn) {
  answers[qi] = { val, t: RIASEC_Q[qi].t };

  // Reset semua tombol di baris ini, lalu tandai yang dipilih
  document
    .querySelectorAll(`#sb_${qi} .scale-btn`)
    .forEach((b) => (b.className = "scale-btn"));
  btn.className = "scale-btn sel";

  // Aktifkan tombol submit jika semua pertanyaan sudah dijawab
  if (Object.keys(answers).length === RIASEC_Q.length) {
    const submit = document.getElementById("riasecSubmit");
    submit.classList.remove("disabled");
  }
}

/* ----------------------------------------
   PAGE NAVIGATION
---------------------------------------- */
function showPage(n) {
  [1, 2, 3, 4, 5].forEach((i) =>
    document.getElementById("page" + i).classList.add("hidden")
  );
  document.getElementById("page" + n).classList.remove("hidden");
  updateProgress(n);
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function updateProgress(page) {
  const stepMap = { 1: 1, 2: 1, 3: 2, 4: 3, 5: 4 };
  const active = stepMap[page] || 1;

  [1, 2, 3, 4].forEach((i) => {
    const dot = document.getElementById("dot" + i);
    const lbl = document.getElementById("lbl" + i);

    if (i < active) {
      dot.className = "step-circle done";
      dot.textContent = "✓";
    } else if (i === active) {
      dot.className = "step-circle active";
      dot.textContent = i;
    } else {
      dot.className = "step-circle";
      dot.textContent = i;
    }

    lbl.className = i === active ? "step-label active" : "step-label";

    if (i < 4) {
      document.getElementById("line" + i).className =
        i < active ? "step-line done" : "step-line";
    }
  });
}

/* ----------------------------------------
   FILTER PREVIEW (Page 3)
---------------------------------------- */
function goToFilter() {
  if (!selectedMBTI) return;
  const d = MBTI_DATA[selectedMBTI];

  document.getElementById("filterTitle").textContent =
    `Filter MBTI: ${selectedMBTI} — ${d.name}`;

  document.getElementById("matchAlert").innerHTML =
    `<strong>${selectedMBTI} (${d.name})</strong> — ${d.desc}.
     Profil RIASEC yang biasanya cocok:
     <strong>${d.riasec.map((r) => RIASEC_INFO[r].label).join(", ")}</strong>.`;

  document.getElementById("filterFields").innerHTML = d.fields
    .map((f) => `<span class="ftag">${f}</span>`)
    .join("");

  document.getElementById("filterCareers").innerHTML = d.careers
    .map((c) => `<span class="ftag">${c}</span>`)
    .join("");

  showPage(3);
}

/* ----------------------------------------
   CALCULATE & RENDER RESULTS (Page 5)
---------------------------------------- */
function calcResults() {
  // Hitung rata-rata skor per dimensi RIASEC
  const scores = { R: 0, I: 0, A: 0, S: 0, E: 0, C: 0 };
  const counts = { R: 0, I: 0, A: 0, S: 0, E: 0, C: 0 };

  Object.values(answers).forEach((a) => {
    scores[a.t] += a.val;
    counts[a.t]++;
  });

  Object.keys(scores).forEach((k) => {
    if (counts[k] > 0) scores[k] = scores[k] / counts[k];
  });

  const sorted = Object.entries(scores).sort((a, b) => b[1] - a[1]);
  const top3 = sorted.slice(0, 3).map((e) => e[0]);
  const mbti = MBTI_DATA[selectedMBTI];

  // Gabungkan top RIASEC dari quiz + saran dari MBTI (tanpa duplikat)
  const combined = [...new Set([...top3, ...mbti.riasec])].slice(0, 3);

  renderResults(scores, sorted, top3, combined);
  showPage(5);
}

function renderResults(scores, sorted, top3, combined) {
  const mbti = MBTI_DATA[selectedMBTI];
  const max = sorted[0][1] || 1;

  // Bar chart RIASEC
  const bars = sorted
    .map(
      ([k, v]) => `
      <div class="rbar-row">
        <span class="rbar-lbl">${RIASEC_INFO[k].label} (${k})</span>
        <div class="rbar-bg">
          <div class="rbar-fill" style="width: ${Math.round((v / max) * 100)}%"></div>
        </div>
        <span class="rbar-pct">${Math.round(v * 20)}%</span>
      </div>`
    )
    .join("");

  // Result hero
  document.getElementById("resultHero").innerHTML = `
    <div class="result-hero">
      <div class="badge-mbti">${selectedMBTI} — ${mbti.name}</div>
      <h2>Profil Minat: ${top3.map((t) => RIASEC_INFO[t].label).join(" · ")}</h2>
      <p>
        ${mbti.desc}. Berdasarkan kombinasi kepribadian dan minat bakatmu,
        kamu punya potensi kuat di bidang
        ${combined.map((c) => RIASEC_INFO[c].label.toLowerCase()).join(", ")}.
      </p>
      <div class="rbar-wrap">${bars}</div>
    </div>`;

  // Jurusan & jalur karir
  const jurusan = [...new Set(combined.flatMap((c) => JURUSAN[c]))].slice(0, 8);
  const careerPool = [
    ...new Set(combined.flatMap((c) => RIASEC_INFO[c].careers)),
  ].slice(0, 8);

  document.getElementById("recGrid").innerHTML = `
    <div class="rec-card">
      <h4>Jurusan Kuliah</h4>
      ${jurusan.map((j) => `<span class="rec-tag">${j}</span>`).join("")}
    </div>
    <div class="rec-card">
      <h4>Jalur Karir</h4>
      ${careerPool.map((c) => `<span class="rec-tag">${c}</span>`).join("")}
    </div>`;

  // Top 6 karir
  const topCareers = combined
    .flatMap((c) => RIASEC_INFO[c].careers.slice(0, 3))
    .filter((v, i, a) => a.indexOf(v) === i)
    .slice(0, 6);

  document.getElementById("topCareers").innerHTML = `
    <div class="rec-card">
      <h4>Top Rekomendasi Karir untuk ${selectedMBTI}</h4>
      <div class="career-grid">
        ${topCareers
          .map(
            (c, i) => `
          <div class="career-card">
            <div class="career-num">${i + 1}</div>
            <div class="career-name">${c}</div>
          </div>`
          )
          .join("")}
      </div>
    </div>`;
}

/* ----------------------------------------
   RESET
---------------------------------------- */
function resetApp() {
  selectedMBTI = null;
  answers = {};

  document
    .querySelectorAll(".mbti-card")
    .forEach((el) => (el.className = "mbti-card"));
  document
    .querySelectorAll(".scale-btn")
    .forEach((el) => (el.className = "scale-btn"));

  document.getElementById("mbtiNext").classList.add("disabled");
  document.getElementById("riasecSubmit").classList.add("disabled");

  showPage(1);
}

/* ----------------------------------------
   START
---------------------------------------- */
document.addEventListener("DOMContentLoaded", init);
