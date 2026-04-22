<?php
function navbar()
{
    echo <<<HTML
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos exclusivos da Navbar Administrativa */
        .adm-header {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            border-bottom: 1px solid #E5E7EB;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .adm-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .adm-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }

        .adm-brand img {
            height: 35px;
            width: auto;
            object-fit: contain;
        }

        .adm-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
            border-left: 2px solid #E5E7EB;
            padding-left: 15px;
        }

        .adm-nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .adm-nav a.nav-link {
            color: #4B5563;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .adm-nav a.nav-link:hover {
            color: #4F46E5;
        }

        .button-icons {
            background: #FEE2E2;
            border: none;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .button-icons svg {
            width: 20px;
            height: 20px;
            fill: #EF4444;
            transition: fill 0.2s ease;
        }

        .button-icons:hover {
            background: #EF4444;
        }

        .button-icons:hover svg {
            fill: #ffffff;
        }

        /* Botão do menu mobile (Hamburguer) */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            flex-direction: column;
            gap: 5px;
        }

        .menu-toggle span {
            display: block;
            width: 25px;
            height: 3px;
            background-color: #374151;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        /* Responsividade */
        @media (max-width: 850px) {
            .menu-toggle {
                display: flex;
            }

            .adm-nav {
                display: none; /* Esconde no mobile por padrão */
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background: #ffffff;
                flex-direction: column;
                padding: 20px 0;
                border-bottom: 1px solid #E5E7EB;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }

            .adm-nav.active {
                display: flex; /* Mostra quando a classe active é adicionada via JS */
            }

            .adm-title {
                display: none; /* Esconde o texto no mobile para dar espaço */
            }
        }

        .logo {
            font-size: 1.7rem;
            font-weight: 800;
            color: #4F46E5;
            letter-spacing: -0.5px;
        }

    </style>

    <header class="adm-header">
        <div class="adm-container">
            
            <a href="index.php" class="adm-brand">
            <a class="logo">Explora+</a>
            </a>

            <button id="menu-toggle" class="menu-toggle" aria-label="Abrir menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav id="nav-links" class="adm-nav">
                <a href="index.php" title="Sair do sistema">
                    <button class="button-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/>
                        </svg>
                    </button>
                </a>
            </nav>
            
        </div>
    </header>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const menuToggle = document.getElementById("menu-toggle");
        const navLinks = document.getElementById("nav-links");

        if (menuToggle && navLinks) {
            menuToggle.addEventListener("click", function() {
                navLinks.classList.toggle("active");
                // Animação opcional do ícone hambúrguer pode ir aqui
            });
        }
    });
    </script>
HTML;
}
