<style>
    .kop-surat {
        width: 100%;
        margin-bottom: 20px;
        font-family: 'Times New Roman', serif;
    }
    
    .kop-header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 0;
        position: relative;
    }
    
    .logo-container {
        position: absolute;
        left: 50px;
        width: 80px;
        height: 80px;
    }
    
    .logo-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .header-text {
        text-align: center;
        flex: 1;
        margin-left: 80px;
    }
    
    .header-title {
        font-size: 16px;
        font-weight: bold;
        line-height: 1.2;
        margin: 0;
        text-transform: uppercase;
    }
    
    .header-subtitle {
        font-size: 14px;
        font-weight: bold;
        margin: 2px 0;
        text-transform: uppercase;
    }
    
    .header-village {
        font-size: 18px;
        font-weight: bold;
        margin: 5px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .header-address {
        font-size: 11px;
        margin: 3px 0;
        line-height: 1.3;
    }
    
    .header-website {
        font-size: 10px;
        margin: 2px 0;
    }
    
    .header-website a {
        color: blue;
        text-decoration: underline;
    }
    
    .kop-divider {
        border-top: 3px solid #000;
        margin: 10px 0 5px 0;
        width: 100%;
    }
    
    .kop-divider-thin {
        border-top: 1px solid #000;
        margin: 0;
        width: 100%;
    }
    
    /* Print styles */
    @media print {
        .kop-surat {
            margin-bottom: 15px;
        }
        
        .logo-container {
            left: 30px;
            width: 70px;
            height: 70px;
        }
        
        .header-text {
            margin-left: 70px;
        }
        
        .header-title {
            font-size: 15px;
        }
        
        .header-subtitle {
            font-size: 13px;
        }
        
        .header-village {
            font-size: 16px;
        }
        
        .header-address {
            font-size: 10px;
        }
        
        .header-website {
            font-size: 9px;
        }
    }
</style>

<div class="kop-surat">
    <div class="kop-header">
        <div class="logo-container">
            <img src="{{ asset('assets/images/logo-pamekasan.png') }}" alt="Logo Pamekasan" />
        </div>
        
        <div class="header-text">
            <div class="header-title">PEMERINTAH KABUPATEN PAMEKASAN</div>
            <div class="header-subtitle">KECAMATAN PALENGAAN</div>
            <div class="header-village">DESA BANYUPELLE</div>
            <div class="header-address">
                Jl. Raya Palengaan Proppo Cemkepak Desa Banyupelle 69362
            </div>
            <div class="header-website">
                Website : <a href="http://banyupelle.desa.id/">http://banyupelle.desa.id/</a> atau <a href="http://www.banyupelle.desa.id/">www.banyupelle.desa.id/</a>
            </div>
        </div>
    </div>
    
    <div class="kop-divider"></div>
    <div class="kop-divider-thin"></div>
</div> 