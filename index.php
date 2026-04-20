<?php include "inc/header.php"; ?>
<?php include "inc/navbar.php"; ?>

<style>
.container{
    max-width:1100px;
    margin:auto;
    padding:20px;
}

/* HERO */
.hero{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    margin-top:30px;
}

.hero-text{
    flex:1;
}

.hero h1{
    font-size:36px;
    margin-bottom:10px;
}

.hero p{
    color:#555;
    font-size:16px;
}

.hero-img{
    flex:1;
    text-align:center;
}

.hero-img img{
    width:100%;
    max-width:400px;
}

/* SECTIONS */
.section{
    margin-top:60px;
}

.section h2{
    margin-bottom:20px;
}

/* CARDS */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.card{
    padding:20px;
}

/* REVIEWS */
.review{
    background:#fff;
    padding:15px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

.review small{
    color:#777;
}

/* CTA */
.cta{
    text-align:center;
    padding:40px;
    background:#eaf6ff;
    border-radius:10px;
}
</style>

<div class="container">

<!-- HERO -->
<div class="hero">
    <div class="hero-text">
        <h1>🎮 Play Games. Earn Real <?php echo $currency; ?>.</h1>
        <p>
            Turn your time into rewards. Play exciting games, earn money, 
            and withdraw anytime. Simple, fun, and rewarding.
        </p>

        <br>

        <a href="games.php" class="btn">🎮 Start Playing</a>

        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn" style="margin-left:10px;background:#222;">
                Create Account
            </a>
        <?php endif; ?>
    </div>

    <div class="hero-img">
        <img src="assets/images/hero.png" alt="Game">
    </div>
</div>

<!-- HOW IT WORKS -->
<div class="section">
    <h2>⚙️ How It Works</h2>

    <div class="cards">
        <div class="card">
            <h3>🎮 Play Games</h3>
            <p>Choose from multiple fun games and start playing instantly.</p>
        </div>

        <div class="card">
            <h3>💰 Earn Money</h3>
            <p>Earn <?php echo $currency; ?> based on your play time and performance.</p>
        </div>

        <div class="card">
            <h3>💸 Withdraw</h3>
            <p>Request withdrawals anytime directly to your preferred method.</p>
        </div>
    </div>
</div>

<!-- FEATURED GAMES -->
<div class="section">
    <h2>🔥 Featured Games</h2>

    <div class="cards">

        <div class="card">
            <h3>🏎️ Race Car</h3>
            <p>Dodge traffic and survive as long as possible.</p>
            <a href="games/race-car.php" class="btn">Play</a>
        </div>

        <div class="card">
            <h3>🎯 Bubble Shooter</h3>
            <p>Match bubbles and clear the board.</p>
            <a href="games/bubble.php" class="btn">Play</a>
        </div>

    </div>
</div>

<!-- TRUST / STATS -->
<div class="section">
    <h2>📊 Trusted by Players</h2>

    <div class="cards">
        <div class="card">
            <h3>10,000+</h3>
            <p>Games Played</p>
        </div>

        <div class="card">
            <h3>5,000+</h3>
            <p>Active Players</p>
        </div>

        <div class="card">
            <h3>₦1,000,000+</h3>
            <p>Total Paid Out</p>
        </div>
    </div>
</div>

<!-- REVIEWS -->
<div class="section">
    <h2>💬 What Players Say</h2>

    <div class="cards">

        <div class="review">
            ⭐⭐⭐⭐⭐<br>
            “This is actually legit. I earned and withdrew without issues.”
            <br><small>- Daniel</small>
        </div>

        <div class="review">
            ⭐⭐⭐⭐⭐<br>
            “The games are fun and addictive. Love the earning system.”
            <br><small>- Sarah</small>
        </div>

        <div class="review">
            ⭐⭐⭐⭐⭐<br>
            “Best play-to-earn site I’ve used so far.”
            <br><small>- Michael</small>
        </div>

    </div>
</div>

<!-- CTA -->
<div class="section">
    <div class="cta">
        <h2>🚀 Ready to Start Earning?</h2>
        <p>Join thousands of players already earning <?php echo $currency; ?>.</p>

        <br>

        <a href="games.php" class="btn">Start Playing Now</a>

        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn" style="margin-left:10px;background:#222;">
                Create Free Account
            </a>
        <?php endif; ?>
    </div>
</div>

</div>

<?php include "inc/footer.php"; ?>
