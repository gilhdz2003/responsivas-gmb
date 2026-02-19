<?php
/**
 * GMB Responsivas - Notifier
 *
 * Sistema de notificaciones por email
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/database.php';

class Notifier {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    private function configureMailer() {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@grupomb.com';
        $mail->Password = ''; // Configurar
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('noreply@grupomb.com', 'GMB Responsivas');
        return $mail;
    }

    public function sendEmail($to, $subject, $body, $attachments = []) {
        try {
            $mail = $this->configureMailer();
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->isHTML(true);

            foreach ($attachments as $file) {
                $mail->addAttachment($file);
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $e->getMessage());
            return false;
        }
    }

    public function notifyNuevaResponsiva($responsivaId) {
        $responsiva = $this->db->fetchOne("
            SELECT r.*, e.nombre as empleado_nombre, u.email,
                   eq.tipo, eq.marca, eq.modelo
            FROM responsivas r
            JOIN empleados e ON r.empleado_id = e.id
            JOIN usuarios u ON e.usuario_id = u.id
            JOIN equipos eq ON r.equipo_id = eq.id
            WHERE r.id = :id
        ", ['id' => $responsivaId]);

        if (!$responsiva || !$responsiva['email']) {
            return false;
        }

        $subject = 'Nueva Responsiva Pendiente de Firma - GMB';
        $body = $this->getTemplateNuevaResponsiva($responsiva);
        return $this->sendEmail($responsiva['email'], $subject, $body);
    }

    public function notifyResponsivaFirmada($responsivaId) {
        $responsiva = $this->db->fetchOne("
            SELECT r.*, e.nombre as empleado_nombre,
                   eq.tipo, eq.marca, eq.modelo, eq.numero_serie,
                   s.nombre as sucursal_nombre
            FROM responsivas r
            JOIN empleados e ON r.empleado_id = e.id
            JOIN equipos eq ON r.equipo_id = eq.id
            JOIN sucursales s ON r.sucursal_id = s.id
            WHERE r.id = :id
        ", ['id' => $responsivaId]);

        if (!$responsiva) {
            return false;
        }

        $admins = $this->db->fetchAll("SELECT email FROM usuarios WHERE rol = 'admin' AND activo = 1");
        if (empty($admins)) {
            return false;
        }

        $subject = 'Responsiva Firmada - ' . $responsiva['empleado_nombre'];
        $body = $this->getTemplateResponsivaFirmada($responsiva);

        $pdfPath = __DIR__ . '/../uploads/responsivas/responsiva_' . $responsivaId . '.pdf';
        $attachments = file_exists($pdfPath) ? [$pdfPath] : [];

        foreach ($admins as $admin) {
            $this->sendEmail($admin['email'], $subject, $body, $attachments);
        }

        return true;
    }

    private function getTemplateNuevaResponsiva($r) {
        return '
        <div style="font-family: Arial, sans-serif; max-width:600px; margin:0 auto;">
            <div style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding:30px; text-align:center; border-radius:10px 10px 0 0;">
                <h1 style="color:white; margin:0;">GMB Responsivas</h1>
            </div>
            <div style="background:#f9fafb; padding:30px; border:1px solid #e5e7eb;">
                <h2 style="color:#1f2937; margin-top:0;">Nueva Responsiva Pendiente</h2>
                <p>Hola <strong>' . htmlspecialchars($r['empleado_nombre']) . '</strong>,</p>
                <p>Tienes una nueva responsiva de <strong>' . ucfirst(htmlspecialchars($r['tipo'])) . '</strong> por firmar.</p>
                <div style="background:#fef3c7; padding:15px; margin:20px 0; border-left:4px solid #fbbf24;">
                    <p style="margin:5px 0;"><strong>Equipo:</strong> ' . htmlspecialchars($r['marca'] . ' ' . $r['modelo']) . '</p>
                </div>
                <p>Por favor ingresa al sistema para firmarla:</p>
                <p><a href="https://responsivas.grupomb.com/empleado/dashboard.php" style="display:inline-block; background:#3b82f6; color:white; padding:12px 24px; text-decoration:none; border-radius:6px;">Firmar Responsiva</a></p>
            </div>
            <div style="background:#1f2937; color:#9ca3af; padding:20px; text-align:center; border-radius:0 0 10px 10px; font-size:12px;">
                <p>&copy; ' . date('Y') . ' Grupo MB. Todos los derechos reservados.</p>
            </div>
        </div>';
    }

    private function getTemplateResponsivaFirmada($r) {
        return '
        <div style="font-family: Arial, sans-serif; max-width:600px; margin:0 auto;">
            <div style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding:30px; text-align:center; border-radius:10px 10px 0 0;">
                <h1 style="color:white; margin:0;">GMB Responsivas</h1>
            </div>
            <div style="background:#f9fafb; padding:30px; border:1px solid #e5e7eb;">
                <h2 style="color:#1f2937; margin-top:0;">Responsiva Firmada</h2>
                <p>El empleado <strong>' . htmlspecialchars($r['empleado_nombre']) . '</strong> ha firmado su responsiva.</p>
                <div style="background:#d1fae5; padding:15px; margin:20px 0; border-left:4px solid #10b981;">
                    <p style="margin:5px 0;"><strong>Tipo:</strong> ' . ucfirst(htmlspecialchars($r['tipo'])) . '</p>
                    <p style="margin:5px 0;"><strong>Equipo:</strong> ' . htmlspecialchars($r['marca'] . ' ' . $r['modelo']) . '</p>
                    <p style="margin:5px 0;"><strong>Serie:</strong> ' . htmlspecialchars($r['numero_serie']) . '</p>
                    <p style="margin:5px 0;"><strong>Sucursal:</strong> ' . htmlspecialchars($r['sucursal_nombre']) . '</p>
                </div>
                <p>El PDF firmado está adjunto a este correo.</p>
            </div>
            <div style="background:#1f2937; color:#9ca3af; padding:20px; text-align:center; border-radius:0 0 10px 10px; font-size:12px;">
                <p>&copy; ' . date('Y') . ' Grupo MB. Todos los derechos reservados.</p>
            </div>
        </div>';
    }
}
