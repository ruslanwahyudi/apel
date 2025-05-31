<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Multiple Pages Footer</title>
    <style>
        @page {
            margin: 1.5cm 2cm 2.5cm 2cm;
            size: A4;
            @bottom-center {
                content: "Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara";
                font-size: 7pt;
                color: #666;
                text-align: center;
                border-top: 1px solid #ccc;
                padding-top: 5px;
            }
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .page-content {
            margin: 20px 0;
            padding: 20px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* Fallback footer jika @page tidak bekerja */
        .footer-fallback {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #666;
            background: white;
            border-top: 1px solid #ccc;
            padding: 3px;
            margin: 0;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    {{-- Include Kop Surat Dynamic --}}
    @include('partials.kop-surat-dynamic')
    
    <div class="page-content">
        <h2 style="text-align: center; text-decoration: underline;">TEST SURAT MULTIPLE PAGES</h2>
        
        <h3>Halaman 1</h3>
        <p>Ini adalah konten halaman pertama untuk testing footer elektronik di multiple pages.</p>
        <p><strong>Metode Footer:</strong></p>
        <ul>
            <li>@page @bottom-center untuk CSS footer otomatis</li>
            <li>Fallback dengan position: fixed untuk kompatibilitas</li>
        </ul>
        
        @for($i = 1; $i <= 25; $i++)
            <p>Baris {{ $i }}: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        @endfor
    </div>
    
    <div class="page-content page-break">
        <h3>Halaman 2</h3>
        <p>Ini adalah konten halaman kedua untuk memverifikasi footer muncul di semua halaman.</p>
        <p><strong>Footer harus muncul konsisten di setiap halaman PDF.</strong></p>
        
        @for($i = 1; $i <= 25; $i++)
            <p>Baris {{ $i }}: Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        @endfor
    </div>
    
    <div class="page-content page-break">
        <h3>Halaman 3</h3>
        <p>Ini adalah konten halaman ketiga untuk memastikan footer konsisten di semua halaman.</p>
        <p><strong>Jika footer muncul di halaman ini juga, berarti implementasi berhasil!</strong></p>
        
        @for($i = 1; $i <= 15; $i++)
            <p>Baris {{ $i }}: Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
        @endfor
        
        {{-- Include Tanda Tangan --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '300px',
            'marginTop' => '50px',
            'marginBottom' => '30px',
            'spacingTtd' => '100px'
        ])
    </div>
    
    {{-- Fallback Footer --}}
    <div class="footer-fallback">
        <strong>Dokumen ini telah ditandatangani secara elektronik</strong> | menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara
    </div>
</body>
</html> 