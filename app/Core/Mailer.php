<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Classe Mailer
 * Envia e-mails usando a biblioteca PHPMailer e as configuracoes do arquivo .env.
 * Se o SMTP nao estiver configurado no .env, registra o e-mail em storage/logs/emails.log
 * para que o sistema funcione em ambiente local de estudo sem travar.
 */
class Mailer
{
    // enviar e-mail com phpmailer
    public static function enviar(string $paraEmail, string $paraNome, string $assunto, string $corpoHtml): bool
    {
        $host    = $_ENV['MAIL_HOST'] ?? '';
        $porta   = (int) ($_ENV['MAIL_PORT'] ?? 2525);
        $usuario = $_ENV['MAIL_USER'] ?? '';
        $senha   = $_ENV['MAIL_PASS'] ?? '';
        $cripto  = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
        $deEmail = $_ENV['MAIL_FROM_EMAIL'] ?? 'contato@iikliente.com.br';
        $deNome  = $_ENV['MAIL_FROM_NAME'] ?? 'iiKliente Gestão de Clientes';

        // Instancia o PHPMailer (carregado via Composer)
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        // Se o aluno ainda nao colocou usuario/senha SMTP no .env, salvamos no log local
        if (empty($usuario) || empty($senha)) {
            return self::registrarLogLocal($paraEmail, $paraNome, $assunto, $corpoHtml);
        }

        try {
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $usuario;
            $mail->Password   = $senha;
            $mail->SMTPSecure = $cripto;
            $mail->Port       = $porta;

            $mail->setFrom($deEmail, $deNome);
            $mail->addAddress($paraEmail, $paraNome);

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $corpoHtml;
            $mail->AltBody = strip_tags($corpoHtml);

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Em caso de falha de rede/SMTP local, guarda no log para nao perder a mensagem
            return self::registrarLogLocal($paraEmail, $paraNome, $assunto, $corpoHtml);
        }
    }

    // salvar copia do e-mail em arquivo de log quando estiver sem internet/smtp
    private static function registrarLogLocal(string $paraEmail, string $paraNome, string $assunto, string $corpoHtml): bool
    {
        $pastaLog = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($pastaLog)) {
            mkdir($pastaLog, 0775, true);
        }

        $linha = "[" . date('Y-m-d H:i:s') . "] PARA: {$paraNome} <{$paraEmail}> | ASSUNTO: {$assunto}\n";
        $linha .= "MENSAGEM: " . trim(strip_tags($corpoHtml)) . "\n";
        $linha .= str_repeat('-', 70) . "\n";

        file_put_contents($pastaLog . '/emails.log', $linha, FILE_APPEND);
        return true;
    }
}
