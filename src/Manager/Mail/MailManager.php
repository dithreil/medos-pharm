<?php

declare(strict_types=1);

namespace App\Manager\Mail;

use App\Exception\MailManagerException;
use Twig\Environment;

class MailManager
{
    public const NEW_PASSWORD_TEMPLATE = 'email/security/new_password.html.twig';
    public const CONFIRM_EMAIL_TEMPLATE = 'email/security/confirm_email.html.twig';
    public const NEW_ORDER_TEMPLATE = 'email/order/new_order_notification.html.twig';
    public const CANCEL_ORDER_TEMPLATE = 'email/order/cancel_order_notification.html.twig';
    public const ORDER_FOR_PAYMENT_TEMPLATE = 'email/order/order_for_payment_notification.html.twig';
    public const PROFILE_CHANGED_TEMPLATE = 'email/user/profile_changed.html.twig';

    /**
     * @var \Swift_Mailer
     */
    private \Swift_Mailer $swiftMailer;

    /**
     * @var string
     */
    private string $from;

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * @param Environment $twig
     * @param \Swift_Mailer $swiftMailer
     * @param string $from
     */
    public function __construct(
        Environment $twig,
        \Swift_Mailer $swiftMailer,
        string $from
    ) {
        $this->twig = $twig;
        $this->swiftMailer = $swiftMailer;
        $this->from = $from;
    }

    /**
     * @param string $twigTemplate
     * @param array $options
     * @param array $context
     * @return int|null
     * @throws MailManagerException
     */
    public function sendTwigMailHtml(
        string $twigTemplate,
        array $options,
        array $context
    ): ?int {
        if (!$twigTemplate || !array_key_exists('mail_to', $options)) {
            return null;
        }

        try {
            $tpl = $this->twig->load($twigTemplate);
            $message = new \Swift_Message();
            $message->setTo($options['mail_to'])
            ->setFrom($this->from)
            ->setSubject($tpl->renderBlock('subject', $context));

            if (array_key_exists('mail_bcc_to', $options)) {
                $message->setBcc($options['mail_bcc_to']);
            }

            if (array_key_exists('mail_attach', $options)) {
                if (is_array($options['mail_attach'])) {
                    foreach ($options['mail_attach'] as $attach) {
                        $message->attach(\Swift_Attachment::fromPath($attach));
                    }
                }
            }

            $message->setBody(html_entity_decode($tpl->renderBlock('body', $context)), 'text/html');
            return $this->swiftMailer->send($message);
        } catch (\Throwable $e) {
            throw new MailManagerException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
