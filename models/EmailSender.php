<?php
declare(strict_types=1);

final class EmailSender
{
    public static function send(string $to, string $subject, string $message): bool
    {
        if (!validateEmail($to)) {
            return false;
        }

        $headers = [
            'From: PureGlow <pureglowstaff@gmail.com>',
            'Reply-To: pureglowstaff@gmail.com',
            'Content-Type: text/plain; charset=UTF-8',
            'MIME-Version: 1.0',
        ];

        return self::sendRaw($to, $subject, $message, $headers);
    }

    public static function sendHtml(string $to, string $subject, string $message): bool
    {
        if (!validateEmail($to)) {
            return false;
        }

        $headers = [
            'From: PureGlow <pureglowstaff@gmail.com>',
            'Reply-To: pureglowstaff@gmail.com',
            'Content-Type: text/html; charset=UTF-8',
            'MIME-Version: 1.0',
        ];

        return self::sendRaw($to, $subject, $message, $headers);
    }

    private static function sendRaw(string $to, string $subject, string $message, array $headers): bool
    {
        $sendmailPath = 'C:\\xampp\\sendmail\\sendmail.exe';
        $rawMessage = implode("\r\n", array_merge(
            [
                'To: ' . $to,
                'Subject: ' . $subject,
            ],
            $headers,
            [
                '',
                $message,
            ]
        ));

        if (is_file($sendmailPath)) {
            $process = proc_open(
                '"' . $sendmailPath . '" -t',
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes
            );

            if (is_resource($process)) {
                fwrite($pipes[0], $rawMessage);
                fclose($pipes[0]);
                stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                $error = stream_get_contents($pipes[2]);
                fclose($pipes[2]);
                $exitCode = proc_close($process);

                if ($exitCode !== 0 && $error !== '') {
                    error_log('Email sendmail error: ' . trim($error));
                }

                return $exitCode === 0;
            }
        }

        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
}
