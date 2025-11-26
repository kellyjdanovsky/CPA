<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test A4 Landscape Printing</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        
        .test-container {
            font-family: 'Poppins', 'Roboto', sans-serif;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .test-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #3498db;
        }
        
        .test-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }
        
        .test-subtitle {
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
        }
        
        .test-content {
            padding: 30px;
        }
        
        .test-section {
            margin-bottom: 30px;
        }
        
        .test-section h3 {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .test-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .test-item h4 {
            color: #2c3e50;
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 8px 0;
        }
        
        .test-item p {
            color: #7f8c8d;
            font-size: 12px;
            margin: 0;
        }
        
        .test-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .test-table th {
            background: #2c3e50;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .test-table td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
        }
        
        .test-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .test-actions {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .test-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
            margin: 0 10px;
        }
        
        .test-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .test-btn.secondary {
            background: #95a5a6;
        }
        
        .test-btn.secondary:hover {
            background: #7f8c8d;
        }
        
        @media print {
            body * {
                color: #000 !important;
                background: transparent !important;
            }
            
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            
            .test-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .test-header {
                background: #000 !important;
                border-bottom: 2px solid #000 !important;
            }
            
            .test-table th {
                background: #000 !important;
                color: #fff !important;
            }
            
            .test-table tbody tr:nth-child(even) {
                background: #f5f5f5 !important;
            }
            
            .test-item {
                border-left: 3px solid #000 !important;
            }
            
            .test-section h3 {
                border-bottom: 2px solid #000 !important;
            }
            
            .test-actions {
                display: none;
            }
            
            .test-table {
                font-size: 11px;
            }
            
            .test-table th, .test-table td {
                padding: 8px 5px;
            }
        }
        
        @media (max-width: 768px) {
            .test-grid {
                grid-template-columns: 1fr;
            }
            
            .test-table {
                font-size: 11px;
            }
            
            .test-table th, .test-table td {
                padding: 8px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1 class="test-title">Test A4 Landscape Printing</h1>
            <p class="test-subtitle">Verify that the report card prints correctly in A4 landscape format</p>
        </div>
        
        <div class="test-content">
            <div class="test-section">
                <h3>Layout Test</h3>
                <div class="test-grid">
                    <div class="test-item">
                        <h4>Header Section</h4>
                        <p>Testing header layout and styling</p>
                    </div>
                    <div class="test-item">
                        <h4>Grid Layout</h4>
                        <p>Responsive grid system</p>
                    </div>
                    <div class="test-item">
                        <h4>Typography</h4>
                        <p>Font sizes and weights</p>
                    </div>
                    <div class="test-item">
                        <h4>Colors</h4>
                        <p>Color scheme consistency</p>
                    </div>
                </div>
            </div>
            
            <div class="test-section">
                <h3>Table Test</h3>
                <table class="test-table">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>DS1 (20)</th>
                            <th>DS2 (20)</th>
                            <th>Examen (20)</th>
                            <th>Moyenne (/20)</th>
                            <th>Coeff</th>
                            <th>Total</th>
                            <th>Appréciations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="subject-name">Mathématiques</td>
                            <td>15.5</td>
                            <td>16.0</td>
                            <td>14.5</td>
                            <td class="grade">15.3</td>
                            <td>3</td>
                            <td class="grade">46.0</td>
                            <td>Bonne performance</td>
                        </tr>
                        <tr>
                            <td class="subject-name">Français</td>
                            <td>14.0</td>
                            <td>13.5</td>
                            <td>15.0</td>
                            <td class="grade">14.2</td>
                            <td>4</td>
                            <td class="grade">56.8</td>
                            <td>Satisfaisant</td>
                        </tr>
                        <tr>
                            <td class="subject-name">Anglais</td>
                            <td>16.5</td>
                            <td>17.0</td>
                            <td>16.0</td>
                            <td class="grade">16.5</td>
                            <td>2</td>
                            <td class="grade">33.0</td>
                            <td>Excellente</td>
                        </tr>
                        <tr>
                            <td class="subject-name">Histoire-Géo</td>
                            <td>13.0</td>
                            <td>14.0</td>
                            <td>12.5</td>
                            <td class="grade">13.2</td>
                            <td>2</td>
                            <td class="grade">26.4</td>
                            <td>À améliorer</td>
                        </tr>
                        <tr>
                            <td class="subject-name">Sciences</td>
                            <td>15.0</td>
                            <td>14.5</td>
                            <td>16.0</td>
                            <td class="grade">15.2</td>
                            <td>3</td>
                            <td class="grade">45.6</td>
                            <td>Bonne</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="test-section">
                <h3>Summary Test</h3>
                <div class="test-grid">
                    <div class="test-item">
                        <h4>Total des Points</h4>
                        <p>207.8</p>
                    </div>
                    <div class="test-item">
                        <h4>Moyenne Générale</h4>
                        <p>14.8/20</p>
                    </div>
                    <div class="test-item">
                        <h4>Moyenne de la Classe</h4>
                        <p>13.2/20</p>
                    </div>
                    <div class="test-item">
                        <h4>Position</h4>
                        <p>5/25</p>
                    </div>
                </div>
            </div>
            
            <div class="test-actions">
                <button class="test-btn" onclick="window.print()">🖨️ Imprimer le test</button>
                <button class="test-btn secondary" onclick="window.close()">Fermer</button>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-print after 2 seconds for testing
        setTimeout(function() {
            if (confirm('Automatically print test page?')) {
                window.print();
            }
        }, 2000);
    </script>
</body>
</html>