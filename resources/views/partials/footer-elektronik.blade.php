{{-- Footer Elektronik untuk PDF Multiple Pages --}}
<style>
    @page {
        margin-bottom: 4cm; /* Space untuk footer */
    }
    
    .footer-elektronik {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8pt;
        color: #666;
        border-top: 1px solid #ccc;
        padding: 8px 5px;
        background: white;
        line-height: 1.2;
        z-index: 1000;
    }
    
    /* For better compatibility with DomPDF */
    .footer-content {
        max-width: 90%;
        margin: 0 auto;
    }
    
    /* Alternative approach - page break handling */
    .page-footer {
        page-break-inside: avoid;
        position: fixed;
        bottom: 10mm;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8pt;
        color: #666;
        border-top: 1px solid #ccc;
        padding: 4px 0;
        background: white;
    }
</style>

{{-- Footer Content --}}
<div class="footer-elektronik">
    <div class="footer-content">
        <strong>Dokumen ini telah ditandatangani secara elektronik</strong><br>
        menggunakan sertifikat elektronik yang diterbitkan oleh<br>
        <strong>Balai Besar Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara</strong>
    </div>
</div>

{{-- Alternative footer for better multiple page support --}}
<div class="page-footer">
    <strong>Dokumen ini telah ditandatangani secara elektronik</strong> | 
    menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara
</div> 