<style>
    body {
        font-family: 'Poppins', sans-serif;
        line-height: 1.6;
    }

    .navbar {
        background-color: rgba(255, 255, 255, 0.95);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .navbar-brand {
        font-weight: 600;
        color: #2c3e50;
    }

    .brand-logo {
        height: 40px;
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover .brand-logo {
        transform: scale(1.05);
    }

    .navbar-brand span {
        font-size: 1.2rem;
        font-weight: 600;
        background: linear-gradient(45deg, #2c3e50, #3498db);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-link {
        color: #2c3e50;
        font-weight: 500;
        margin: 0 10px;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: #3498db;
    }

    .hero {
        height: 100vh;
        position: relative;
        overflow: hidden;
    }

    .carousel, .carousel-inner, .carousel-item {
        height: 100%;
    }

    .hero-slide {
        height: 100vh;
        display: flex;
        align-items: center;
        color: white;
        transition: all 0.3s ease;
    }

    .carousel-item h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease;
    }

    .carousel-item.active h1 {
        opacity: 1;
        transform: translateY(0);
    }

    .carousel-item p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease 0.3s;
    }

    .carousel-item.active p {
        opacity: 1;
        transform: translateY(0);
    }

    .carousel-item .btn {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease 0.6s;
    }

    .carousel-item.active .btn {
        opacity: 1;
        transform: translateY(0);
    }

    .carousel-indicators {
        margin-bottom: 2rem;
    }

    .carousel-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin: 0 5px;
        background-color: rgba(255, 255, 255, 0.5);
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .carousel-indicators button.active {
        background-color: #fff;
        transform: scale(1.2);
    }

    .carousel-control-prev, .carousel-control-next {
        width: 5%;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .hero:hover .carousel-control-prev,
    .hero:hover .carousel-control-next {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .carousel-item h1 {
            font-size: 2.5rem;
        }
        
        .carousel-item p {
            font-size: 1rem;
        }
    }

    .btn-custom {
        padding: 12px 30px;
        font-weight: 500;
        border-radius: 30px;
        transition: all 0.3s ease;
    }

    .btn-primary-custom {
        background-color: #3498db;
        border: none;
        color: white;
    }

    .btn-primary-custom:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }

    .features {
        padding: 80px 0;
        background-color: #f8f9fa;
    }

    .feature-box {
        text-align: center;
        padding: 30px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        margin-bottom: 30px;
    }

    .feature-box:hover {
        transform: translateY(-10px);
    }

    .feature-icon {
        font-size: 2.5rem;
        color: #3498db;
        margin-bottom: 20px;
    }

    .news-section {
        padding: 80px 0;
    }

    .news-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        margin-bottom: 30px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .news-card:hover {
        transform: translateY(-5px);
    }

    .news-card .card-img-top {
        height: 200px;
        object-fit: cover;
    }

    .news-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .news-card .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.8em;
    }

    .news-card .card-text {
        color: #666;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }

    .news-card .btn {
        margin-top: auto;
        align-self: flex-start;
    }

    .footer {
        background-color: #2c3e50;
        color: white;
        padding: 50px 0 20px;
    }

    .social-links a {
        color: white;
        margin: 0 10px;
        font-size: 1.5rem;
        transition: color 0.3s ease;
    }

    .social-links a:hover {
        color: #3498db;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        border-radius: 10px;
    }

    .dropdown-item {
        padding: 8px 20px;
        color: #2c3e50;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #3498db;
        transform: translateX(5px);
    }

    .user-profile {
        display: flex;
        align-items: center;
        padding: 5px 15px;
    }

    .user-profile img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
        border: 2px solid #3498db;
    }

    .user-profile .user-info {
        line-height: 1.2;
    }

    .user-profile .user-name {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 0.9rem;
    }

    .user-profile .user-role {
        color: #7f8c8d;
        font-size: 0.8rem;
        margin: 0;
    }

    .dropdown-divider {
        margin: 0.5rem 0;
    }

    .nav-link.dropdown-toggle {
        display: flex;
        align-items: center;
    }

    .nav-link.dropdown-toggle::after {
        margin-left: 0.5em;
        vertical-align: middle;
    }

    .user-dropdown-toggle {
        display: flex;
        align-items: center;
        padding: 0;
    }

    @media (max-width: 991px) {
        .user-profile {
            padding: 5px 0;
        }
        
        .user-info {
            flex: 1;
        }

        .nav-link.dropdown-toggle::after {
            margin-left: auto;
        }
    }

    .carousel-item.active .btn {
        opacity: 1;
        transform: translateY(0);
    }

    .app-download {
        margin-top: 20px;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease 0.9s;
    }

    .carousel-item.active .app-download {
        opacity: 1;
        transform: translateY(0);
    }

    .playstore-button {
        display: inline-block;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 114, 255, 0.3);
    }

    .playstore-button:hover {
        background: linear-gradient(45deg, #0072ff, #00c6ff);
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 6px 20px rgba(0, 114, 255, 0.4);
    }

    .playstore-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .playstore-icon {
        font-size: 28px;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .playstore-text {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.2;
    }

    .playstore-text .small-text {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
    }

    .playstore-text .big-text {
        font-size: 18px;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
</style> 