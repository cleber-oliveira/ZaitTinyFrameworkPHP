<?php

namespace app\core\utils;

/**
 * Classe para envio de e-mails.
 */
class Mail {
    
    /** @var string O destinatário do e-mail. */
    private $to = "";
    
    /** @var string O assunto do e-mail. */
    private $subject = "";
    
    /** @var string O corpo do e-mail. */
    private $body = "";
    
    /** @var array Os cabeçalhos do e-mail. */
    private $headers = [];
    
    /** @var array Os anexos do e-mail. */
    private $attachments = [];
    
    /**
     * Construtor da classe Mail.
     *
     * @param string $to O destinatário do e-mail.
     * @param string $subject O assunto do e-mail.
     * @param string $body O corpo do e-mail.
     */
    public function __construct($to, $subject, $body) {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        
        // Configuração dos cabeçalhos padrão
        $this->headers[] = "MIME-Version: 1.0";
        $this->headers[] = "Content-type: text/html; charset=UTF-8";
    }
    
    /**
     * Adiciona um cabeçalho ao e-mail.
     *
     * @param string $header O cabeçalho a ser adicionado.
     * @return void
     */
    public function addHeader($header) {
        $this->headers[] = $header;
    }
    
    /**
     * Adiciona um anexo ao e-mail.
     *
     * @param string $file O caminho do arquivo a ser anexado.
     * @return void
     */
    public function addAttachment($file) {
        if (file_exists($file)) {
            $filename = basename($file);
            $file_size = filesize($file);
            $this->attachments[] = [
                'file' => $file,
                'filename' => $filename,
                'size' => $file_size,
                'type' => mime_content_type($file)
            ];
        }
    }
    
    /**
     * Adiciona um anexo em base64 ao e-mail.
     *
     * @param string $filename O nome do arquivo anexado.
     * @param string $data O conteúdo do arquivo em base64.
     * @param string $type O tipo de conteúdo do arquivo.
     * @return void
     */
    public function addAttachmentBase64($filename, $data, $type = 'application/octet-stream') {
        $content = base64_decode($data);
        $this->attachments[] = [
            'filename' => $filename,
            'content' => $content,
            'type' => $type,
            'disposition' => 'attachment',
        ];
    }
    
    /**
     * Envia o e-mail.
     *
     * @return bool Retorna true se o e-mail for enviado com sucesso, false caso contrário.
     */
    public function send() {
        $to = $this->to;
        $subject = $this->subject;
        $body = $this->body;
        
        $headers = implode("\r\n", $this->headers);
        
        $boundary = md5(time());
        
        if (count($this->attachments) > 0) {
            $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"" . $boundary . "\"";
            $body = "--" . $boundary . "\r\n" .
                "Content-Type: text/html; charset=\"utf-8\"\r\n" .
                "Content-Transfer-Encoding: base64\r\n\r\n" .
                chunk_split(base64_encode($body));
                
                foreach ($this->attachments as $attachment) {
                    $file = $attachment['file'];
                    $filename = $attachment['filename'];
                    $file_type = $attachment['type'];
                    
                    $body .= "\r\n\r\n--" . $boundary . "\r\n" .
                        "Content-Type: " . $file_type . "; name=\"" . $filename . "\"\r\n" .
                        "Content-Transfer-Encoding: base64\r\n" .
                        "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n" .
                        chunk_split(base64_encode(file_get_contents($file)));
                }
                
                $body .= "\r\n--" . $boundary . "--";
        }
        
        return mail($to, $subject, $body, $headers);
    }
}


/** Exemplo de Uso
 * 
 * @var \app\core\utils\Mail $mail
 * * Exemplo de uso:
 *
 * ```php
 * 
 * $mail = new Mail('destinatario@domínio.com', 'Assunto do e-mail', 'Corpo do e-mail');
 * 
 * Adiciona um cabeçalho personalizado
 * $mail->addHeader('X-Priority: 1');
 * 
 * Adiciona um anexo
 * $mail->addAttachment('arquivo.pdf');
 * // Envia o e-mail
 * if ($mail->send()) {
 *     echo 'E-mail enviado com sucesso!';
 *     } else {
 *         echo 'Falha ao enviar o e-mail.';
 * } ``` 
 */
?>