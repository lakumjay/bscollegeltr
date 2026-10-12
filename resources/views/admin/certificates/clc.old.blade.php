<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>प्रमाण पत्र</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+Devanagari:wght@400;600;700;900&family=Poppins:wght@300;400;600&display=swap');
        
        @page {
            size: A4;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Noto Serif Devanagari', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .certificate {
            width: 210mm;
            height: 297mm;
            background: white;
            position: relative;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        /* Decorative borders */
        .certificate::before {
            content: '';
            position: absolute;
            top: 15mm;
            left: 15mm;
            right: 15mm;
            bottom: 15mm;
            border: 3px solid #2c5aa0;
            border-radius: 4px;
        }
        
        .certificate::after {
            content: '';
            position: absolute;
            top: 18mm;
            left: 18mm;
            right: 18mm;
            bottom: 18mm;
            border: 1px solid #d4af37;
            border-radius: 2px;
        }
        
        /* Header section */
        .header {
            text-align: center;
            padding: 25mm 30mm 15mm;
            position: relative;
            z-index: 10;
        }
        
        .logo {
            max-width: 80mm;
            max-height: 25mm;
            margin-bottom: 15mm;
        }
        
        .certificate-title {
            font-size: 22px;
            font-weight: 700;
            color: #2c5aa0;
            margin-bottom: 8mm;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        /* Content section */
        .content {
            padding: 0 30mm;
            position: relative;
            z-index: 10;
        }
        
        .top-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8mm;
            font-size: 14px;
            color: #333;
        }
        
        .serial-no {
            font-weight: 600;
        }
        
        .date {
            font-weight: 600;
        }
        
        .student-info {
            margin-bottom: 15mm;
        }
        
        .info-row {
            margin: 6mm 0;
            font-size: 16px;
            line-height: 1.8;
            color: #333;
        }
        
        .info-label {
            font-weight: 600;
            color: #2c5aa0;
        }
        
        .underline {
            border-bottom: 2px solid #2c5aa0;
            display: inline-block;
            min-width: 120px;
            text-align: center;
            font-weight: 600;
            color: #000;
            padding: 2px 8px;
            margin: 0 5px;
        }
        
        .long-underline {
            border-bottom: 2px solid #2c5aa0;
            display: inline-block;
            min-width: 200px;
            text-align: center;
            font-weight: 600;
            color: #000;
            padding: 2px 8px;
            margin: 0 5px;
        }
        
        /* Performance section */
        .performance {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 10mm 15mm;
            margin: 8mm -10mm;
            border-radius: 8px;
            border-left: 4px solid #d4af37;
        }
        
        .performance-title {
            font-size: 16px;
            font-weight: 700;
            color: #2c5aa0;
            margin-bottom: 5mm;
            text-align: center;
        }
        
        .performance-row {
            margin: 4mm 0;
            font-size: 15px;
            display: flex;
            align-items: center;
        }
        
        .performance-label {
            min-width: 100px;
            font-weight: 600;
            color: #495057;
        }
        
        /* Receipt section */
        .receipt-info {
            margin-top: 8mm;
            padding: 5mm 10mm;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        .receipt-row {
            display: inline-block;
            margin: 2mm 15mm 2mm 0;
            font-size: 14px;
        }
        
        /* Signature section */
        .signature-section {
        width:100%;
            margin-top: 15mm;
            display: flex;
            justify-content: space-between;
          
            gap:20px;
            padding: 0 5mm;
        }
        
        .signature-item {
            text-align: center;
        }
        
        .signature-line {
            width: auto;
            height: 1px;
            border-bottom: 2px solid #2c5aa0;
            margin-bottom: 3mm;
        }
        
        .signature-title {
            font-size: 14px;
            font-weight: 600;
            color: #2c5aa0;
        }
        
        /* Decorative elements */
        .corner-decoration {
            position: absolute;
            width: 30px;
            height: 30px;
            background: linear-gradient(45deg, #d4af37, #f4d03f);
            border-radius: 50%;
        }
        
        .corner-decoration.top-left {
            top: 20mm;
            left: 20mm;
        }
        
        .corner-decoration.top-right {
            top: 20mm;
            right: 20mm;
        }
        
        .corner-decoration.bottom-left {
            bottom: 20mm;
            left: 20mm;
        }
        
        .corner-decoration.bottom-right {
            bottom: 20mm;
            right: 20mm;
        }
        
        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .certificate {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
            }
        }
        
        /* Responsive design */
        @media screen and (max-width: 768px) {
            .certificate {
                width: 95vw;
                height: auto;
                min-height: 95vh;
            }
            
            .header {
                padding: 15mm 20mm 10mm;
            }
            
            .content {
                padding: 0 20mm;
            }
            
            .certificate-title {
                font-size: 18px;
            }
            
            .info-row {
                font-size: 14px;
            }
        }
        .signature-block{
        text-align:center;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="certificate">
        <!-- Decorative corner elements -->
        <div class="corner-decoration top-left"></div>
        <div class="corner-decoration top-right"></div>
        <div class="corner-decoration bottom-left"></div>
        <div class="corner-decoration bottom-right"></div>
        
        <!-- Header section -->
        <div class="header">
            <img src="{{ url($profile->banner) }}" alt="College Logo" class="logo">
            <h1 class="certificate-title">महाविद्यालय परित्याग प्रमाण-पत्र/स्थानांतरण प्रमाण-पत्र</h1>
        </div>
        
        <!-- Content section -->
        <div class="content">
            <div class="top-info">
                <span class="serial-no">क्र. संख्या- <span class="underline">{{ $sr = $student->id}}</span></span>
                <span class="date">दिनांक- <span class="underline">{{date('d M Y')}}</span></span>
            </div>
            
            <div class="student-info">
                <div class="info-row">
                    <span class="info-label">विद्यार्थी का नाम-</span> 
                    <span class="long-underline">{{$student->first_name}} {{$student->last_name}}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">कक्षा-</span> 
                    <span class="underline">{{$student->course_details->name}}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">पिता/पति का नाम श्री-</span> 
                    <span class="long-underline">{{$student->father_name}}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">माता का नाम श्रीमती-</span> 
                    <span class="long-underline">{{$student->mother_name}}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">क्रमांक-</span> 
                    <span class="underline">{{$student->roll_no}}</span> 
                    इन्होंने {{ $profile->name_in_hindi }} द्वारा आयोजित सत्र 
                    <span class="underline"> &nbsp;</span> की वार्षिक/पूरक परीक्षा 
                    <span class="underline">&nbsp;</span> की है।
                </div>
                
                <div class="info-row">
                    वर्ष <span class="underline">&nbsp;</span> में 
                    <span class="underline">&nbsp;</span> श्रेणी में उत्तीर्ण हुआ/हुई है।
                </div>
                
                <div class="info-row">
                    इनका परीक्षा केन्द्र <span class="underline">{{ $profile->name_in_hindi }}</span> 
                    एवं विश्वविद्यालय पंजीयन संख्या <span class="underline">{!!$student->university_reg_no ?? '&nbsp;'!!}</span> है।
                </div>
                
                <div class="info-row">
                    प्रवेश पुस्तिका के अनुसार इनका आचरण <span class="underline">उत्तम</span> है तथा इनसे संबंधित कोई अनुशासनात्मक कार्यवाही लंबित नहीं है।
                </div>
            </div>

            <!-- Signature section -->
            <div class="signature-section">
                <div class="signature-block">
                    <span class="signature-label">प्राचार्य</span><br>
                    <span class="college-name">{{ $profile->name_in_hindi }}</span>
                </div>

  <div class="signature-block">
                    <span class="signature-label" style=> सहायक</span><br>
                    <span class="college-name">{{ $profile->name_in_hindi }}</span>
                </div>
                
            </div>
        </div> <!-- End of content -->
    </div> <!-- End of certificate -->
</body>
</html>

