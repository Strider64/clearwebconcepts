<header class="headerStyle" itemprop="header">
    <div class="logo">
        <a class="logoImage" href="shop.php" title="Products Page">
            <img src="assets/images/img-company-logo-new-001.jpg" alt="clearwebconcepts">
        </a>
    </div>
    <div class="header-text">
        <h1>John Pepp</h1>
        <p>Web Development & Fine Art Photography</p>
        <p>Southeastern Michigan</p>
    </div>
</header>

<nav class="nav">
    <!-- Burger Button for mobile navigation -->
    <button class="nav-btn" id="nav-btn" aria-label="Toggle navigation">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <!-- Navigation links -->
    <div class="nav-links" id="nav-links">
        <?php $database->regular_navigation(); ?>

    </div>
</nav>
