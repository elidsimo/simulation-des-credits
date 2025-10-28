<?php include 'check_session.php'; ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulateur de Crédit</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.8);
            --shadow-light: 0 8px 32px rgba(0, 0, 0, 0.1);
            --shadow-heavy: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            padding: 2rem 1rem;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Floating elements background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(102, 126, 234, 0.1) 0%, transparent 50%);
            animation: floating 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes floating {

            0%,
            100% {
                transform: rotate(0deg) translateY(0px);
            }

            50% {
                transform: rotate(180deg) translateY(-20px);
            }
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: var(--shadow-heavy);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent-gradient);
            border-radius: 24px 24px 0 0;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .header p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 400;
        }

        .header .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--glass-bg);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-top: 1rem;
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-container {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--accent-gradient);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .input-container:focus-within::before {
            left: 0;
            opacity: 0.1;
        }

        .input-container:focus-within {
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.1);
            transform: translateY(-2px);
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 1rem 1.25rem;
            border: none;
            background: transparent;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 500;
            outline: none;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
            font-weight: 400;
        }

        .form-select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23ffffff'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.5rem;
            padding-right: 3rem;
        }

        .form-select option {
            background: #1e3c72;
            color: white;
            padding: 0.5rem;
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            z-index: 3;
            pointer-events: none;
        }

        .submit-btn {
            width: 100%;
            padding: 1.25rem 2rem;
            background: var(--primary-gradient);
            border: none;
            border-radius: 16px;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: var(--shadow-light);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--secondary-gradient);
            transition: left 0.5s ease;
            z-index: 1;
        }

        .submit-btn:hover::before {
            left: 0;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .submit-btn span {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .results-card {
            display: none;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: var(--shadow-light);
            position: relative;
            overflow: hidden;
        }

        .results-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--secondary-gradient);
            border-radius: 20px 20px 0 0;
        }

        .results-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .results-grid {
            display: grid;
            gap: 1rem;
        }

        .result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .result-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .result-label {
            font-weight: 600;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .result-value {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--text-primary);
        }

        .result-item.highlight {
            background: rgba(245, 87, 108, 0.15);
            border-color: rgba(245, 87, 108, 0.3);
        }

        .result-item.highlight .result-value {
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        footer {
            margin-top: 3rem;
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .results-grid {
                gap: 0.75rem;
            }

            .result-item {
                padding: 1rem;
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .result-value {
                font-size: 1.4rem;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calculator"></i> CreditSim </h1>
            <p>Simulateur de crédit intelligent et moderne</p>
            <div class="welcome-badge">
                <i class="fas fa-user-circle"></i>
                <span>Bienvenue <?php echo htmlspecialchars($user_name); ?></span>
            </div>
        </div>

        <form id="simulatorForm">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">
                        <i class="fas fa-tags"></i> Type de crédit
                    </label>
                    <div class="input-container">
                        <select id="creditType" class="form-select" required>
                            <option value="">Sélectionnez un type de crédit</option>
                            <option value="immobilier">🏠 Crédit immobilier</option>
                            <option value="auto">🚗 Crédit automobile</option>
                            <option value="personnel">👤 Crédit personnel</option>
                            <option value="travaux">🔨 Crédit travaux</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-money-bill-wave"></i> Montant souhaité
                    </label>
                    <div class="input-container">
                        <input type="number" id="amount" class="form-input" placeholder="200 000" required min="1000">
                        <div class="input-icon">MAD</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-clock"></i> Durée
                    </label>
                    <div class="input-container">
                        <select id="duration" class="form-select" required>
                            <option value="">Choisir une durée</option>
                            <option value="5">5 ans</option>
                            <option value="7">7 ans</option>
                            <option value="10">10 ans</option>
                            <option value="15">15 ans</option>
                            <option value="20">20 ans</option>
                            <option value="25">25 ans</option>
                            <option value="30">30 ans</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-percentage"></i> Taux d'intérêt
                    </label>
                    <div class="input-container">
                        <input type="number" id="rate" class="form-input" placeholder="4.50" step="0.01" min="0.1"
                            max="15" required>
                        <div class="input-icon">%</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-piggy-bank"></i> Apport personnel
                    </label>
                    <div class="input-container">
                        <input type="number" id="downPayment" class="form-input" placeholder="50 000" min="0">
                        <div class="input-icon">MAD</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <span>
                    <i class="fas fa-magic"></i>
                    Calculer ma simulation
                </span>
            </button>
        </form>

        <div class="results-card" id="results">
            <h3 class="results-title">
                <i class="fas fa-chart-line"></i>
                Résultats de votre simulation
            </h3>
            <div class="results-grid">
                <div class="result-item">
                    <div class="result-label">
                        <i class="fas fa-calendar-alt"></i>
                        Mensualité
                    </div>
                    <div class="result-value" id="monthlyPayment">-</div>
                </div>
                <div class="result-item">
                    <div class="result-label">
                        <i class="fas fa-coins"></i>
                        Coût total
                    </div>
                    <div class="result-value" id="totalCost">-</div>
                </div>
                <div class="result-item highlight">
                    <div class="result-label">
                        <i class="fas fa-chart-pie"></i>
                        Intérêts totaux
                    </div>
                    <div class="result-value" id="totalInterest">-</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('simulatorForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Ajouter animation de loading
            const submitBtn = this.querySelector('.submit-btn');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span><div class="loading"></div> Calcul en cours...</span>';
            submitBtn.disabled = true;

            const type_credit = document.getElementById('creditType').value;
            const amount = parseFloat(document.getElementById('amount').value);
            const rate = parseFloat(document.getElementById('rate').value) / 100;
            const duration = parseInt(document.getElementById('duration').value) * 12;
            const downPayment = parseFloat(document.getElementById('downPayment').value) || 0;

            const loanAmount = amount - downPayment;
            if (loanAmount <= 0) {
                alert('Le montant à financer doit être supérieur à l\'apport.');
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
                return;
            }

            // Simulation du délai de calcul
            setTimeout(() => {
                const monthlyRate = rate / 12;
                const monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, duration)) / (Math.pow(1 + monthlyRate, duration) - 1);
                const totalCost = monthlyPayment * duration;
                const totalInterest = totalCost - loanAmount;

                document.getElementById('monthlyPayment').textContent = monthlyPayment.toLocaleString('fr-MA', { style: 'currency', currency: 'MAD' });
                document.getElementById('totalCost').textContent = totalCost.toLocaleString('fr-MA', { style: 'currency', currency: 'MAD' });
                document.getElementById('totalInterest').textContent = totalInterest.toLocaleString('fr-MA', { style: 'currency', currency: 'MAD' });

                document.getElementById('results').style.display = 'block';
                document.getElementById('results').scrollIntoView({ behavior: 'smooth', block: 'start' });

                // Restaurer le bouton
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;

                // Envoi des données vers PHP pour sauvegarde
                fetch('save_simulation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        type_credit: type_credit,
                        montant: amount,
                        duree: duration / 12,
                        taux: rate * 100,
                        mensualite: monthlyPayment,
                        total_rembourse: totalCost
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Animation de succès
                            const resultsCard = document.getElementById('results');
                            resultsCard.style.transform = 'scale(1.02)';
                            setTimeout(() => {
                                resultsCard.style.transform = 'scale(1)';
                            }, 200);
                        } else {
                            console.error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                    });
            }, 1000);
        });

        // Animation au focus des inputs
        document.querySelectorAll('.form-input, .form-select').forEach(input => {
            input.addEventListener('focus', function () {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function () {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>

    <footer>
        <p>&copy; 2025 CreditSim. Simulation moderne et intuitive.</p>
    </footer>
</body>

</html>