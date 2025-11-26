@extends('layouts.app')

@section('title', 'Test Print Layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">A4 Landscape Print Test Page</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Test Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This test page validates the A4 landscape print layout. Click "Test Print" to see how the layout appears when printed.
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Test Scenarios:</h5>
                            <ul>
                                <li>A4 Landscape orientation</li>
                                <li>Grid layout optimization</li>
                                <li>Typography for print</li>
                                <li>Responsive design</li>
                                <li>Print-specific CSS</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Expected Results:</h5>
                            <ul>
                                <li>Full A4 landscape coverage</li>
                                <li>Optimized grid layout</li>
                                <li>Clean, readable typography</li>
                                <li>No content cutoff</li>
                                <li>Professional appearance</li>
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <!-- Test Print Layout -->
                    <div class="bulletin-container">
                        <!-- Header -->
                        <div class="bulletin-header">
                            <div class="school-info">
                                <h1 class="school-title">TEST SCHOOL</h1>
                                <p>123 Test Street, Test City</p>
                                <h2 class="exam-title">TERM EXAM - SAMPLE</h2>
                                <p>NIVEAU SECONDAIRE</p>
                            </div>
                        </div>

                        <!-- Student Details -->
                        <div class="student-details">
                            <div class="student-details-grid">
                                <div class="detail-item"><strong>NOM & PRÉNOMS:</strong> JEAN DUPONT</div>
                                <div class="detail-item"><strong>N° D'ADMISSION:</strong> STD001</div>
                                <div class="detail-item"><strong>CLASSE:</strong> SECONDE A</div>
                                <div class="detail-item"><strong>SECTION:</strong> SCIENCE</div>
                                <div class="detail-item"><strong>TRIMESTRE:</strong> PREMIER TRIMESTRE</div>
                                <div class="detail-item"><strong>ANNÉE ACADÉMIQUE:</strong> 2024</div>
                                <div class="detail-item"><strong>ÂGE:</strong> 16 ans</div>
                                <div class="detail-item"><strong>SEXE:</strong> MASCULIN</div>
                                <div class="detail-item"><strong>DATE DE NAISSANCE:</strong> 15/01/2008</div>
                            </div>
                        </div>

                        <!-- Marks Table -->
                        <table class="marks-table">
                            <thead>
                                <tr>
                                    <th>Matières</th>
                                    <th>DS1 (20)</th>
                                    <th>DS2 (20)</th>
                                    <th>Examen (20)</th>
                                    <th>Moyenne (/20)</th>
                                    <th>Coeff</th>
                                    <th>Total avec Coeff</th>
                                    <th>Remarques</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="subject-name">Mathématiques</td>
                                    <td>16.50</td>
                                    <td>18.00</td>
                                    <td>17.50</td>
                                    <td class="grade">17.33</td>
                                    <td>4</td>
                                    <td class="grade">69.33</td>
                                    <td>Bonne performance</td>
                                </tr>
                                <tr>
                                    <td class="subject-name">Physique-Chimie</td>
                                    <td>15.00</td>
                                    <td>16.50</td>
                                    <td>14.00</td>
                                    <td class="grade">15.17</td>
                                    <td>3</td>
                                    <td class="grade">45.50</td>
                                    <td>Satisfaisant</td>
                                </tr>
                                <tr>
                                    <td class="subject-name">Français</td>
                                    <td>18.00</td>
                                    <td>17.50</td>
                                    <td>19.00</td>
                                    <td class="grade">18.17</td>
                                    <td>4</td>
                                    <td class="grade">72.67</td>
                                    <td>Excellente</td>
                                </tr>
                                <tr>
                                    <td class="subject-name">Anglais</td>
                                    <td>14.50</td>
                                    <td>15.00</td>
                                    <td>16.00</td>
                                    <td class="grade">15.17</td>
                                    <td>2</td>
                                    <td class="grade">30.33</td>
                                    <td>Bonne</td>
                                </tr>
                                <tr>
                                    <td class="subject-name">Histoire-Géographie</td>
                                    <td>17.00</td>
                                    <td>16.50</td>
                                    <td>18.00</td>
                                    <td class="grade">17.17</td>
                                    <td>2</td>
                                    <td class="grade">34.33</td>
                                    <td>Très bien</td>
                                </tr>
                                <tr>
                                    <td class="subject-name">SVT</td>
                                    <td>16.00</td>
                                    <td>17.00</td>
                                    <td>15.50</td>
                                    <td class="grade">16.17</td>
                                    <td>2</td>
                                    <td class="grade">32.33</td>
                                    <td>Bonne</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Summary -->
                        <div class="summary-section">
                            <div class="summary-item">
                                <h4>Total des Points</h4>
                                <p>284.00</p>
                            </div>
                            <div class="summary-item">
                                <h4>Moyenne Générale</h4>
                                <p>16.71/20</p>
                            </div>
                            <div class="summary-item">
                                <h4>Moyenne de la Classe</h4>
                                <p>14.25/20</p>
                            </div>
                            <div class="summary-item">
                                <h4>Position</h4>
                                <p>5/28</p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="footer-section">
                            <div class="comments">
                                <h4>Commentaires</h4>
                                <p><strong>Professeur Principal:</strong> L'élève montre une progression constante dans toutes les matières. Particulièrement fort en mathématiques et français.</p>
                                <p><strong>Proviseur/Directeur:</strong> Bon élève, motivé et travailleur. Recommandation pour participer aux olympiades de mathématiques.</p>
                            </div>
                            <div class="signatures">
                                <h4>Signatures</h4>
                                <div class="signature-line">Parent/Tuteur</div>
                                <div class="signature-line">Prof. Principal</div>
                                <div class="signature-line">Le Proviseur</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Print Optimization Features:</h5>
                            <ul>
                                <li>A4 Landscape orientation</li>
                                <li>Optimized margins (1cm)</li>
                                <li>Professional typography</li>
                                <li>Grid layout optimization</li>
                                <li>Print-specific color scheme</li>
                                <li>Responsive design</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Browser Compatibility:</h5>
                            <ul>
                                <li>Chrome ✓</li>
                                <li>Firefox ✓</li>
                                <li>Safari ✓</li>
                                <li>Edge ✓</li>
                                <li>Mobile browsers ✓</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    /* A4 Landscape Print Optimization */
    @media print {
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        
        * {
            color: #000 !important;
            background: transparent !important;
        }
        
        body {
            background-color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .bulletin-container {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
            box-shadow: none;
            border-radius: 0;
            border: none;
        }
        
        .bulletin-header {
            background: #000 !important;
            color: #fff !important;
            padding: 15px 0;
            text-align: center;
            border-bottom: 2px solid #000 !important;
        }
        
        .school-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 5px 0;
            letter-spacing: 0.5px;
        }
        
        .exam-title {
            font-size: 14px;
            font-weight: 600;
            margin: 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .school-info p {
            margin: 2px 0;
            color: #fff !important;
            font-size: 11px;
        }
        
        .student-details {
            background: #f8f9fa !important;
            padding: 12px;
            border-bottom: 1px solid #000 !important;
        }
        
        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin: 0;
        }
        
        .detail-item {
            background: white;
            padding: 6px 8px;
            border-radius: 4px;
            border-left: 3px solid #000 !important;
            font-size: 10px;
            box-shadow: none;
        }
        
        .detail-item strong {
            color: #000 !important;
            font-weight: 600;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 10px;
            background: white;
        }
        
        .marks-table th {
            background: #000 !important;
            color: #fff !important;
            padding: 6px 4px;
            text-align: center;
            font-weight: 600;
            font-size: 11px;
            border: 1px solid #000 !important;
        }
        
        .marks-table td {
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #000 !important;
            vertical-align: middle;
        }
        
        .marks-table tbody tr:nth-child(even) {
            background: #f8f9fa !important;
        }
        
        .subject-name {
            text-align: left !important;
            font-weight: 600;
            color: #000 !important;
            font-size: 11px;
        }
        
        .grade {
            font-weight: 600;
            color: #000 !important;
            font-size: 11px;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            padding: 12px;
            background: #f8f9fa !important;
        }
        
        .summary-item {
            background: white;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            box-shadow: none;
            border-top: 3px solid #000 !important;
        }
        
        .summary-item h4 {
            margin: 0 0 6px 0;
            color: #000 !important;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-item p {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            color: #000 !important;
        }
        
        .footer-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            padding: 12px;
            background: #ffffff;
            border-top: 2px solid #000 !important;
        }
        
        .comments, .signatures {
            padding: 0;
        }
        
        .comments h4, .signatures h4 {
            color: #000 !important;
            margin: 0 0 8px 0;
            font-size: 11px;
            font-weight: 600;
            padding-bottom: 5px;
            border-bottom: 1px solid #000 !important;
        }
        
        .comments p {
            margin: 0 0 6px 0;
            font-size: 10px;
            line-height: 1.4;
        }
        
        .signature-line {
            height: 35px;
            border-bottom: 1px solid #000 !important;
            margin: 6px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000 !important;
            font-size: 9px;
            font-weight: 500;
        }
        
        .card, .card-header, .card-body, .alert {
            display: none !important;
        }
        
        .container-fluid, .row, .col-12, .col-md-6 {
            display: none !important;
        }
        
        hr {
            display: none !important;
        }
    }
    
    /* Screen-specific styling */
    @media screen {
        body {
            font-family: 'Poppins', 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        
        .bulletin-container {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .bulletin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #3498db;
        }
        
        .school-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }
        
        .exam-title {
            font-size: 20px;
            font-weight: 600;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .school-info p {
            margin: 3px 0;
            color: rgba(255,255,255,0.9);
            font-size: 14px;
        }
        
        .student-details {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 0;
        }
        
        .detail-item {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border-left: 4px solid #3498db;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .detail-item strong {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 13px;
            background: white;
        }
        
        .marks-table th {
            background: #2c3e50;
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            border: none;
        }
        
        .marks-table td {
            padding: 12px 8px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .marks-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .marks-table tbody tr:hover {
            background-color: #e3f2fd;
        }
        
        .subject-name {
            text-align: left !important;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .grade {
            font-weight: 600;
            color: #27ae60;
            font-size: 14px;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .summary-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-top: 4px solid #3498db;
        }
        
        .summary-item h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-item p {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .footer-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 20px;
            background: #ffffff;
            border-top: 2px solid #e9ecef;
        }
        
        .comments, .signatures {
            padding: 0;
        }
        
        .comments h4, .signatures h4 {
            color: #2c3e50;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .comments p {
            margin: 0 0 10px 0;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .signature-line {
            height: 50px;
            border-bottom: 2px solid #bdc3c7;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
            font-size: 13px;
            font-weight: 500;
        }
    }
    
    /* Responsive design for mobile */
    @media (max-width: 768px) {
        .student-details-grid {
            grid-template-columns: 1fr;
        }
        
        .summary-section {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .footer-section {
            grid-template-columns: 1fr;
        }
        
        .marks-table {
            font-size: 11px;
        }
        
        .marks-table th, .marks-table td {
            padding: 8px 4px;
        }
        
        .bulletin-container {
            padding: 0;
            margin: 10px;
        }
        
        .bulletin-header {
            padding: 15px;
        }
        
        .school-title {
            font-size: 20px;
        }
        
        .exam-title {
            font-size: 16px;
        }
    }
    
    @media (max-width: 480px) {
        .summary-section {
            grid-template-columns: 1fr;
        }
        
        .student-details-grid {
            grid-template-columns: 1fr;
        }
        
        .bulletin-container {
            margin: 5px;
        }
        
        .marks-table {
            font-size: 10px;
        }
        
        .marks-table th, .marks-table td {
            padding: 4px 2px;
        }
    }
</style>
@endsection