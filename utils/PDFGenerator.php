<?php
/**
 * GMB Responsivas - PDF Generator
 *
 * Genera PDFs de responsivas firmadas
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/database.php';

use TCPDF;

class PDFGenerator {
    private $db;
    private $uploadDir;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->uploadDir = __DIR__ . '/../uploads/responsivas/';
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function generateResponsivaPDF($responsivaId) {
        $responsiva = $this->db->fetchOne("
            SELECT r.*, e.nombre as empleado_nombre, e.puesto as empleado_puesto, e.departamento as empleado_depto, e.numero_empleado,
                   eq.tipo, eq.marca, eq.modelo, eq.numero_serie,
                   s.nombre as sucursal_nombre, s.direccion as sucursal_direccion
            FROM responsivas r
            JOIN empleados e ON r.empleado_id = e.id
            JOIN equipos eq ON r.equipo_id = eq.id
            JOIN sucursales s ON r.sucursal_id = s.id
            WHERE r.id = :id
        ", ['id' => $responsivaId]);

        if (!$responsiva) {
            throw new Exception("Responsiva no encontrada");
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('GMB Responsivas');
        $pdf->SetAuthor('Grupo MB');
        $pdf->SetTitle('Responsiva - ' . $responsiva['empleado_nombre']);

        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        // Header
        $html = '<h1 style="text-align:center; color:#1e40af;">CARTA RESPONSIVA</h1>';
        $html .= '<h2 style="text-align:center;">DE EQUIPO DE CÓMPUTO / CELULAR</h2>';
        $html .= '<p style="text-align:center; color:#666;"><strong>' . strtoupper($responsiva['sucursal_nombre']) . '</strong> | ' . date('d/m/Y', strtotime($responsiva['fecha_emision'])) . '</p>';
        $html .= '<hr style="margin-top:20px;">';

        // Contenido
        $html .= '<p>En la ciudad de <strong>' . htmlspecialchars($responsiva['sucursal_nombre']) . '</strong>, siendo el día <strong>' . date('d', strtotime($responsiva['fecha_emision'])) . '</strong> del mes de <strong>' . strftime('%B', strtotime($responsiva['fecha_emision'])) . '</strong> del año <strong>' . date('Y', strtotime($responsiva['fecha_emision'])) . '</strong>, por la presente yo:</p>';

        $html .= '<div style="background:#f0f9ff; padding:15px; margin:15px 0; border-left:4px solid #3b82f6;">';
        $html .= '<p style="font-size:14px; margin:0;"><strong>' . htmlspecialchars($responsiva['empleado_nombre']) . '</strong></p>';
        $html .= '<p style="margin:5px 0;">Puesto: ' . htmlspecialchars($responsiva['empleado_puesto'] ?? 'N/A') . ' | Departamento: ' . htmlspecialchars($responsiva['empleado_depto'] ?? 'N/A') . '</p>';
        $html .= '<p style="margin:5px 0; font-size:12px; color:#666;">No. Empleado: ' . htmlspecialchars($responsiva['numero_empleado']) . '</p>';
        $html .= '</div>';

        $html .= '<p>Me comprometo a hacer buen uso del equipo que se me ha asignado en <strong>Grupo MB</strong>:</p>';

        $html .= '<div style="background:#fef3c7; padding:15px; margin:15px 0; border:1px solid #fbbf24;">';
        $html .= '<h3 style="margin:0 0 10px 0;">DETALLES DEL EQUIPO</h3>';
        $html .= '<p style="margin:5px 0;"><strong>Tipo:</strong> ' . ucfirst(htmlspecialchars($responsiva['tipo'])) . '</p>';
        $html .= '<p style="margin:5px 0;"><strong>Marca:</strong> ' . htmlspecialchars($responsiva['marca']) . '</p>';
        $html .= '<p style="margin:5px 0;"><strong>Modelo:</strong> ' . htmlspecialchars($responsiva['modelo']) . '</p>';
        $html .= '<p style="margin:5px 0;"><strong>Número de Serie:</strong> ' . htmlspecialchars($responsiva['numero_serie']) . '</p>';
        $html .= '</div>';

        // Términos
        $terminos = [
            'Hacer buen uso del equipo siguiendo políticas de la empresa',
            'Uso exclusivo para actividades laborales de Grupo MB',
            'Responsable del cuidado y mantenimiento adecuado',
            'Reportar inmediatamente robo, pérdida o daño al área de TI',
            'Devolver equipo al terminar relación laboral',
            'No instalar software no autorizado',
            'Mantener actualizado antivirus y seguridad',
            'No compartir credenciales de acceso'
        ];

        $html .= '<h3>CONDICIONES Y RESPONSABILIDADES:</h3>';
        $html .= '<ol style="padding-left:20px;">';
        foreach ($terminos as $i => $t) {
            $html .= '<li style="margin:5px 0;">' . $t . '</li>';
        }
        $html .= '</ol>';

        // Código verificación
        $html .= '<p style="margin-top:30px; font-size:11px; color:#666;">Código de verificación: <code>' . htmlspecialchars($responsiva['codigo_verificacion']) . '</code></p>';

        // Firma
        $html .= '<div style="margin-top:40px;">';
        $html .= '<div style="display:flex; gap:50px;">';
        $html .= '<div style="flex:1; text-align:center;">';
        $html .= '<p style="margin-bottom:30px; border-bottom:2px solid #333;">&nbsp;</p>';
        $html .= '<p>Firma del Empleado</p>';
        $html .= '</div>';
        $html .= '<div style="flex:1; text-align:center;">';
        $html .= '<p style="margin-bottom:30px; border-bottom:2px solid #333;">&nbsp;</p>';
        $html .= '<p>Firma del Responsable</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Fecha firma
        if ($responsiva['fecha_firma']) {
            $html .= '<p style="margin-top:20px; font-size:11px; color:#666;">Firmado digitalmente el ' . date('d/m/Y H:i', strtotime($responsiva['fecha_firma'])) . '</p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "responsiva_{$responsivaId}.pdf";
        $filepath = $this->uploadDir . $filename;
        $pdf->Output($filepath, 'F');

        $this->db->update('responsivas', ['pdf_ruta' => $filename], 'id = :id', ['id' => $responsivaId]);

        return $filepath;
    }
}
