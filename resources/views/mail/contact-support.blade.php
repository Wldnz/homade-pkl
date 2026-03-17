<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pesan Support Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            padding: 30px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .email-header {
            background-color: #0d6efd; /* Primary Bootstrap */
            color: #ffffff;
            padding: 25px 20px;
            text-align: center;
        }
        .email-body {
            padding: 30px;
        }
        .info-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 1.05rem;
            color: #212529;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .message-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 20px;
            border-radius: 4px;
            color: #495057;
            white-space: pre-wrap; /* Biar enter/newline dari textarea kebaca */
            font-style: italic;
        }
        .email-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 15px;
            text-align: center;
            font-size: 0.85rem;
            color: #adb5bd;
        }
        a { text-decoration: none; color: #0d6efd; }
    </style>
</head>
<body>
    <div class="email-card">
        <div class="email-header">
            <h4 class="mb-0 fw-bold">Pesan Masuk Customer</h4>
        </div>
        
        <div class="email-body">
            <p class="mb-4">Halo Tim Support,</p>
            <p class="mb-4">Ada pesan baru yang masuk melalui formulir kontak di website. Berikut adalah detail dari pengirim:</p>
            
            <div class="row">
                <div class="col-sm-6">
                    <div class="info-label">Nama Pengirim</div>
                    <div class="info-value">{{ $mailData['fullname'] }}</div>
                </div>
                <div class="col-sm-6">
                    <div class="info-label">Alamat Email</div>
                    <div class="info-value">
                        <a href="mailto:{{ $mailData['email'] }}">{{ $mailData['email'] }}</a>
                    </div>
                </div>
            </div>

            <div class="info-label mt-2">Subjek Pesan</div>
            <div class="info-value fw-bold">{{ $mailData['subject'] }}</div>

            <div class="info-label mt-4 mb-2">Isi Pesan:</div>
            <div class="message-box">{{ $mailData['message'] }}</div>
            
            <div class="mt-4 pt-3 border-top">
                <p class="small text-muted mb-0">
                    <i class="bi bi-info-circle me-1"></i> <strong>Tips:</strong> Anda bisa langsung membalas email ini (Reply) untuk merespons pesan customer secara langsung.
                </p>
            </div>
        </div>
        
        <div class="email-footer">
            &copy; {{ date('Y') }} Sistem Notifikasi Katering.
        </div>
    </div>
</body>
</html>