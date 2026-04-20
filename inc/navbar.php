<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ================= NAVBAR BASE ================= */
.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 20px;
    background:#ffffff;
    border-bottom:1px solid #eaeaea;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    position:relative;
    z-index:1000;
}

.nav-left{
    font-size:18px;
    font-weight:bold;
    color:#00aaff;
    display:flex;
    align-items:center;
    gap:8px;
}

/* ================= NAV RIGHT ================= */
.nav-right{
    display:flex;
    align-items:center;
    gap:15px;
}

/* links */
.navbar a{
    text-decoration:none;
    color:#333;
    font-weight:500;
    transition:0.2s;
    padding:6px 10px;
    border-radius:6px;
}

.navbar a:hover{
    color:#00aaff;
    background:#f5faff;
}

/* balance */
.balance{
    background:#eaf6ff;
    padding:6px 12px;
    border-radius:20px;
    font-size:14px;
    color:#0077aa;
    white-space:nowrap;
}

/* ================= HAMBURGER ================= */
.menu-toggle{
    display:none;
    font-size:22px;
    cursor:pointer;
    color:#333;
}

/* ================= MOBILE ================= */
@media (max-width: 768px){

    .menu-toggle{
        display:block;
    }

    .nav-right{
        position:absolute;
        top:60px;
        right:0;
        left:0;
        background:#fff;
        flex-direction:column;
        align-items:flex-start;
        padding:15px;
        gap:10px;
        display:none;
        border-bottom:1px solid #eee;
        box-shadow:0 10px 20px rgba(0,0,0,0.08);
    }

    .nav-right.active{
        display:flex;
    }

    .nav-right a, .balance{
        width:100%;
    }

    .balance{
        text-align:left;
    }
}
</style>

<div class="navbar">

    <div class="nav-left">
        <i class="fa-solid fa-gamepad"></i>
        <?php echo htmlspecialchars($site_name); ?>
    </div>

    <!-- HAMBURGER ICON -->
    <div class="menu-toggle" onclick="toggleMenu()">
        <i class="fa-solid fa-bars"></i>
    </div>

    <div class="nav-right" id="navMenu">

        <!-- HOME -->
        <?php if($current_page !== 'index.php'): ?>
            <a href="/index.php">
                <i class="fa-solid fa-house"></i> Home
            </a>
        <?php endif; ?>

        <?php if(isset($_SESSION['user_id'])): ?>

            <span class="balance">
                <i class="fa-solid fa-wallet"></i>
                <?php echo $currency . " " . number_format($_SESSION['balance'],2); ?>
            </span>

            <!-- DASHBOARD -->
            <?php if($current_page !== 'dashboard.php'): ?>
                <a href="/dashboard.php">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            <?php endif; ?>

            <a href="/logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>

        <?php else: ?>

            <a href="/login.php">
                <i class="fa-solid fa-user"></i> Login
            </a>

            <a href="/register.php">
                <i class="fa-solid fa-user-plus"></i> Register
            </a>

        <?php endif; ?>

    </div>
</div>

<script>
function toggleMenu(){
    document.getElementById("navMenu").classList.toggle("active");
}
</script>
