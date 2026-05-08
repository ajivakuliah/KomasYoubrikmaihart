/* =========================================
   KarirMatch — Application Logic
   DATABASE VERSION
   ========================================= */

/* ----------------------------------------
   GLOBAL DATA
---------------------------------------- */

let MBTI_DATA = {};
let RIASEC_Q = [];
let RIASEC_INFO = {};
let JURUSAN = {};

/* ----------------------------------------
   STATE
---------------------------------------- */

let selectedMBTI = null;
let answers = {};

/* ----------------------------------------
   INIT
---------------------------------------- */

async function loadQuizData() {

  try {

    const response = await fetch("api/get-quiz-data.php");
    const data = await response.json();

    MBTI_DATA = data.MBTI_DATA || {};
    RIASEC_Q = data.RIASEC_Q || [];
    RIASEC_INFO = data.RIASEC_INFO || {};
    JURUSAN = data.JURUSAN || {};

    init();

  } catch (error) {

    console.error("Gagal load data quiz:", error);

    alert("Gagal mengambil data quiz dari database.");

  }

}

function init() {

  buildMBTIGrid();
  buildRIASECQuiz();

}

/* ----------------------------------------
   BUILD MBTI GRID
---------------------------------------- */

function buildMBTIGrid() {

  const grid = document.getElementById("mbtiGrid");

  if (!grid) return;

  grid.innerHTML = "";

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

/* ----------------------------------------
   BUILD RIASEC QUIZ
---------------------------------------- */

function buildRIASECQuiz() {

  const wrap = document.getElementById("riasecQuestions");

  if (!wrap) return;

  wrap.innerHTML = "";

  RIASEC_Q.forEach((q, i) => {

    const div = document.createElement("div");

    div.className = "riasec-q";

    div.innerHTML = `
      <div class="q-text">
        ${i + 1}. ${q.q}
      </div>

      <div class="scale-wrap">

        <span class="scale-lbl">
          Tidak suka
        </span>

        <div class="scale-btns" id="sb_${i}">

          ${[1,2,3,4,5].map(v => `
            <button
              class="scale-btn"
              data-qi="${i}"
              data-val="${v}">
              ${v}
            </button>
          `).join("")}

        </div>

        <span class="scale-lbl r">
          Sangat suka
        </span>

      </div>
    `;

    wrap.appendChild(div);

  });

  /* EVENT */

  wrap.addEventListener("click", (e) => {

    const btn = e.target.closest(".scale-btn");

    if (!btn) return;

    const qi = parseInt(btn.dataset.qi);
    const val = parseInt(btn.dataset.val);

    pickScale(qi, val, btn);

  });

}

/* ----------------------------------------
   MBTI PICK
---------------------------------------- */

function pickMBTI(type) {

  if (selectedMBTI) {

    const old = document.getElementById("mc_" + selectedMBTI);

    if (old) {
      old.className = "mbti-card";
    }

  }

  selectedMBTI = type;

  document.getElementById("mc_" + type).className =
    "mbti-card selected";

  const btn = document.getElementById("mbtiNext");

  if (btn) {
    btn.classList.remove("disabled");
  }

}

/* ----------------------------------------
   PICK SCALE
---------------------------------------- */

function pickScale(qi, val, btn) {

  answers[qi] = {
    val,
    t: RIASEC_Q[qi].t
  };

  document
    .querySelectorAll(`#sb_${qi} .scale-btn`)
    .forEach((b) => {
      b.className = "scale-btn";
    });

  btn.className = "scale-btn sel";

  if (Object.keys(answers).length === RIASEC_Q.length) {

    const submit =
      document.getElementById("riasecSubmit");

    if (submit) {
      submit.classList.remove("disabled");
    }

  }

}

/* ----------------------------------------
   PAGE NAVIGATION
---------------------------------------- */

function showPage(n) {

  [1,2,3,4,5].forEach((i) => {

    const page = document.getElementById("page" + i);

    if (page) {
      page.classList.add("hidden");
    }

  });

  document
    .getElementById("page" + n)
    .classList.remove("hidden");

  updateProgress(n);

  window.scrollTo({
    top: 0,
    behavior: "smooth"
  });

}

/* ----------------------------------------
   PROGRESS
---------------------------------------- */

function updateProgress(page) {

  const stepMap = {
    1: 1,
    2: 1,
    3: 2,
    4: 3,
    5: 4
  };

  const active = stepMap[page] || 1;

  [1,2,3,4].forEach((i) => {

    const dot =
      document.getElementById("dot" + i);

    const lbl =
      document.getElementById("lbl" + i);

    if (!dot || !lbl) return;

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

    lbl.className =
      i === active
      ? "step-label active"
      : "step-label";

    if (i < 4) {

      const line =
        document.getElementById("line" + i);

      if (line) {

        line.className =
          i < active
          ? "step-line done"
          : "step-line";

      }

    }

  });

}

/* ----------------------------------------
   FILTER PREVIEW
---------------------------------------- */

function goToFilter() {

  if (!selectedMBTI) return;

  const d = MBTI_DATA[selectedMBTI];

  document.getElementById("filterTitle").textContent =
    `Filter MBTI: ${selectedMBTI} — ${d.name}`;

  document.getElementById("matchAlert").innerHTML = `
    <strong>${selectedMBTI} (${d.name})</strong>
    — ${d.desc}.
    Profil RIASEC yang biasanya cocok:
    <strong>
      ${d.riasec.map((r) =>
        RIASEC_INFO[r].label
      ).join(", ")}
    </strong>.
  `;
  
  document.getElementById("filterFields").innerHTML =
  d.fields.map((f) =>
    `<span class="ftag">${f}</span>`
  ).join("");

  document.getElementById("filterCareers").innerHTML =
    d.careers.map((c) =>
      `<span class="ftag">${c}</span>`
    ).join("");


  showPage(3);

}

/* ----------------------------------------
   CALCULATE RESULTS
---------------------------------------- */

function calcResults() {

  const scores = {
    R:0,I:0,A:0,S:0,E:0,C:0
  };

  const counts = {
    R:0,I:0,A:0,S:0,E:0,C:0
  };

  Object.values(answers).forEach((a) => {

    scores[a.t] += a.val;
    counts[a.t]++;

  });

  Object.keys(scores).forEach((k) => {

    if (counts[k] > 0) {
      scores[k] =
        scores[k] / counts[k];
    }

  });

  const sorted =
    Object.entries(scores)
    .sort((a,b) => b[1] - a[1]);

  const top3 =
    sorted.slice(0,3)
    .map((e) => e[0]);

  const mbti =
    MBTI_DATA[selectedMBTI];

  const combined = [
    ...new Set([
      ...top3,
      ...mbti.riasec
    ])
  ].slice(0,3);

  renderResults(
    scores,
    sorted,
    top3,
    combined
  );

  showPage(5);

}

/* ----------------------------------------
   RENDER RESULTS
---------------------------------------- */

function renderResults(
  scores,
  sorted,
  top3,
  combined
) {

  const mbti =
    MBTI_DATA[selectedMBTI];

  const max =
    sorted[0][1] || 1;

  const bars = sorted.map(([k,v]) => `

    <div class="rbar-row">

      <span class="rbar-lbl">
        ${RIASEC_INFO[k].label} (${k})
      </span>

      <div class="rbar-bg">
        <div class="rbar-fill"
          style="width:${Math.round((v/max)*100)}%">
        </div>
      </div>

      <span class="rbar-pct">
        ${Math.round(v*20)}%
      </span>

    </div>

  `).join("");

  document.getElementById("resultHero").innerHTML = `

    <div class="result-hero">

      <div class="badge-mbti">
        ${selectedMBTI} — ${mbti.name}
      </div>

      <h2>
        Profil Minat:
        ${top3.map((t) =>
          RIASEC_INFO[t].label
        ).join(" · ")}
      </h2>

      <p>
        ${mbti.desc}
      </p>

      <div class="rbar-wrap">
        ${bars}
      </div>

    </div>

  `;

  const jurusan = [
    ...new Set(
      combined.flatMap((c) =>
        JURUSAN[c] || []
      )
    )
  ].slice(0,8);

  const careerPool = [
    ...new Set(
      combined.flatMap((c) =>
        RIASEC_INFO[c].careers || []
      )
    )
  ].slice(0,8);

  document.getElementById("recGrid").innerHTML = `

    <div class="rec-card">

      <h4>Jurusan Kuliah</h4>

      ${jurusan.map((j) =>
        `<span class="rec-tag">${j}</span>`
      ).join("")}

    </div>

    <div class="rec-card">

      <h4>Jalur Karir</h4>

      ${careerPool.map((c) =>
        `<span class="rec-tag">${c}</span>`
      ).join("")}

    </div>

  `;

}

/* ----------------------------------------
   RESET
---------------------------------------- */

function resetApp() {

  selectedMBTI = null;

  answers = {};

  document
    .querySelectorAll(".mbti-card")
    .forEach((el) => {
      el.className = "mbti-card";
    });

  document
    .querySelectorAll(".scale-btn")
    .forEach((el) => {
      el.className = "scale-btn";
    });

  document
    .getElementById("mbtiNext")
    ?.classList.add("disabled");

  document
    .getElementById("riasecSubmit")
    ?.classList.add("disabled");

  showPage(1);

}

/* ----------------------------------------
   START
---------------------------------------- */

document.addEventListener("DOMContentLoaded", async () => {

  const res = await fetch("api/get-quiz-data.php");
  const data = await res.json();

  MBTI_DATA = data.MBTI_DATA;
  RIASEC_Q = data.RIASEC_Q;
  RIASEC_INFO = data.RIASEC_INFO;
  JURUSAN = data.JURUSAN;

  init();

});