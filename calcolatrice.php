<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Calcolatrice Sinx</title>
  <style>
    /* === Overlay === */
    .calc-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }

    /* === Modale === */
    .calc-modal {
      background: linear-gradient(#778dff, #233592);
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.4);
      width: 280px;
      padding: 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    /* Pulsante chiusura */
    .calc-close {
      position: absolute;
      top: 5px;
      right: 10px;
      color: white;
      font-size: 1.2rem;
      cursor: pointer;
    }

    /* === Display === */
    .calc-display {
      background: #000;
      color: #00ff66;
      font-family: 'Courier New', monospace;
      font-size: 1.8rem;
      text-align: right;
      padding: 10px;
      border-radius: 8px;
      width: 100%;
      box-shadow: inset 0 0 5px #111;
      margin-bottom: 10px;
      overflow-x: auto;
    }

    /* === Pulsanti === */
    .calc-buttons {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      grid-gap: 8px;
      width: 100%;
    }

    .calc-buttons button {
      padding: 14px;
      font-size: 1.2rem;
      border: none;
      border-radius: 8px;
      background: #e8e8e8;
      box-shadow: 0 2px 3px rgba(0,0,0,0.3);
      cursor: pointer;
      font-weight: bold;
      color: #233592;
      transition: transform 0.1s, background 0.2s;
    }

    .calc-buttons button:hover {
      background: #dcdcdc;
      transform: scale(1.05);
    }

    .btn-func {
      background: #4c5cff;
      color: white;
    }

    .btn-func:hover {
      background: #778dff;
    }

    .btn-eq {
      background: #00c853;
      color: white;
      grid-row: span 2;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .btn-eq:hover {
      background: #00e676;
    }

    .btn-zero {
      grid-column: span 2;
    }

    /* === Bottone di apertura === */
    .open-calc {
      padding: 10px 20px;
      background: #233592;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
    }

    .open-calc:hover {
      background: #4c5cff;
    }

    /* === VERSIONE MOBILE === */
@media (max-width: 600px) {
  .calculator {
    width: 90vw;
    padding: 20px;
  }

  .calculator-display {
    height: 70px;
    font-size: 32px;
  }

  .calculator-buttons {
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
  }

  .calculator button {
    height: 70px;
    font-size: 28px;
  }
}
  </style>
</head>
<body>

  <!-- Bottone di apertura -->
 <!-- <center><button class="open-calc" onclick="openCalculator()">🧮 Calcolatrice</button></center> -->



  <!-- Overlay della calcolatrice -->
  <div id="calcOverlay" class="calc-overlay" onclick="closeCalculator(event)">
    <div class="calc-modal" onclick="event.stopPropagation()">

      <div class="calc-display" id="calcDisplay">0</div>

      <div class="calc-buttons">
        <button class="btn-func" onclick="clearCalc()">C</button>
        <button class="btn-func" onclick="backspaceCalc()">⌫</button>
        <button class="btn-func" onclick="appendCalc('/')">÷</button>
        <button class="btn-func" onclick="appendCalc('*')">×</button>

        <button onclick="appendCalc('7')">7</button>
        <button onclick="appendCalc('8')">8</button>
        <button onclick="appendCalc('9')">9</button>
        <button class="btn-func" onclick="appendCalc('-')">−</button>

        <button onclick="appendCalc('4')">4</button>
        <button onclick="appendCalc('5')">5</button>
        <button onclick="appendCalc('6')">6</button>
        <button class="btn-func" onclick="appendCalc('+')">+</button>

        <button onclick="appendCalc('1')">1</button>
        <button onclick="appendCalc('2')">2</button>
        <button onclick="appendCalc('3')">3</button>
        <button class="btn-eq" rowspan="2" onclick="calculateCalc()">=</button>

        <button class="btn-zero" onclick="appendCalc('0')">0</button>
        <button onclick="appendCalc('.')">.</button>
      </div>
    </div>
  </div>

  <script>
    let calcInput = '';

    function openCalculator() {
      document.getElementById('calcOverlay').style.display = 'flex';
      document.getElementById('calcDisplay').textContent = calcInput || '0';
    }

    function closeCalculator(event) {
      if (!event || event.target.id === 'calcOverlay' || event.target.classList.contains('calc-close')) {
        document.getElementById('calcOverlay').style.display = 'none';
      }
    }

    function appendCalc(val) {
      calcInput += val;
      document.getElementById('calcDisplay').textContent = calcInput;
    }

    function clearCalc() {
      calcInput = '';
      document.getElementById('calcDisplay').textContent = '0';
    }

    function backspaceCalc() {
      calcInput = calcInput.slice(0, -1);
      document.getElementById('calcDisplay').textContent = calcInput || '0';
    }

    function calculateCalc() {
      try {
        let result = eval(calcInput);
        if (result === Infinity || isNaN(result)) throw 'Error';
        calcInput = result.toString();
        document.getElementById('calcDisplay').textContent = calcInput;
      } catch {
        document.getElementById('calcDisplay').textContent = 'Errore';
        calcInput = '';
      }
    }

    // Supporto tastiera
    document.addEventListener('keydown', (e) => {
      if (document.getElementById('calcOverlay').style.display !== 'flex') return;
      const key = e.key;
      if (/^[0-9+\-*/.]$/.test(key)) appendCalc(key);
      else if (key === 'Enter') calculateCalc();
      else if (key === 'Backspace') backspaceCalc();
      else if (key === 'Escape') closeCalculator(e);
    });

         // Apri automaticamente la calcolatrice all'apertura della pagina
window.addEventListener('load', openCalculator);
  </script>
</body>
</html>
