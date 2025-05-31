<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Permohonan Penerbitan KIA</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 1.5cm 1.5cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 10pt;
            line-height: 1;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .surat-content {
            margin: 0;
            padding: 0;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin: 10px 0 15px 0;
            text-decoration: underline;
        }
        
        .form-section {
            margin: 10px 0;
        }
        
        .form-row {
            display: table;
            width: 100%;
            margin: 3px 0;
        }
        
        .form-label {
            display: table-cell;
            width: 120px;
            vertical-align: top;
            font-size: 9pt;
        }
        
        .form-colon {
            display: table-cell;
            width: 15px;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }
        
        .form-value {
            display: table-cell;
            vertical-align: top;
            font-size: 9pt;
            border-bottom: 1px dotted #000;
            min-height: 12px;
            padding-bottom: 1px;
        }
        
        .form-checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            text-align: center;
            font-size: 8pt;
            line-height: 10px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 9pt;
            margin: 8px 0 5px 0;
            text-transform: uppercase;
        }
        
        .persyaratan-section {
            margin: 15px 0;
        }
        
        .persyaratan-section ol {
            margin: 5px 0;
            padding-left: 20px;
            font-size: 9pt;
        }
        
        .persyaratan-section ol li {
            margin: 3px 0;
            line-height: 1.2;
        }
        
        .pernyataan-section {
            margin: 15px 0;
            font-size: 9pt;
            text-align: justify;
            line-height: 1.3;
        }
        
        .signature-section {
            margin-top: 20px;
            text-align: right;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 40px 0 5px 0;
            height: 1px;
        }
        
        .checkbox-group {
            margin: 3px 0;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            
            .surat-content {
                max-width: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="surat-content">
        {{-- Include Kop Surat --}}
        @include('partials.kop-surat')
        
        {{-- Judul Formulir --}}
        <div class="judul-surat">
            FORMULIR PERMOHONAN PENERBITAN
            <br>
            KARTU IDENTITAS ANAK (KIA)
        </div>
        
        {{-- Data Anak Section --}}
        <div class="form-section">
            <div class="section-title">A. Data Anak</div>
            
            <div class="form-row">
                <div class="form-label">1. NIK Anak</div>
                <div class="form-colon">:</div>
                <div class="form-value">{{ $data['nik_anak'] ?? '...................................................................................................' }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">2. Nama Anak</div>
                <div class="form-colon">:</div>
                <div class="form-value">{{ $data['nama_anak'] ?? '...................................................................................................' }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">3. Tempat Lahir</div>
                <div class="form-colon">:</div>
                <div class="form-value">{{ $data['tempat_lahir'] ?? '...................................................................................................' }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">4. Tanggal Lahir</div>
                <div class="form-colon">:</div>
                <div class="form-value">
                    @php
                        $tanggalLahir = isset($data['tanggal_lahir']) && !empty($data['tanggal_lahir']) 
                            ? \Carbon\Carbon::parse($data['tanggal_lahir'])->locale('id')->translatedFormat('j F Y')
                            : '...................................................................................................';
                    @endphp
                    {{ $tanggalLahir }}
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">5. Alamat Anak</div>
                <div class="form-colon">:</div>
                <div class="form-value">{{ $data['alamat'] ?? '...................................................................................................' }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">6. No. KK</div>
                <div class="form-colon">:</div>
                <div class="form-value">{{ $data['no_kk'] ?? '...................................................................................................' }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">7. No. Akte Kelahiran</div>
                <div class="form-colon">:</div>
                <div class="form-value">{{ $data['no_akte'] ?? '...................................................................................................' }}</div>
            </div>
        </div>
        
        {{-- Data Orang Tua Section --}}
        <div class="form-section">
            <div class="section-title">B. Data Orang Tua</div>
            
            <div class="form-row">
                <div class="form-label">1. Nama Ayah</div>
                <div class="form-colon">:</div>
                <div class="form-value">{{ $data['nama_ayah'] ?? '...................................................................................................' }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">2. Nama Ibu</div>
                <div class="form-colon">:</div>
                <div class="form-value">{{ $data['nama_ibu'] ?? '...................................................................................................' }}</div>
            </div>
        </div>
        
        {{-- Persyaratan Section --}}
        <div class="persyaratan-section">
            <div class="section-title">PERSYARATAN :</div>
            <ol>
                <li>Fotokopi Kartu Keluarga</li>
                <li>Fotokopi Akta Kelahiran Anak</li>
                <li>Fotokopi KTP Orang Tua/Wali yang masih berlaku</li>
                <li>Foto terbaru anak ukuran 2x3 cm sebanyak 2 lembar</li>
                <li>Apabila anak menentang atau menolak difoto, maka foto dapat diganti dengan foto Orang Tua/Wali</li>
            </ol>
        </div>
        
        {{-- Pernyataan Section --}}
        <div class="pernyataan-section">
            Dengan ini saya Orang Tua/Wali menyatakan dengan sesungguhnya bahwa semua keterangan yang 
            disampaikan dalam formulir ini adalah benar, apabila dikemudian hari ditemukan ketidakbenaran 
            dalam keterangan tersebut, maka saya bersedia mempertanggungjawabkan sesuai dengan ketentuan 
            perundang-undangan yang berlaku.
        </div>
        
        {{-- Tanda Tangan Pemohon --}}
        @php
            $tanggalSurat = isset($data['tanggal']) && !empty($data['tanggal']) 
                ? \Carbon\Carbon::parse($data['tanggal'])->locale('id')->translatedFormat('j F Y')
                : \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
        @endphp
        
        <div style="text-align: right; margin-top: 20px; margin-bottom: 30px;">
            <div style="display: inline-block; text-align: center; width: 250px;">
                <div style="margin-bottom: 10px;">
                    Pamekasan, {{ $tanggalSurat }}
                </div>
                <div>Pemohon,</div>
                <div style="margin: 80px 0 10px 0;"></div>
                <div style="font-weight: bold; text-decoration: underline;">
                    @if(isset($data['nama_ayah']) && !empty($data['nama_ayah']))
                        {{ $data['nama_ayah'] }}
                    @elseif(isset($data['nama_ibu']) && !empty($data['nama_ibu']))
                        {{ $data['nama_ibu'] }}
                    @else
                        (Orang Tua/Wali)
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html> 