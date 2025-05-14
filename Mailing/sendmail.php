<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load required libraries
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
require '../phpqrcode/qrlib.php';
require('../fpdf/fpdf.php');

// ---------------------- GET PARAMETERS ------------------------
$email = $_GET['email'] ?? '';
$ticket_code = $_GET['ticket_code'] ?? '';
$event_date = $_GET['event_date'] ?? '';
$event_time = $_GET['event_time'] ?? '';
$concert_name = $_GET['concert_name'] ?? '';
$artist_name = $_GET['artist_name'] ?? '';
$venue = $_GET['venue'] ?? '';
$city = $_GET['city'] ?? '';
$price = $_GET['price'] ?? '';

// ---------------------- QR CODE GENERATION ------------------------
// Instead of generating QR code on server, use Google Chart API
$qr_code_url = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($ticket_code) . '&choe=UTF-8';

// Create directory for tickets if it doesn't exist
if (!file_exists('qrcodes')) {
    mkdir('qrcodes', 0777, true);
}

// Store the ticket code in a text file for reference
$ticket_text_file = 'qrcodes/' . $ticket_code . '.txt';
file_put_contents($ticket_text_file, "Ticket Code: $ticket_code\nThis is your ticket verification code.");

// Flag to indicate we're using online QR code
$using_online_qr = true;

// ---------------------- PDF GENERATION ------------------------
$pdf_file = 'tickets/' . $ticket_code . '.pdf';
if (!file_exists('tickets')) {
    mkdir('tickets', 0777, true);
}

$pdf = new FPDF();
$pdf->AddPage();

// Set document properties
$pdf->SetTitle('Concert Ticket - LiveTheMusic');

// HEADER
$pdf->SetFillColor(76, 175, 80); // green
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 20, 'LiveTheMusic - Ticket', 0, 1, 'C', true);
$pdf->Ln(5);

// TICKET BOX
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetLineWidth(0.5);
$pdf->SetFillColor(245, 245, 245); // light gray
$pdf->Rect(10, $pdf->GetY(), 190, 80, 'DF');
$pdf->Ln(5);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->SetX(15);
$pdf->Cell(0, 10, "🎟 Ticket Code: $ticket_code", 0, 1);
$pdf->SetX(15);
$pdf->Cell(0, 10, "🎤 Concert: $concert_name", 0, 1);
$pdf->SetX(15);
$pdf->Cell(0, 10, "👨‍🎤 Artist: $artist_name", 0, 1);
$pdf->SetX(15);
$pdf->Cell(0, 10, "📅 Date: $event_date at $event_time", 0, 1);
$pdf->SetX(15);
$pdf->Cell(0, 10, "📍 Venue: $venue, $city", 0, 1);
$pdf->SetX(15);
$pdf->Cell(0, 10, "💰 Price: " . number_format($price, 2) . " TND", 0, 1);
$pdf->Ln(10);

// QR CODE SECTION
$pdf->SetFont('Arial', 'B', 12);

// Always show the ticket code prominently
$pdf->Cell(0, 10, 'Your Ticket Verification Code:', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 15, $ticket_code, 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Please present this code at entry', 0, 1, 'C');

// Add a note about the QR code in the email
$pdf->Ln(10);
$pdf->Cell(0, 10, 'A scannable QR code is available in your email', 0, 1, 'C');

$pdf->Output('F', $pdf_file);


// ---------------------- SEND EMAIL ------------------------
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->SMTPDebug  = 0;  // Set to 2 for debug
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->Username   = 'wmejri@affitech.fr';
    $mail->Password   = 'iund katx pvuw gycp';

    $mail->setFrom('chakroun.yassin@esprit.tn', 'LiveTheMusic');
    $mail->addReplyTo('chakroun.yassin@esprit.tn', 'LiveTheMusic');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Votre billet de concert - LiveTheMusic';

    $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
        <div style='background-color: #4CAF50; color: white; padding: 20px; text-align: center;'>
            <h2 style='margin: 0;'>🎫 LiveTheMusic - Votre Ticket</h2>
        </div>
        <div style='padding: 20px;'>
            <p>Bonjour,</p>
            <p>Merci pour votre achat ! Voici votre ticket pour le concert :</p>
            <div style='border: 1px dashed #ccc; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                <p><strong>Code du ticket :</strong> <span style='font-size: 18px; color: #4CAF50;'>$ticket_code</span></p>
                <p><strong>Date :</strong> {$event_date} à {$event_time}</p>
                <p><strong>Concert :</strong> {$concert_name} - {$artist_name}</p>
                <p><strong>Lieu :</strong> {$venue}, {$city}</p>
                <p><strong>Prix :</strong> {$price} TND</p>
            </div>
            <p>Veuillez présenter ce code à l'entrée :</p>
            <div style='text-align: center; margin: 20px 0;'>
                <!-- Ticket code in large text -->
                <div style='font-size: 24px; font-weight: bold; padding: 20px; background-color: #f5f5f5; border-radius: 10px; margin-bottom: 20px;'>
                    $ticket_code
                </div>
                
                <!-- QR code from Google Chart API -->
                <p>Ou scannez ce code QR :</p>
                <img src='$qr_code_url' alt='QR Code' style='width: 200px; height: 200px;' />
            </div>
            <p style='font-size: 12px; color: #999;'>Ce billet est nominatif et valable pour une seule personne.</p>
        </div>
        <div style='background-color: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; color: #666;'>
            &copy; " . date('Y') . " LiveTheMusic. Tous droits réservés.
        </div>
    </div>";

    // Add the ticket text file as attachment
    $mail->addAttachment($ticket_text_file, 'Ticket_' . $ticket_code . '.txt');
    
    // Always add the PDF ticket
    $mail->addAttachment($pdf_file, 'LiveTheMusic_Ticket_' . $ticket_code . '.pdf');

    $mail->send();

    echo "<script>alert('Vérifiez votre boîte mail ! Votre billet a été envoyé à " . htmlspecialchars($email) . "'); window.location.href='../view/front_office/events.php';</script>";

} catch (Exception $e) {
    echo "Erreur lors de l'envoi du mail : " . $mail->ErrorInfo;
}
?>
