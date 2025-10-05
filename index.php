

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Dropdown Menu</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.navbar {
    background-color: #333;
    color: white;
    padding: 15px;
    position: relative;
    z-index: 1000;
}

.menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: space-around;
}

.menu li {
    position: relative;
}

.menu li a {
    color: white;
    text-decoration: none;
    padding: 15px 20px;
    display: block;
}

.menu li a:hover,
.dropdown:hover>.dropdown-toggle {
    background-color: #555;
}

.dropdown-menu {
    display: none;
    position: absolute;
    left: 0;
    top: 100%;
    background-color: #333;
    min-width: 200px;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.dropdown-menu li {
    width: 100%;
}

.dropdown-menu li a {
    padding: 10px 20px;
}

.dropdown-menu .dropdown-menu {
    top: 0;
    left: 100%;
}

.menu li:hover>.dropdown-menu {
    display: block;
}

@media (max-width: 768px) {
    .menu {
        flex-direction: column;
        align-items: flex-start;
    }

    .dropdown-menu {
        position: relative;
    }
}

.container {
    margin: auto;
    justify-content: center;
    align-items: center;

    position: relative;
    width: 100%;
    height: 90vh;
    display: flex;
}

.container .text {
    font-size: 50px;
    font-weight: 900;
    font-variant: normal;

    /* box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.5), 0 0 20px rgba(0, 0, 0, 0.3); */
    box-shadow:
        0 0 10px rgba(0, 0, 0, 0.4),
        0 0 30px rgba(0, 0, 0, 0.1) inset,
        -20px 20px 40px rgba(255, 255, 255, 0.2),
        -20px 20px 60px rgba(255, 255, 255, 0.3) inset;
    animation: gradient-slide 3s linear infinite;
    background:
        linear-gradient(45deg, #ff7e5f 25%, transparent 25%, transparent 75%, #ff7e5f 75%),
        linear-gradient(-45deg, #ff7e5f 25%, transparent 25%, transparent 75%, #ff7e5f 75%);
    background-size: 50% 50%;
    background-position: 0 0, 50% 0;
}

@keyframes gradient-slide {
    0% {
        background-position: 0 50%;
    }

    100% {
        background-position: 100% 50%;
    }
}
.dot{
    color: #fff;
  font-size: 48px;
  background: linear-gradient(to right, #00b4db, #0083b0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4); */
  padding: 20px;
  font-size: 48px;
  text-shadow: 0 0 10px #f0f, 0 0 20px #0ff, 0 0 30px #00f;
  animation: neon-glow 1s ease-in-out infinite alternate;
}

@keyframes neon-glow {
  0% { text-shadow: 0 0 10px #f0f, 0 0 20px #0ff, 0 0 30px #00f; }
  100% { text-shadow: 0 0 20px #f0f, 0 0 40px #0ff, 0 0 60px #00f; }
}
    </style>
</head>

<body>
    <nav class="navbar">
        <ul class="menu">
            <li><a href="#">Home</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Services</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Web Development</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Mobile Development</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">iOS</a></li>
                            <li><a href="#">Android</a></li>
                            <li><a href="#">Cross-Platform</a></li>
                        </ul>
                    </li>
                    <li><a href="#">SEO Services</a></li>
                    <li><a href="#">Graphic Design</a></li>
                </ul>
            </li>
            <li><a href="#">About Us</a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Register</a>
                <ul class="dropdown-menu">
                    <li><a href="./forms/SignUp.php">Signup</a></li>
                    <li><a href="./forms/login.php">Login</a></li>
                    <li><a href="#">Help</a></li>
                </ul>
            </li>

            <li><a href="#">Contact</a></li>
        </ul>
    </nav>

    <div class="container d-block">

        <h1 class="text">CODEWITH <span class="dot">AG</span></h1>
    </div>

    <script src="script.js"></script>
</body>

</html>