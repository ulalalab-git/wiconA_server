<?php
namespace App\Benefit\Domain;

use App\TContainer;
use PDO;

/**
 *
 */
class Mail
{
    use TContainer;

    /**
     * [send description]
     * @param  array       $receiver [description]
     * @param  string|null $subject  [description]
     * @param  string|null $message  [description]
     * @param  string      $sender   [description]
     * @param  string      $email    [description]
     * @return [type]                [description]
     */
    public function send(array $receiver = [], string $subject = null, string $message = null, string $sender = 'ulalaLab', string $email = 'mail@ulalalab.com'): bool
    {
        $headers   = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'From: ' . $sender . '<' . $email . '>';
        $headers[] = 'Reply-To: ' . $email;
        // $headers[] = 'Cc: ' . $email;
        // $headers[] = 'Bcc: ' . $email;
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        // wordwrap
        $subject = '=?UTF-8?B?' . base64_encode($subject) . "?=\n";
        // mail('dearl@ulalalab.com', $subject, $message, $headers);

        return mail(implode(', ', $receiver), $subject, $message, implode(PHP_EOL, $headers));
    }
}
