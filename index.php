<?php include "inc/header.php"; ?>
<?php include "inc/navbar.php"; ?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.container {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
}

/* HERO */
.hero {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
    margin-top: 50px;
    padding: 40px 0;
}
.hero-text { flex: 1; min-width: 300px; }
.hero h1 {
    font-size: 42px;
    margin-bottom: 15px;
    line-height: 1.2;
}
.hero p {
    color: #555;
    font-size: 18px;
    line-height: 1.7;
    margin-bottom: 25px;
}
.hero-img img {
    width: 100%;
    max-width: 480px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

/* SECTION STYLING */
.section {
    margin-top: 80px;
}
.section h2 {
    font-size: 32px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* ICON COLORS */
.icon-blue   { color: #00aaff; }
.icon-green  { color: #22c55e; }
.icon-purple { color: #8b5cf6; }
.icon-orange { color: #f97316; }
.icon-yellow { color: #eab308; }
.icon-red    { color: #ef4444; }

/* CARDS */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}
.card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    transition: transform 0.3s ease;
}
.card:hover {
    transform: translateY(-8px);
}
.card i {
    font-size: 36px;
    margin-bottom: 15px;
    display: block;
}
.card h3 {
    margin: 10px 0 12px;
    font-size: 22px;
}

/* STEPS */
.steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}
.step {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    text-align: center;
}
.step i {
    font-size: 42px;
    margin-bottom: 15px;
}

/* STATS */
.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
    background: linear-gradient(135deg, #f8fafc, #e0f2fe);
    padding: 40px 30px;
    border-radius: 16px;
}
.stat-item {
    text-align: center;
}
.stat-item h3 {
    font-size: 36px;
    margin: 0;
    color: #1e40af;
}
.stat-item p {
    color: #555;
    font-size: 17px;
    margin-top: 5px;
}

/* REVIEWS */
.reviews {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 25px;
}
.review {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
}
.review-stars {
    color: #facc15;
    font-size: 20px;
    margin-bottom: 12px;
}

/* CTA */
.cta {
    text-align: center;
    padding: 60px 40px;
    background: linear-gradient(135deg, #0ea5e9, #3b82f6);
    color: white;
    border-radius: 16px;
    margin-top: 80px;
}
.cta h2 {
    font-size: 36px;
    margin-bottom: 15px;
    color: white;
}
.cta p {
    font-size: 19px;
    opacity: 0.95;
    margin-bottom: 25px;
}

/* BUTTONS */
.btn {
    padding: 14px 28px;
    border: none;
    border-radius: 8px;
    font-size: 17px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}
.btn-primary {
    background: #00aaff;
    color: white;
}
.btn-primary:hover { background: #0088cc; }
.btn-dark {
    background: #1f2937;
    color: white;
}
.btn-dark:hover { background: #111827; }
</style>

<div class="container">

    <!-- HERO SECTION -->
    <div class="hero">
        <div class="hero-text">
            <h1>
                <i class="fa-solid fa-gamepad icon-blue"></i> 
                Play Games & Earn Real Cash
            </h1>
            <p>
                Welcome to the most exciting play-to-earn platform! Enjoy addictive skill-based games, 
                compete with players worldwide, and turn your gaming time into real money. 
                Fast payouts, secure transactions, and non-stop fun await you.
            </p>
            <br>
            <a href="games.php" class="btn btn-primary">
                <i class="fa-solid fa-play"></i> Start Playing Now
            </a>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-dark" style="margin-left:12px;">
                    <i class="fa-solid fa-user-plus"></i> Join Free Today
                </a>
            <?php endif; ?>
        </div>
        <div class="hero-img">
            <img src="assets/images/hero.png" alt="Play and Earn">
        </div>
    </div>

    <!-- HOW IT WORKS -->
    <div class="section">
        <h2><i class="fa-solid fa-gears icon-purple"></i> How It Works - 3 Simple Steps</h2>
        <div class="steps">
            <div class="step">
                <i class="fa-solid fa-gamepad icon-blue"></i>
                <h3>1. Play Games</h3>
                <p>Choose from a wide variety of fun, skill-based games. No downloads required – play directly in your browser.</p>
            </div>
            <div class="step">
                <i class="fa-solid fa-coins icon-yellow"></i>
                <h3>2. Earn Rewards</h3>
                <p>Earn <?php echo $currency; ?> based on your playing time, score, and performance. The better you play, the more you earn.</p>
            </div>
            <div class="step">
                <i class="fa-solid fa-wallet icon-green"></i>
                <h3>3. Withdraw Easily</h3>
                <p>Convert your earnings into real cash. Withdraw securely via bank transfer, mobile money, or crypto.</p>
            </div>
        </div>
    </div>

    <!-- WHY CHOOSE US -->
    <div class="section">
        <h2><i class="fa-solid fa-star icon-orange"></i> Why Thousands Love Us</h2>
        <div class="cards">
            <div class="card">
                <i class="fa-solid fa-bolt icon-blue"></i>
                <h3>Lightning Fast Access</h3>
                <p>Play instantly with zero installation. Our games load in seconds so you can start earning immediately.</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-shield-halved icon-green"></i>
                <h3>100% Secure & Trusted</h3>
                <p>Advanced encryption and secure payment systems protect your data and earnings at all times.</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-money-bill-wave icon-yellow"></i>
                <h3>Real Money Rewards</h3>
                <p>Unlike fake reward apps, we pay real cash. Over <?php echo $currency; ?>1,000,000 already paid out to our players.</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-users icon-purple"></i>
                <h3>Thriving Community</h3>
                <p>Join a growing community of players who enjoy fair gameplay and genuine earning opportunities.</p>
            </div>
        </div>
    </div>

    <!-- FEATURED GAMES -->
    <div class="section">
        <h2><i class="fa-solid fa-fire icon-red"></i> Featured Games</h2>
        <div class="cards">
            <div class="card">
                <h3><i class="fa-solid fa-car icon-orange"></i> High-Speed Racing</h3>
                <p>Dodge traffic, collect coins, and beat your high score in this addictive endless runner.</p>
                <br>
                <a href="games/race-car.php" class="btn btn-primary">Play Now</a>
            </div>
            <div class="card">
                <h3><i class="fa-solid fa-bullseye icon-blue"></i> Bubble Shooter Pro</h3>
                <p>Challenge your mind with this classic bubble popping game. Clear levels and earn big rewards.</p>
                <br>
                <a href="games/bubble-shooter.php" class="btn btn-primary">Play Now</a>
            </div>
            <div class="card">
                <h3><i class="fa-solid fa-brain icon-purple"></i> Puzzle Master</h3>
                <p>Test your logic and problem-solving skills with increasingly difficult puzzles.</p>
                <br>
                <a href="games/puzzle.php" class="btn btn-primary">Play Now</a>
            </div>
        </div>
    </div>

    <!-- PLATFORM STATS -->
    <div class="section">
        <h2><i class="fa-solid fa-chart-line icon-green"></i> Our Growing Platform</h2>
        <div class="stats">
            <div class="stat-item">
                <h3>25,000+</h3>
                <p>Games Played Daily</p>
            </div>
            <div class="stat-item">
                <h3>12,450+</h3>
                <p>Happy Players</p>
            </div>
            <div class="stat-item">
                <h3><?php echo $currency; ?> 2,850,000+</h3>
                <p>Total Earnings Paid</p>
            </div>
            <div class="stat-item">
                <h3>98.7%</h3>
                <p>Successful Withdrawals</p>
            </div>
        </div>
    </div>

    <!-- PLAYER REVIEWS -->
    <div class="section">
        <h2><i class="fa-solid fa-comments icon-blue"></i> What Our Players Say</h2>
        <div class="reviews">
            <div class="review">
                <div class="review-stars">
                    ★★★★☆
                </div>
                <p>“I’ve withdrawn over ₦45,000 in just 3 weeks. The games are actually fun and the payouts are fast!”</p>
                <small><strong>- Chinedu Okafor, Lagos</strong></small>
            </div>
            <div class="review">
                <div class="review-stars">
                    ★★★★★
                </div>
                <p>“Best earning platform in Nigeria. I play during my free time and cash out every weekend. Highly recommended!”</p>
                <small><strong>- Aisha Bello, Abuja</strong></small>
            </div>
            <div class="review">
                <div class="review-stars">
                    ★★★★☆
                </div>
                <p>“Finally a legit play-to-earn site that pays. Customer support is responsive too.”</p>
                <small><strong>- Emmanuel Adebayo</strong></small>
            </div>
        </div>
    </div>

    <!-- FINAL CTA -->
    <div class="section">
        <div class="cta">
            <h2><i class="fa-solid fa-rocket"></i> Ready to Start Earning?</h2>
            <p>Join thousands of Nigerians who are turning their gaming passion into real income.</p>
            <br><br>
            <a href="games.php" class="btn btn-primary" style="font-size:18px; padding:16px 32px;">
                <i class="fa-solid fa-play"></i> Play Games Now
            </a>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-dark" style="margin-left:15px; font-size:18px; padding:16px 32px;">
                    <i class="fa-solid fa-user-plus"></i> Create Free Account
                </a>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include "inc/footer.php"; ?>
