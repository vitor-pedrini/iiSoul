<?php

namespace App\Core;

/**
 * Classe UploadValidador
 * Valida de forma estrita o tipo binário real (MIME type com finfo_file)
 * e o tamanho dos arquivos enviados para impedir arquivos maliciosos.
 */
class UploadValidador
{
    private static array $mimeMap = [
        'pdf'  => ['application/pdf'],
        'png'  => ['image/png'],
        'jpg'  => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'zip'  => ['application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip', 'application/octet-stream'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
    ];

    /**
     * Valida arquivo enviado via $_FILES
     * Retorna array com ['valido' => bool, 'erro' => string, 'extensao' => string, 'tamanho_bytes' => int]
     */
    public static function validar(array $arquivo, float $maxMb = 5.0, array $extensoesPermitidas = ['pdf', 'png', 'jpg', 'jpeg', 'zip', 'docx']): array
    {
        if (empty($arquivo['name']) || empty($arquivo['tmp_name']) || $arquivo['error'] !== UPLOAD_ERR_OK) {
            return [
                'valido' => false,
                'erro'   => 'Nenhum arquivo enviado ou erro no upload.',
            ];
        }

        $tamanhoBytes = (int) $arquivo['size'];
        $tamanhoMb = $tamanhoBytes / 1024 / 1024;
        if ($tamanhoMb > $maxMb) {
            return [
                'valido' => false,
                'erro'   => "O arquivo excede o limite máximo permitido de {$maxMb} MB.",
            ];
        }

        $nomeOriginal = (string) $arquivo['name'];
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        if (!in_array($extensao, $extensoesPermitidas, true)) {
            return [
                'valido' => false,
                'erro'   => 'Extensão de arquivo não permitida. Use: ' . implode(', ', $extensoesPermitidas) . '.',
            ];
        }

        // --- Verificação Real de MIME Type binário com finfo_file ---
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $arquivo['tmp_name']);
        finfo_close($finfo);

        $mimesAceitos = self::$mimeMap[$extensao] ?? [];
        if (!in_array($mimeReal, $mimesAceitos, true)) {
            return [
                'valido' => false,
                'erro'   => "O conteúdo do arquivo ({$mimeReal}) não corresponde à extensão .{$extensao}.",
            ];
        }

        return [
            'valido'        => true,
            'erro'          => '',
            'extensao'      => $extensao,
            'mime'          => $mimeReal,
            'tamanho_bytes' => $tamanhoBytes,
            'tamanho_kb'    => max(1, (int) round($tamanhoBytes / 1024)),
        ];
    }
}
