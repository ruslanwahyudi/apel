{{-- 
    Kop Surat Dynamic - mengambil konfigurasi dari database
    Menggunakan model KopSuratConfig untuk mendapatkan data kop surat
--}}

@php
    use App\Models\KopSuratConfig;
    $kopConfig = KopSuratConfig::getActiveConfig();
@endphp

<style>
    .kop-surat {
        width: 100%;
        margin-bottom: 15px;
        font-family: 'Times New Roman', serif;
        margin-top: 0;
        padding-top: 0;
    }
    
    .kop-header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px 0;
        position: relative;
        margin-top: 0;
    }
    
    .logo-container {
        position: absolute;
        left: 40px;
        width: 90px;
        height: 90px;
    }
    
    .logo-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .header-text {
        text-align: center;
        flex: 1;
        margin-left: 90px;
        line-height: 1.1;
    }
    
    .header-title {
        font-size: 20px;
        font-weight: bold;
        line-height: 1.1;
        margin: 0 0 2px 0;
        text-transform: uppercase;
    }
    
    .header-subtitle {
        font-size: 18px;
        font-weight: bold;
        margin: 2px 0;
        text-transform: uppercase;
    }
    
    .header-village {
        font-size: 22px;
        font-weight: bold;
        margin: 3px 0;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .header-address {
        font-size: 13px;
        margin: 3px 0;
        line-height: 1.2;
    }
    
    .header-website {
        font-size: 12px;
        margin: 2px 0;
    }
    
    .header-website a {
        color: blue;
        text-decoration: underline;
    }
    
    .kop-divider {
        border-top: 3px solid #000;
        margin: 8px 0 3px 0;
        width: 100%;
    }
    
    .kop-divider-thin {
        border-top: 1px solid #000;
        margin: 0 0 15px 0;
        width: 100%;
    }
    
    /* PDF/Print optimized styles */
    @media print {
        .kop-surat {
            margin-bottom: 10px;
            margin-top: 0;
            padding-top: 0;
        }
        
        .kop-header {
            padding: 3px 0;
            margin-top: 0;
        }
        
        .logo-container {
            left: 35px;
            width: 85px;
            height: 85px;
        }
        
        .header-text {
            margin-left: 85px;
        }
        
        .header-title {
            font-size: 19px;
        }
        
        .header-subtitle {
            font-size: 17px;
        }
        
        .header-village {
            font-size: 21px;
        }
        
        .header-address {
            font-size: 12px;
        }
        
        .header-website {
            font-size: 11px;
        }
        
        .kop-divider {
            margin: 6px 0 2px 0;
        }
        
        .kop-divider-thin {
            margin: 0 0 12px 0;
        }
    }
    
    /* DomPDF specific optimizations */
    @page {
        margin-top: 1cm;
    }
    
    body {
        margin-top: 0;
        padding-top: 0;
    }
</style>

<div class="kop-surat">
    <div class="kop-header">
        <div class="logo-container">
            <img src="{{ asset($kopConfig->logo_path) }}" alt="Logo {{ $kopConfig->kabupaten }}" />
        </div>
        
        <div class="header-text">
            <div class="header-title">PEMERINTAH KABUPATEN {{ $kopConfig->kabupaten }}</div>
            <div class="header-subtitle">KECAMATAN {{ $kopConfig->kecamatan }}</div>
            <div class="header-village">DESA {{ $kopConfig->desa }}</div>
            <div class="header-address">
                {{ $kopConfig->alamat }}
            </div>
            <div class="header-website">
                Website : <a href="{{ $kopConfig->website1 }}">{{ $kopConfig->website1 }}</a> atau <a href="http://{{ $kopConfig->website2 }}">{{ $kopConfig->website2 }}</a>
            </div>
        </div>
    </div>
    
    <div class="kop-divider"></div>
    <div class="kop-divider-thin"></div>
</div>