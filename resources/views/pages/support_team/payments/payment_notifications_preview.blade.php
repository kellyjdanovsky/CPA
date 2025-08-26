@extends('layouts.master')
@section('page_title', 'Aperçu - Avis de Paiement')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-eye mr-2"></i>Aperçu - Avis de Paiement - {{ $class_name }}</h5>
            <div class="header-elements">
                <div class="btn-group">
                    <button onclick="printPreview()" class="btn btn-success btn-sm">
                        <i class="icon-printer mr-2"></i>Imprimer
                    </button>
                    <button onclick="downloadPDF()" class="btn btn-primary btn-sm">
                        <i class="icon-file-pdf mr-2"></i>Télécharger PDF
                    </button>
                    <a href="{{ route('payments.verified') }}" class="btn btn-secondary btn-sm">
                        <i class="icon-arrow-left8 mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Information Summary -->
            <div class="alert alert-info m-3">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Classe :</strong> {{ $class_name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Nombre d'élèves :</strong> {{ count($unpaid_students) }}
                    </div>
                    <div class="col-md-3">
                        <strong>Date limite :</strong> {{ date('d/m/Y', strtotime($payment_deadline)) }}
                    </div>
                    <div class="col-md-3">
                        <strong>Pages :</strong> {{ ceil(count($unpaid_students) / 10) }}
                    </div>
                </div>
                <div class="layout-info mt-2">
                    <i class="icon-grid mr-2"></i>
                    Disposition optimisée : <strong>10 élèves par page A4</strong> (2 colonnes × 5 rangées)
                </div>
                <!-- Debug Information -->
                <div class="mt-2" style="font-size: 11px; color: #666;">
                    <strong>Debug Info:</strong> 
                    Classe ID: {{ request()->input('my_class_id') }} | 
                    Paiements: {{ implode(',', request()->input('my_payments_id', [])) }} | 
                    Statuts: {{ implode(',', request()->input('status', ['Normal', 'ADRA'])) }}
                </div>
            </div>

            <!-- Preview Container -->
            <div id="preview-container" style="background: #f5f5f5; padding: 20px;">
                <div style="max-width: 210mm; margin: 0 auto; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <!-- Include the original payment notifications template -->
                    @include('pages.support_team.payments.payment_notifications_content')
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for PDF download - Completely removed as we use dynamic form generation -->

    <!-- Print-specific stylesheet -->
    <!-- Print-optimized styles for 2x5 layout -->
    <style media="print">
        @page {
            size: A4 portrait;
            margin: 3mm;
        }
        
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        /* Hide all interface elements */
        .navbar, .sidebar, .card-header, .alert, .btn-group, .header-elements {
            display: none !important;
        }
        
        /* Show only the main content */
        html, body {
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            overflow: visible !important;
        }
        
        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .card, .card-body {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
        }
        
        #preview-container {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            width: 100% !important;
            height: auto !important;
            max-width: none !important;
            box-shadow: none !important;
        }
        
        #preview-container > div {
            background: white !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }
        
        /* Ensure notifications body is fully visible */
        .notifications-body {
            display: block !important;
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            font-family: Arial, sans-serif !important;
            font-size: 8px !important;
            line-height: 1.2 !important;
            color: black !important;
        }
        
        /* Grid layout for 2x5 */
        .page {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            grid-template-rows: repeat(5, 1fr) !important;
            gap: 2mm !important;
            width: 204mm !important;
            height: 287mm !important;
            margin: 0 auto !important;
            padding: 0 !important;
            page-break-after: always !important;
            page-break-inside: avoid !important;
            background: white !important;
        }
        
        .page:last-child {
            page-break-after: avoid !important;
        }
        
        /* Individual notifications */
        .notification {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            height: 100% !important;
            border: 2px solid #2c3e50 !important;
            border-radius: 8px !important;
            padding: 3mm !important;
            background: white !important;
            color: #2c3e50 !important;
            position: relative !important;
            page-break-inside: avoid !important;
            font-size: 8px !important;
            line-height: 1.4 !important;
            justify-content: space-between !important;
            box-shadow: none !important;
        }
        
        /* Explicit grid positioning */
        .notification:nth-child(1) { grid-column: 1; grid-row: 1; }
        .notification:nth-child(2) { grid-column: 2; grid-row: 1; }
        .notification:nth-child(3) { grid-column: 1; grid-row: 2; }
        .notification:nth-child(4) { grid-column: 2; grid-row: 2; }
        .notification:nth-child(5) { grid-column: 1; grid-row: 3; }
        .notification:nth-child(6) { grid-column: 2; grid-row: 3; }
        .notification:nth-child(7) { grid-column: 1; grid-row: 4; }
        .notification:nth-child(8) { grid-column: 2; grid-row: 4; }
        .notification:nth-child(9) { grid-column: 1; grid-row: 5; }
        .notification:nth-child(10) { grid-column: 2; grid-row: 5; }
        
        /* Content sections */
        .header {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-bottom: 1px solid #e0e6ed !important;
            padding: 2mm !important;
            background: #f8fafc !important;
            border-radius: 8px !important;
            height: auto !important;
        }
        
        .notification-title {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 1px solid #cbd5e1 !important;
            background: #f1f5f9 !important;
            padding: 1.5mm !important;
            margin: 1.5mm 0 !important;
            text-align: center !important;
            font-weight: bold !important;
            border-radius: 6px !important;
        }
        
        .content {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 1.5mm !important;
        }
        
        .student-info {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 1.5mm !important;
        }
        
        .payment-info {
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 1.5mm !important;
            flex: 1 !important;
        }
        
        .amount-list {
            margin-top: 1mm !important;
        }
        
        .amount-item {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 0.8mm !important;
            margin-bottom: 0.5mm !important;
            background: #f8fafc !important;
            border-radius: 4px !important;
            font-size: 6px !important;
        }
        
        .amount-item.total {
            background: #fef3c7 !important;
            border: 1px solid #f59e0b !important;
            font-weight: bold !important;
        }
        
        .deadline-info {
            background: #fef3c7 !important;
            border: 1px solid #f59e0b !important;
            border-radius: 8px !important;
            padding: 1.5mm !important;
            text-align: center !important;
        }
        
        .deadline-date {
            font-size: 7px !important;
            font-weight: bold !important;
            color: #92400e !important;
            background: white !important;
            padding: 0.8mm 1.5mm !important;
            border-radius: 4px !important;
            border: 1px solid #f59e0b !important;
            display: inline-block !important;
        }
        
        .footer-section {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: #f8fafc !important;
            border-top: 1px solid #e2e8f0 !important;
            border-radius: 0 0 8px 8px !important;
            margin: 0 -3mm -3mm -3mm !important;
            padding: 1mm 3mm !important;
        }

    </style>

    <style>
        /* Print-specific styles - Simplified for better compatibility */
        @media print {
            /* Hide all interface elements */
            .navbar, .sidebar, .card-header, .alert, .btn-group, .header-elements {
                display: none !important;
            }
            
            /* Page setup for A4 portrait */
            @page {
                size: A4 portrait;
                margin: 5mm;
            }
            
            /* Reset everything for print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Body setup */
            html, body {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                color: black !important;
                font-family: Arial, sans-serif !important;
                font-size: 8px !important;
                line-height: 1.2 !important;
            }
            
            /* Make sure only the preview container is visible */
            body * {
                visibility: hidden !important;
            }
            
            #preview-container,
            #preview-container *,
            .notifications-body,
            .notifications-body * {
                visibility: visible !important;
            }
            
            /* Card and container reset */
            .card, .card-body {
                background: white !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Preview container */
            #preview-container {
                display: block !important;
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
            
            /* Notifications body */
            .notifications-body {
                display: block !important;
                width: 100% !important;
                background: white !important;
                color: black !important;
            }
            
            /* Page layout - explicit grid */
            .page {
                display: block !important;
                width: 200mm !important;
                min-height: 280mm !important;
                margin: 0 auto 5mm !important;
                padding: 2mm !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
                background: white !important;
                position: relative !important;
            }
            
            .page:last-child {
                page-break-after: avoid !important;
            }
            
            /* Individual notifications - Use float layout for better print compatibility */
            .notification {
                display: block !important;
                float: left !important;
                width: 95mm !important;
                height: 54mm !important;
                margin: 1mm !important;
                border: 2px solid #2c3e50 !important;
                border-radius: 8px !important;
                padding: 3mm !important;
                background: white !important;
                color: #2c3e50 !important;
                position: relative !important;
                page-break-inside: avoid !important;
                box-sizing: border-box !important;
                overflow: visible !important;
                font-size: 8px !important;
                line-height: 1.3 !important;
            }
            
            /* Clear float every 2 notifications (for 2-column layout) */
            .notification:nth-child(2n+1) {
                clear: left !important;
            }
            
            /* Ensure all notification content is visible */
            .notification * {
                visibility: visible !important;
                color: black !important;
                background: transparent !important;
            }
            
            /* Header section */
            .notification .header {
                display: block !important;
                border-bottom: 1px solid black !important;
                padding-bottom: 1mm !important;
                margin-bottom: 1mm !important;
                height: 10mm !important;
                text-align: center !important;
            }
            
            /* School info */
            .notification .school-info {
                font-size: 4px !important;
                line-height: 1 !important;
            }
            
            /* Title */
            .notification .notification-title {
                display: block !important;
                border: 1px solid black !important;
                padding: 1mm !important;
                margin: 1mm 0 !important;
                text-align: center !important;
                font-weight: bold !important;
                font-size: 5px !important;
                height: 4mm !important;
            }
            
            /* Content area */
            .notification .content {
                display: block !important;
                font-size: 6px !important;
                line-height: 1.4 !important;
                padding: 1mm !important;
                background: #fafafa !important;
                border-radius: 1mm !important;
                min-height: 20mm !important;
                overflow: visible !important;
            }
            
            /* Malagasy greeting */
            .notification .malagasy-greeting {
                font-size: 5px !important;
                margin-bottom: 1mm !important;
                font-weight: 600 !important;
                display: block !important;
            }
            
            /* Payment notice */
            .notification .payment-notice {
                font-size: 5.5px !important;
                margin: 1.2mm 0 !important;
                padding: 1mm !important;
                border-left: 2px solid #2196f3 !important;
                padding-left: 1.5mm !important;
                background: #f3f8ff !important;
                border-radius: 1mm !important;
                line-height: 1.4 !important;
            }
            
            /* Student info */
            .notification .student-name {
                font-weight: bold !important;
                font-size: 7px !important;
                margin-bottom: 0.5mm !important;
                color: #d32f2f !important;
                border-left: 2px solid #d32f2f !important;
                padding-left: 1mm !important;
            }
            
            .notification .class-info {
                font-size: 5px !important;
                margin-bottom: 1mm !important;
            }
            
            /* Payment details */
            .notification .payment-details {
                border: 2px solid black !important;
                border-radius: 1mm !important;
                padding: 1.5mm !important;
                margin: 1mm 0 !important;
                font-size: 5px !important;
                background: #fff3e0 !important;
            }
            
            /* Deadline */
            .notification .deadline {
                border: 2px solid #f57c00 !important;
                border-radius: 1mm !important;
                padding: 1.5mm !important;
                margin: 1mm 0 !important;
                text-align: center !important;
                font-size: 5px !important;
                font-weight: bold !important;
                background: #fff8e1 !important;
            }
            
            /* Thanks and Bible verse */
            .notification .malagasy-thanks {
                font-style: italic !important;
                text-align: center !important;
                font-size: 4px !important;
                margin: 1mm 0 !important;
                border-top: 1px solid black !important;
                padding-top: 0.5mm !important;
                position: absolute !important;
                bottom: 8mm !important;
                left: 2mm !important;
                right: 2mm !important;
            }
            
            .notification .bible-verse {
                font-style: italic !important;
                text-align: center !important;
                font-size: 3.5px !important;
                color: #1565c0 !important;
                background: #e8f5e8 !important;
                border: 1px solid #4caf50 !important;
                border-radius: 1mm !important;
                padding: 0.5mm !important;
                position: absolute !important;
                bottom: 3mm !important;
                left: 2mm !important;
                right: 2mm !important;
            }
            
            /* Footer */
            .notification .footer {
                position: absolute !important;
                bottom: 1mm !important;
                left: 2mm !important;
                right: 2mm !important;
                font-size: 3px !important;
                border-top: 1px solid black !important;
                padding-top: 0.5mm !important;
            }
            
            /* Logo */
            .school-logo {
                width: 12px !important;
                height: 12px !important;
            }
            
            /* Clear float after pages */
            .page::after {
                content: "" !important;
                display: table !important;
                clear: both !important;
            }
        }
        
        /* Additional print class for forced printing */
        @media print {
            .printing .notifications-body {
                display: block !important;
                visibility: visible !important;
            }
            
            .printing .notification {
                display: block !important;
                visibility: visible !important;
                float: left !important;
                width: 95mm !important;
                height: 54mm !important;
                margin: 1mm !important;
                border: 3px solid black !important;
                border-radius: 2mm !important;
                padding: 3mm !important;
                background: white !important;
                color: black !important;
                page-break-inside: avoid !important;
                box-sizing: border-box !important;
                font-size: 8px !important;
                line-height: 1.3 !important;
            }
            
            .printing .notification:nth-child(2n+1) {
                clear: left !important;
            }
            
            .printing .page {
                display: block !important;
                width: 200mm !important;
                min-height: 280mm !important;
                margin: 0 auto 5mm !important;
                padding: 2mm !important;
                page-break-after: always !important;
                background: white !important;
            }
            
            .printing .page:last-child {
                page-break-after: avoid !important;
            }
        }
        
        /* Screen styles for preview */
            
            /* Logo visibility */
            .school-logo {
                display: block !important;
                visibility: visible !important;
                width: 20px !important;
                height: 20px !important;
            }
            
            /* Remove page break from page-break divs */
            .page-break {
                display: none !important;
            }
            
            /* Force all elements to be printable */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
        
        /* Preview styles - Optimisé pour la disposition 2x5 */
        #preview-container {
            overflow-x: auto;
            background: #f0f0f0;
            padding: 20px;
            min-height: 400px;
        }
        
        .preview-page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 30px;
            background: white;
            position: relative;
            border: 2px solid #ddd;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 4px;
            overflow: hidden;
        }
        
        /* Message informatif pour la disposition */
        .layout-info {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            padding: 10px;
            margin: 10px;
            border-radius: 4px;
            font-size: 12px;
            text-align: center;
            color: #1976d2;
            font-weight: bold;
        }
        
        /* Scale down for preview - Optimisé pour 10 éléments */
        @media screen {
            .preview-page {
                transform: scale(0.75); /* Échelle réduite pour meilleure visibilité */
                transform-origin: top center;
                margin-bottom: -80mm; /* Ajustement de l'espace négatif */
            }
            
            /* Mise en évidence de la grille dans l'aperçu */
            .notifications-body .page {
                outline: 2px solid #2196f3;
                outline-offset: -2px;
            }
            
            .notifications-body .notification {
                transition: all 0.2s ease;
            }
            
            .notifications-body .notification:hover {
                transform: scale(1.02);
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                z-index: 10;
            }
        }
    </style>

    <script>
        function printPreview() {
            console.log('Starting print process...');
            
            // Get the notification content
            const notificationsBody = document.querySelector('.notifications-body');
            
            if (!notificationsBody) {
                alert('Contenu d\'impression introuvable. Veuillez actualiser la page et ressayer.');
                return;
            }
            
            // Count notifications
            const notifications = notificationsBody.querySelectorAll('.notification');
            console.log(`Found ${notifications.length} notifications to print`);
            
            if (notifications.length === 0) {
                alert('Aucun avis de paiement à imprimer. Veuillez vérifier les données.');
                return;
            }
            
            // Store original classes
            const originalBodyClass = document.body.className;
            const originalHtmlClass = document.documentElement.className;
            
            // Add print class to body to trigger print styles
            document.body.className = originalBodyClass + ' printing';
            document.documentElement.className = originalHtmlClass + ' printing';
            
            // Force all notifications to be visible
            notifications.forEach((notification, index) => {
                notification.style.visibility = 'visible';
                notification.style.display = 'block';
                notification.style.opacity = '1';
                notification.style.color = 'black';
                notification.style.background = 'white';
                notification.style.float = 'left';
                notification.style.width = '95mm';
                notification.style.height = '54mm';
                notification.style.margin = '1mm';
                notification.style.border = '2px solid black';
                notification.style.padding = '2mm';
                notification.style.pageBreakInside = 'avoid';
                notification.style.boxSizing = 'border-box';
                
                // Clear float every 2 notifications
                if ((index + 1) % 2 === 1) {
                    notification.style.clear = 'left';
                }
                
                console.log(`Prepared notification ${index + 1} for printing`);
            });
            
            // Force pages to display properly
            const pages = notificationsBody.querySelectorAll('.page');
            pages.forEach((page, pageIndex) => {
                page.style.display = 'block';
                page.style.width = '200mm';
                page.style.minHeight = '280mm';
                page.style.margin = '0 auto 5mm';
                page.style.padding = '2mm';
                page.style.pageBreakAfter = 'always';
                page.style.background = 'white';
                
                // Add clearfix
                const clearfix = document.createElement('div');
                clearfix.style.clear = 'both';
                clearfix.style.display = 'table';
                page.appendChild(clearfix);
                
                console.log(`Configured page ${pageIndex + 1} for printing`);
            });
            
            // Ensure the last page doesn't have page break
            if (pages.length > 0) {
                pages[pages.length - 1].style.pageBreakAfter = 'avoid';
            }
            
            console.log('Triggering print dialog...');
            
            // Small delay to ensure styles are applied
            setTimeout(() => {
                try {
                    window.print();
                } catch (error) {
                    console.error('Print failed:', error);
                    // Restore original classes
                    document.body.className = originalBodyClass;
                    document.documentElement.className = originalHtmlClass;
                    // Try fallback method
                    fallbackPrint();
                    return;
                }
                
                // Restore original classes after print dialog
                setTimeout(() => {
                    document.body.className = originalBodyClass;
                    document.documentElement.className = originalHtmlClass;
                }, 1000);
            }, 300);
        }
        
        function fallbackPrint() {
            console.log('Using fallback print method...');
            
            // Get the notifications content
            const notificationsBody = document.querySelector('.notifications-body');
            if (!notificationsBody) {
                alert('Contenu d\'impression introuvable.');
                return;
            }
            
            // Create a new window for printing
            const printWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes');
            
            if (!printWindow) {
                alert('Fenêtre popup bloquée. Veuillez autoriser les popups pour ce site et réessayer.');
                return;
            }
            
            // Create the print document with enhanced styles
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Avis de Paiement</title>
                    <meta charset="UTF-8">
                    <style>
                        @page {
                            size: A4 portrait;
                            margin: 5mm;
                        }
                        
                        * {
                            box-sizing: border-box;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: Arial, sans-serif;
                            font-size: 8px;
                            line-height: 1.2;
                            color: black;
                            background: white;
                        }
                        
                        .notifications-body {
                            width: 100%;
                            background: white;
                        }
                        
                        .page {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            grid-template-rows: repeat(5, 1fr);
                            gap: 2mm;
                            width: 200mm;
                            height: 280mm;
                            margin: 0 auto 5mm;
                            padding: 2mm;
                            page-break-after: always;
                            page-break-inside: avoid;
                            background: white;
                        }
                        
                        .page:last-child {
                            page-break-after: avoid;
                        }
                        
                        .notification {
                            display: flex;
                            flex-direction: column;
                            width: 100%;
                            height: 100%;
                            border: 2px solid black;
                            padding: 2mm;
                            background: white;
                            color: black;
                            position: relative;
                            justify-content: space-between;
                            overflow: hidden;
                        }
                        
                        .header {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border-bottom: 2px solid black;
                            height: 12mm;
                            padding: 1mm;
                            margin-bottom: 1mm;
                        }
                        
                        .notification-title {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border: 2px solid black;
                            padding: 1mm;
                            margin: 1mm 0;
                            text-align: center;
                            font-weight: bold;
                            height: 6mm;
                            font-size: 6px;
                        }
                        
                        .content {
                            flex: 1;
                            font-size: 6px;
                        }
                        
                        .payment-details {
                            border: 2px solid black;
                            padding: 1mm;
                            margin: 1mm 0;
                            background: white;
                            font-size: 5px;
                        }
                        
                        .deadline {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border: 2px solid black;
                            padding: 1mm;
                            margin: 1mm 0;
                            font-weight: bold;
                            text-align: center;
                            min-height: 5mm;
                            font-size: 5px;
                        }
                        
                        .malagasy-thanks {
                            border-top: 1px solid black;
                            border-bottom: 1px solid black;
                            padding: 0.5mm;
                            margin: 1mm 0;
                            font-style: italic;
                            text-align: center;
                            font-size: 5px;
                        }
                        
                        .footer {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            border-top: 1px solid black;
                            padding: 0.5mm;
                            position: absolute;
                            bottom: 1mm;
                            left: 2mm;
                            right: 2mm;
                            font-size: 4px;
                        }
                        
                        .school-logo {
                            width: 15px;
                            height: 15px;
                        }
                        
                        .school-info {
                            text-align: center;
                            font-size: 4px;
                        }
                        
                        .student-name {
                            font-weight: bold;
                            font-size: 6px;
                        }
                        
                        .class-info {
                            font-size: 5px;
                        }
                        
                        .payment-notice {
                            margin: 1mm 0;
                            font-size: 5px;
                        }
                        
                        .malagasy-greeting {
                            font-size: 5px;
                            margin-bottom: 0.5mm;
                        }
                    </style>
                </head>
                <body>
                    ${notificationsBody.outerHTML}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            
            // Wait for the content to load then print
            setTimeout(() => {
                printWindow.focus();
                printWindow.print();
                
                // Close the window after printing
                setTimeout(() => {
                    printWindow.close();
                }, 1000);
            }, 1000);
        }
        
        function downloadPDF() {
            console.log('Starting PDF download...');
            
            // Get current form data from the request - ensuring we have all the data
            const formData = {
                my_class_id: @json(request()->input('my_class_id') ?: request()->get('my_class_id', '')),
                payment_deadline: @json($payment_deadline ?? ''),
                my_payments_id: @json(request()->input('my_payments_id', []) ?: request()->get('my_payments_id', [])),
                status: @json(request()->input('status') ?: request()->get('status') ?: ['Normal', 'ADRA'])
            };
            
            console.log('Form data:', formData);
            console.log('Class ID:', formData.my_class_id);
            console.log('Payment IDs:', formData.my_payments_id);
            console.log('Status:', formData.status);
            console.log('Deadline:', formData.payment_deadline);
            
            // Validate data
            if (!formData.my_class_id || !formData.my_payments_id || formData.my_payments_id.length === 0) {
                console.error('Missing form data:', formData);
                alert('Données de formulaire manquantes. ' +
                      'Classe ID: ' + formData.my_class_id + ' | ' +
                      'Paiements: ' + JSON.stringify(formData.my_payments_id) + ' | ' +
                      'Veuillez retourner à la page précédente et ressayer.');
                return;
            }
            
            // Create and submit form dynamically
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("payments.generate_notifications") }}';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add class ID
            const classInput = document.createElement('input');
            classInput.type = 'hidden';
            classInput.name = 'my_class_id';
            classInput.value = formData.my_class_id;
            form.appendChild(classInput);
            
            // Add payment deadline
            const deadlineInput = document.createElement('input');
            deadlineInput.type = 'hidden';
            deadlineInput.name = 'payment_deadline';
            deadlineInput.value = formData.payment_deadline;
            form.appendChild(deadlineInput);
            
            // Add action
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'download';
            form.appendChild(actionInput);
            
            // Add payment IDs
            if (Array.isArray(formData.my_payments_id)) {
                formData.my_payments_id.forEach(function(paymentId) {
                    if (paymentId) { // Only add non-empty payment IDs
                        const paymentInput = document.createElement('input');
                        paymentInput.type = 'hidden';
                        paymentInput.name = 'my_payments_id[]';
                        paymentInput.value = paymentId;
                        form.appendChild(paymentInput);
                    }
                });
            }
            
            // Add statuses
            if (Array.isArray(formData.status)) {
                formData.status.forEach(function(status) {
                    if (status) { // Only add non-empty statuses
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status[]';
                        statusInput.value = status;
                        form.appendChild(statusInput);
                    }
                });
            }
            
            // Submit form
            document.body.appendChild(form);
            console.log('Submitting form for PDF download with form elements:');
            console.log('Form action:', form.action);
            console.log('Form elements:', form.elements.length);
            
            // Debug: Log all form data before submission
            const formDataForDebug = new FormData(form);
            for (let [key, value] of formDataForDebug) {
                console.log('Form field:', key, '=', value);
            }
            
            form.submit();
            
            // Clean up after a delay
            setTimeout(() => {
                if (document.body.contains(form)) {
                    document.body.removeChild(form);
                }
            }, 2000);
        }
    </script>
@endsection